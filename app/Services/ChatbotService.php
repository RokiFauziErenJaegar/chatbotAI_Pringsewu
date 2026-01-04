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
        // 1) Proteksi tambahan: block IP sementara kalau terdeteksi abusive
        if ($this->isTemporarilyBlocked($ip)) {
            return [
                'reply' => "Akses dari IP ini sementara dibatasi karena terlalu banyak permintaan. Coba lagi beberapa menit lagi.",
                'meta' => ['blocked' => true],
            ];
        }

        // 2) Buat plan query dari pertanyaan
        $plan = $this->planner->plan($message);

        // 3) Jalankan query aman (hanya tabel ikm_koperindag)
        $data = $this->dataService->runPlan($plan);

        // 4) Siapkan ringkasan (batasi ukuran)
        $payload = $this->buildCompactPayload($plan, $data);

        // 5) Panggil OpenAI (dengan caching agar hemat & tahan spam)
        $cacheKey = 'chat_answer:' . sha1(json_encode($payload));
        $reply = Cache::remember($cacheKey, now()->addMinutes(2), function () use ($message, $payload) {
            return $this->askOpenAI($message, $payload);
        });

        // 6) Logging audit ringan
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

        if (!$apiKey) {
            return 'OPENAI_API_KEY belum diisi di .env';
        }

        $system = "Kamu adalah analis data untuk Bupati Pringsewu. "
            . "Jawab dalam Bahasa Indonesia yang jelas, ringkas, berbasis data. "
            . "Kamu HANYA boleh menggunakan data yang diberikan dalam JSON payload (hasil query dari tabel ikm_koperindag). "
            . "Jika data tidak cukup, katakan apa yang kurang dan sarankan pertanyaan lanjutan. "
            . "Jangan membuat angka atau fakta baru.";

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' => "Pertanyaan:\n{$userMessage}\n\nData (JSON):\n" . json_encode($payload, JSON_UNESCAPED_UNICODE)],
        ];

        $res = Http::timeout(25)
            ->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2,
            ]);

        if (!$res->ok()) {
            // kalau sering error, block sementara ip di layer lain
            return "Gagal memproses AI (HTTP {$res->status()}). Coba lagi.";
        }

        $json = $res->json();
        $text = $json['choices'][0]['message']['content'] ?? null;

        return $text ?: 'AI tidak mengembalikan jawaban.';
    }

    private function buildCompactPayload(array $plan, array $data): array
    {
        // Pastikan ukuran tidak kebesaran (untuk token & keamanan)
        $out = [
            'plan' => $plan,
            'data' => [],
        ];

        if (($data['kind'] ?? '') === 'aggregate') {
            $out['data'] = [
                'kind' => 'aggregate',
                'group_by' => $data['group_by'] ?? null,
                'metric' => $data['metric'] ?? null,
                'rows' => collect($data['rows'] ?? [])->take(50)->values(),
            ];
        } elseif (($data['kind'] ?? '') === 'compare') {
            $out['data'] = [
                'kind' => 'compare',
                'compare_col' => $data['compare_col'] ?? null,
                'metric' => $data['metric'] ?? null,
                'result' => $data['result'] ?? [],
            ];
        } else {
            $out['data'] = [
                'kind' => 'list',
                'rows' => collect($data['rows'] ?? [])->take(20)->values(),
            ];
        }

        return $out;
    }

    private function isTemporarilyBlocked(string $ip): bool
    {
        // jika IP sudah diblokir, tolak
        if (Cache::get("block_ip:{$ip}") === true) {
            return true;
        }

        // hit request error/abuse count (contoh sederhana)
        // (kamu bisa tambah logic: jika kena rate-limit berkali-kali -> block)
        return false;
    }
}
