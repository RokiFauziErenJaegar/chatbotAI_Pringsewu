<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class IKMQueryPlanner
{
    private string $table = 'ikm_koperindag';

    public function __construct(private TableSchemaService $schema) {}

    /**
     * Planner baru:
     * - Ambil daftar kolom dari DB (schema-aware)
     * - Buat plan JSON via LLM (kalau gagal -> fallback rule-based)
     * - Validasi plan supaya aman
     */
    public function plan(string $message): array
    {
        $message = trim($message);

        $allowedCols = $this->schema->getAllowedColumns($this->table);
        $numericCols = $this->schema->getNumericColumns($this->table);

        // 1) Coba LLM planner agar bisa memahami pertanyaan bebas
        $plan = $this->llmPlan($message, $allowedCols, $numericCols);

        // 2) Kalau gagal / hasil tidak valid -> fallback rule-based sederhana
        if (!$plan) {
            $plan = $this->fallbackPlan($message, $allowedCols, $numericCols);
        }

        // 3) Validasi final (wajib)
        return $this->validateAndNormalizePlan($plan, $allowedCols, $numericCols);
    }

    /**
     * LLM planner: output harus JSON plan saja.
     */
    private function llmPlan(string $message, array $allowedCols, array $numericCols): ?array
    {
        $apiKey = env('OPENAI_API_KEY');
        $model  = env('OPENAI_MODEL', 'gpt-4o-mini');

        if (!$apiKey) return null;

        $system = "Kamu adalah query planner untuk data tabel ikm_koperindag.\n"
            . "Tugas: ubah pertanyaan menjadi JSON plan.\n"
            . "ATURAN KETAT:\n"
            . "- HANYA boleh pakai kolom yang tersedia.\n"
            . "- Jangan buat SQL.\n"
            . "- Output HARUS JSON valid tanpa teks lain.\n\n"
            . "Skema JSON plan:\n"
            . "{\n"
            . "  \"type\": \"aggregate\"|\"list\"|\"compare\",\n"
            . "  \"group_by\": \"<kolom>\" (untuk aggregate),\n"
            . "  \"metric\": \"count\"|\"sum\"|\"avg\"|\"min\"|\"max\",\n"
            . "  \"metric_col\": \"<kolom numeric>\" (wajib jika metric bukan count),\n"
            . "  \"filters\": [ {\"col\":\"<kolom>\",\"op\":\"=|!=|like|>|>=|<|<=\",\"val\":\"...\"} ],\n"
            . "  \"select\": [\"col1\",\"col2\", ...] (untuk list, optional),\n"
            . "  \"limit\": 1..50,\n"
            . "  \"compare_col\": \"<kolom>\" (untuk compare),\n"
            . "  \"compare_values\": [\"A\",\"B\"]\n"
            . "}\n";

        $user = "Daftar kolom yang boleh: " . implode(', ', $allowedCols) . "\n"
            . "Kolom numeric: " . implode(', ', $numericCols) . "\n"
            . "Pertanyaan: {$message}\n\n"
            . "Buat plan JSON yang paling tepat. Jika user meminta 'paling banyak/tertinggi', set limit=10.\n";

        $res = Http::timeout(20)
            ->withToken($apiKey)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
                'temperature' => 0,
            ]);

        if (!$res->ok()) return null;

        $text = $res->json('choices.0.message.content');
        if (!is_string($text) || trim($text) === '') return null;

        $json = json_decode($text, true);
        if (!is_array($json)) return null;

        return $json;
    }

    /**
     * Fallback sederhana jika LLM planner gagal.
     */
    private function fallbackPlan(string $message, array $allowedCols, array $numericCols): array
    {
        $q = mb_strtolower($message);

        // default aggregate count per kecamatan (kalau ada)
        $groupDefault = in_array('kecamatan', $allowedCols, true) ? 'kecamatan' : $allowedCols[0];

        $plan = [
            'type' => 'aggregate',
            'group_by' => $groupDefault,
            'metric' => 'count',
            'metric_col' => null,
            'filters' => [],
            'limit' => 10,
        ];

        // jika ada kata "daftar/tampilkan"
        if (str_contains($q, 'daftar') || str_contains($q, 'tampilkan') || str_contains($q, 'list')) {
            $plan['type'] = 'list';
            $plan['select'] = array_values(array_slice($allowedCols, 0, 6));
            $plan['limit'] = 20;
        }

        // jika ada "tenaga kerja"
        if (str_contains($q, 'tenaga kerja') && in_array('jumlah_tenaga_kerja', $allowedCols, true)) {
            $plan['type'] = 'aggregate';
            $plan['metric'] = 'sum';
            $plan['metric_col'] = 'jumlah_tenaga_kerja';
            $plan['group_by'] = $groupDefault;
        }

        // top N
        if (preg_match('/top\s+(\d+)/i', $message, $m)) {
            $plan['limit'] = max(1, min(50, (int)$m[1]));
        }

        return $plan;
    }

    /**
     * Validasi dan normalisasi plan agar aman.
     */
    private function validateAndNormalizePlan(array $plan, array $allowedCols, array $numericCols): array
    {
        $type = $plan['type'] ?? 'aggregate';
        if (!in_array($type, ['aggregate','list','compare'], true)) $type = 'aggregate';

        $limit = (int) ($plan['limit'] ?? 10);
        $limit = max(1, min(50, $limit));

        $filters = $plan['filters'] ?? [];
        if (!is_array($filters)) $filters = [];

        $allowedOps = ['=','!=','like','>','>=','<','<='];

        $cleanFilters = [];
        foreach ($filters as $f) {
            if (!is_array($f)) continue;
            $col = $f['col'] ?? null;
            $op  = $f['op'] ?? '=';
            $val = $f['val'] ?? null;

            if (!is_string($col) || !in_array($col, $allowedCols, true)) continue;
            if (!in_array($op, $allowedOps, true)) $op = '=';

            // batasi panjang value agar anti abuse
            if (is_string($val) && mb_strlen($val) > 200) {
                $val = mb_substr($val, 0, 200);
            }

            $cleanFilters[] = ['col' => $col, 'op' => $op, 'val' => $val];
        }

        $out = [
            'type' => $type,
            'filters' => $cleanFilters,
            'limit' => $limit,
        ];

        if ($type === 'list') {
            $select = $plan['select'] ?? [];
            if (!is_array($select) || count($select) === 0) {
                // default select ringkas
                $select = array_values(array_slice($allowedCols, 0, 6));
            } else {
                $select = array_values(array_filter($select, fn($c) => is_string($c) && in_array($c, $allowedCols, true)));
                $select = array_slice($select, 0, 12);
                if (count($select) === 0) $select = array_values(array_slice($allowedCols, 0, 6));
            }
            $out['select'] = $select;
            return $out;
        }

        if ($type === 'compare') {
            $compareCol = $plan['compare_col'] ?? 'kecamatan';
            if (!is_string($compareCol) || !in_array($compareCol, $allowedCols, true)) {
                $compareCol = in_array('kecamatan', $allowedCols, true) ? 'kecamatan' : $allowedCols[0];
            }
            $vals = $plan['compare_values'] ?? [];
            if (!is_array($vals)) $vals = [];
            $vals = array_values(array_filter(array_map(fn($v) => trim((string)$v), $vals), fn($v) => mb_strlen($v) >= 1));
            $vals = array_slice($vals, 0, 5);

            $out['compare_col'] = $compareCol;
            $out['compare_values'] = $vals;

            // metric
            $metric = $plan['metric'] ?? 'count';
            $metric = is_string($metric) ? strtolower($metric) : 'count';
            if (!in_array($metric, ['count','sum','avg','min','max'], true)) $metric = 'count';
            $out['metric'] = $metric;

            if ($metric !== 'count') {
                $metricCol = $plan['metric_col'] ?? null;
                if (!is_string($metricCol) || !in_array($metricCol, $numericCols, true)) {
                    // fallback numeric kolom pertama
                    $metricCol = $numericCols[0] ?? null;
                }
                $out['metric_col'] = $metricCol;
            } else {
                $out['metric_col'] = null;
            }
            return $out;
        }

        // aggregate
        $groupBy = $plan['group_by'] ?? (in_array('kecamatan', $allowedCols, true) ? 'kecamatan' : $allowedCols[0]);
        if (!is_string($groupBy) || !in_array($groupBy, $allowedCols, true)) {
            $groupBy = in_array('kecamatan', $allowedCols, true) ? 'kecamatan' : $allowedCols[0];
        }

        $metric = $plan['metric'] ?? 'count';
        $metric = is_string($metric) ? strtolower($metric) : 'count';
        if (!in_array($metric, ['count','sum','avg','min','max'], true)) $metric = 'count';

        $out['group_by'] = $groupBy;
        $out['metric'] = $metric;

        if ($metric !== 'count') {
            $metricCol = $plan['metric_col'] ?? null;
            if (!is_string($metricCol) || !in_array($metricCol, $numericCols, true)) {
                $metricCol = $numericCols[0] ?? null;
            }
            $out['metric_col'] = $metricCol;
        } else {
            $out['metric_col'] = null;
        }

        return $out;
    }
}
