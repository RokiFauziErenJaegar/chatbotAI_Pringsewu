<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ChatbotService
{
    public function __construct(
        protected IKMQueryPlanner $planner,
        protected IKMDataService $dataService
    ) {}

    /**
     * ENTRY POINT (HARUS SAMA DENGAN CONTROLLER)
     */
    public function handle(array $payload): array
    {
        try {
            $question = $payload['question'] ?? '';
            $convId   = $payload['conversation_id'] ?? null;
            $ip       = $payload['ip'] ?? null;

            if (!$question) {
                return [
                    'reply' => 'Pertanyaan tidak boleh kosong.',
                    'meta' => [],
                ];
            }

            /* ===============================
             * PLAN QUERY
             * =============================== */
            $plan = $this->planner->plan($question);

            /* ===============================
             * AMBIL DATA DB
             * =============================== */
            $data = $this->dataService->runPlan($plan);

            /* ===============================
             * FACTS (ANTI HALUSINASI)
             * =============================== */
            $factsText = $this->formatFacts($data, $plan);

            $compact = $this->compactData($data);

            $cacheKey = 'chat_answer:' . sha1(json_encode([
                'q' => $question,
                'facts' => $factsText,
                'data' => $compact,
            ]));

            $reply = Cache::remember($cacheKey, now()->addSeconds(30), function () use ($question, $factsText, $compact) {
                return $this->askOpenAI($question, $factsText, $compact);
            });

            if (!$reply || mb_strlen(trim($reply)) < 3) {
                $reply = $factsText ?: 'Maaf, sistem belum dapat menyusun jawaban.';
            }

            Log::info('chat_request', [
                'conv_id' => $convId,
                'ip' => $ip,
                'question' => mb_substr($question, 0, 200),
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
        } catch (\Throwable $e) {
            Log::error('ChatbotService error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'reply' => 'Terjadi kesalahan internal saat memproses data.',
                'meta' => ['error' => true],
            ];
        }
    }

    /* ===============================
     * OPENAI CALL
     * =============================== */
    private function askOpenAI(string $question, string $facts, array $data): string
    {
        $apiKey = env('OPENAI_API_KEY');
        $model  = env('OPENAI_MODEL', 'gpt-4o-mini');

        if (!$apiKey) {
            return 'OPENAI_API_KEY belum diisi.';
        }

        $system = <<<SYS
Kamu adalah analis data resmi untuk Bupati Pringsewu.

ATURAN WAJIB:
1. Jawaban HARUS berdasarkan FACTS.
2. Jangan menambah data baru.
3. Jika diminta "jumlah", sebutkan angka.
4. Gunakan bahasa ringkas & eksekutif.
SYS;

        $messages = [
            ['role' => 'system', 'content' => $system],
            ['role' => 'user', 'content' =>
                "Pertanyaan:\n{$question}\n\nFACTS:\n{$facts}\n\nDATA (JSON):\n" .
                json_encode($data, JSON_UNESCAPED_UNICODE)
            ],
        ];

        $res = Http::timeout(20)
            ->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => 0.2,
            ]);

        if (!$res->ok()) {
            return $facts ?: 'Gagal menghubungi AI.';
        }

        return $res->json('choices.0.message.content', $facts);
    }

    /* ===============================
     * DATA COMPRESSION
     * =============================== */
    private function compactData(array $data): array
    {
        if (($data['kind'] ?? '') === 'aggregate') {
            return [
                'kind' => 'aggregate',
                'group_by' => $data['group_by'] ?? null,
                'metric' => $data['metric'] ?? null,
                'rows' => collect($data['rows'] ?? [])->take(10)->values(),
            ];
        }

        return [
            'kind' => 'list',
            'rows' => collect($data['rows'] ?? [])->take(10)->values(),
        ];
    }

    /* ===============================
     * FACTS FORMATTER
     * =============================== */
    private function formatFacts(array $data, array $plan): string
    {
        if (($data['kind'] ?? '') !== 'aggregate') {
            return 'Data tersedia namun bukan agregat.';
        }

        $rows = $data['rows'] ?? [];
        if (empty($rows)) {
            return 'Tidak ada data IKM yang ditemukan.';
        }

        $group = $data['group_by'] ?? 'kecamatan';
        $lines = [];

        foreach ($rows as $r) {
            $k = $r->{$group} ?? '(kosong)';
            $v = $r->value ?? 0;
            $lines[] = "- {$k}: {$v} IKM";
        }

        return "Jumlah IKM per {$group}:\n" . implode("\n", $lines);
    }
}
