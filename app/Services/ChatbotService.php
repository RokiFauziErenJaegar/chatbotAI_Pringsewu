<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotService
{
    public function __construct(
        private IKMQueryPlanner $planner,
        private IKMDataService $dataService
    ) {}

    public function handle(string $message, string $convId, string $ip): array
    {
        $plan = $this->planner->plan($message);
        $data = $this->dataService->runPlan($plan);

        // facts deterministik (agar jawaban konsisten)
        $factsText = $this->formatFacts($data, $plan);

        $payload = [
            'plan' => $plan,
            'facts' => $factsText,   // <--- kunci utama
            'data' => $this->compactData($data),
        ];

        // cache jawaban singkat (boleh, tapi jangan terlalu lama)
        $cacheKey = 'chat_answer:' . sha1(json_encode($payload));
        $reply = Cache::remember($cacheKey, now()->addSeconds(30), function () use ($message, $payload) {
            return $this->askOpenAI($message, $payload);
        });

        // jika OpenAI error / jawaban kosong, fallback ke facts
        if (!$reply || mb_strlen(trim($reply)) < 3) {
            $reply = $factsText ?: 'Maaf, sistem belum bisa memproses pertanyaan tersebut.';
        }

        Log::info('chat_request', [
            'conv_id' => $convId,
            'ip' => $ip,
            'message' => mb_substr($message, 0, 300),
            'plan' => $plan,
            'data_kind' => $data['kind'] ?? null,
        ]);

        return [
            'reply' => $reply,
            'meta' => [
                'plan' => $plan,
                'data_kind' => $data['kind'] ?? null,
            ],
        ];
    }

    private function askOpenAI(string $userMessage, array $payload): string
    {
        $apiKey = env('OPENAI_API_KEY');
        $model  = env('OPENAI_MODEL', 'gpt-4o-mini');

        if (!$apiKey) return 'OPENAI_API_KEY belum diisi di .env';

        $system = "Kamu adalah analis data untuk Bupati Pringsewu.\n"
            . "ATURAN WAJIB:\n"
            . "1) Jawaban harus berdasarkan FACTS yang diberikan.\n"
            . "2) Jangan bilang 'data kosong' jika facts berisi angka/daftar.\n"
            . "3) Jika pertanyaan minta TOP/terbanyak, sebutkan peringkat 1 dan ringkasan top 5.\n"
            . "4) Jika pertanyaan tidak cocok dengan data tabel ikm_koperindag, jelaskan keterbatasannya.";

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => "Pertanyaan:\n{$userMessage}\n\nFACTS:\n{$payload['facts']}\n\nData (JSON ringkas):\n" . json_encode($payload['data'], JSON_UNESCAPED_UNICODE)],
        ];

        $res = Http::timeout(25)
            ->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2,
            ]);

        if (!$res->ok()) {
            return $payload['facts'] ?: "Gagal memproses AI (HTTP {$res->status()}).";
        }

        $json = $res->json();
        return $json['choices'][0]['message']['content'] ?? ($payload['facts'] ?: 'AI tidak mengembalikan jawaban.');
    }

    private function compactData(array $data): array
    {
        if (($data['kind'] ?? '') === 'aggregate') {
            return [
                'kind' => 'aggregate',
                'group_by' => $data['group_by'] ?? null,
                'metric' => $data['metric'] ?? null,
                'rows' => collect($data['rows'] ?? [])->take(15)->values(),
            ];
        }

        if (($data['kind'] ?? '') === 'compare') {
            return [
                'kind' => 'compare',
                'compare_col' => $data['compare_col'] ?? null,
                'metric' => $data['metric'] ?? null,
                'result' => $data['result'] ?? [],
            ];
        }

        return [
            'kind' => 'list',
            'rows' => collect($data['rows'] ?? [])->take(15)->values(),
        ];
    }

    private function formatFacts(array $data, array $plan): string
    {
        $kind = $data['kind'] ?? '';
        $metric = $data['metric'] ?? ($plan['metric'] ?? 'count');

        if ($kind === 'compare') {
            $res = $data['result'] ?? [];
            if (!$res || count($res) === 0) {
                return "Tidak ada pasangan kecamatan yang berhasil dibandingkan (compare_values kosong atau tidak terbaca).";
            }
            $lines = [];
            foreach ($res as $k => $v) {
                $lines[] = "- {$k}: {$v}";
            }
            return "Hasil perbandingan (" . ($metric === 'sum_tenaga_kerja' ? 'Total Tenaga Kerja' : 'Jumlah IKM') . "):\n" . implode("\n", $lines);
        }

        if ($kind === 'aggregate') {
            $rows = $data['rows'] ?? [];
            if (!$rows || count($rows) === 0) {
                return "Query agregat tidak menghasilkan baris (kemungkinan kolom banyak kosong, atau data belum ada).";
            }

            $label = ($metric === 'sum_tenaga_kerja') ? 'Total Tenaga Kerja' : 'Jumlah IKM';
            $groupBy = $data['group_by'] ?? ($plan['group_by'] ?? 'kecamatan');

            $lines = [];
            $i = 1;
            foreach ($rows as $r) {
                $k = $r->{$groupBy} ?? '(kosong)';
                $v = $r->value ?? 0;
                $lines[] = "{$i}. {$k}: {$v}";
                $i++;
                if ($i > 10) break;
            }

            return "Agregat {$label} per {$groupBy} (Top " . min(10, count($rows)) . "):\n" . implode("\n", $lines);
        }

        if ($kind === 'list') {
            $rows = $data['rows'] ?? [];
            if (!$rows || count($rows) === 0) return "Tidak ada data yang dapat ditampilkan untuk permintaan list.";
            $lines = [];
            foreach ($rows as $r) {
                $lines[] = "- {$r->nama_perusahaan} | {$r->kecamatan} | {$r->jenis_produk} | tenaga_kerja: {$r->jumlah_tenaga_kerja}";
            }
            return "Daftar data (sample):\n" . implode("\n", array_slice($lines, 0, 10));
        }

        return "";
    }
}
