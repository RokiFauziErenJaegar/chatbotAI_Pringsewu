<?php

namespace App\Services;

class IKMQueryPlanner
{
    public function plan(string $message): array
    {
        $q = mb_strtolower(trim($message));

        // default
        $plan = [
            'type' => 'aggregate',
            'group_by' => 'kecamatan',
            'metric' => 'count',
            'filters' => [],
            'limit' => 10,
        ];

        // pertanyaan tentang tenaga kerja
        if (str_contains($q, 'tenaga kerja') || str_contains($q, 'pekerja')) {
            $plan['metric'] = 'sum_tenaga_kerja';
        }

        // top N
        if (preg_match('/top\s+(\d+)/', $q, $m)) {
            $plan['limit'] = max(1, min(50, (int)$m[1]));
        }

        // grouping dimensi
        if (str_contains($q, 'per kecamatan') || str_contains($q, 'tiap kecamatan')) {
            $plan['group_by'] = 'kecamatan';
        } elseif (str_contains($q, 'per jenis produk') || str_contains($q, 'tiap jenis produk')) {
            $plan['group_by'] = 'jenis_produk';
        } elseif (str_contains($q, 'per perijinan') || str_contains($q, 'perizinan')) {
            $plan['group_by'] = 'perijinan';
        } elseif (str_contains($q, 'produk utama')) {
            $plan['group_by'] = 'produk_utama';
        }

        // list perusahaan
        if (str_contains($q, 'daftar') || str_contains($q, 'list') || str_contains($q, 'tampilkan perusahaan')) {
            $plan['type'] = 'list';
            $plan['limit'] = 20;
        }

        // compare (bandingkan A vs B)
        if (str_contains($q, 'bandingkan') || str_contains($q, 'vs')) {
            $plan = [
                'type' => 'compare',
                'compare_col' => 'kecamatan',
                'compare_values' => $this->extractCompareValues($message),
                'metric' => (str_contains($q, 'tenaga kerja') ? 'sum_tenaga_kerja' : 'count'),
                'filters' => [],
            ];
        }

        // filter kecamatan: "di kecamatan X"
        if (preg_match('/kecamatan\s+([a-zA-Z\s]+)/i', $message, $m)) {
            $val = trim($m[1]);
            if (mb_strlen($val) >= 3) {
                // kalau mode compare, jangan timpa
                if (($plan['type'] ?? '') !== 'compare') {
                    $plan['filters'][] = ['col' => 'kecamatan', 'op' => 'like', 'val' => $val];
                }
            }
        }

        // filter jenis produk: "jenis produk ..."
        if (preg_match('/jenis\s+produk\s+([a-zA-Z0-9\s]+)/i', $message, $m)) {
            $val = trim($m[1]);
            if (mb_strlen($val) >= 3) {
                if (($plan['type'] ?? '') !== 'compare') {
                    $plan['filters'][] = ['col' => 'jenis_produk', 'op' => 'like', 'val' => $val];
                }
            }
        }

        return $plan;
    }

    private function extractCompareValues(string $message): array
    {
        // sangat sederhana: ambil teks setelah "kecamatan" lalu split dengan "vs" atau "dan"
        $lower = mb_strtolower($message);

        if (preg_match('/kecamatan\s+(.+)/i', $message, $m)) {
            $tail = trim($m[1]);
            $parts = preg_split('/\s+(vs|dan|&)\s+/i', $tail);
            $parts = array_map('trim', $parts);
            $parts = array_filter($parts, fn($x) => mb_strlen($x) >= 2);
            return array_values(array_slice($parts, 0, 5));
        }

        // fallback: kalau ada "A vs B"
        if (preg_match('/(.+)\s+vs\s+(.+)/i', $message, $m)) {
            return [trim($m[1]), trim($m[2])];
        }

        return [];
    }
}
