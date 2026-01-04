<?php

namespace App\Services;

class IKMQueryPlanner
{
    public function plan(string $message): array
    {
        $msg = trim($message);
        $q = mb_strtolower($msg);

        // default: agregat per kecamatan (count)
        $plan = [
            'type' => 'aggregate',          // aggregate | list | compare
            'group_by' => 'kecamatan',      // kecamatan | jenis_produk | nama_perusahaan | perijinan | produk_utama
            'metric' => 'count',            // count | sum_tenaga_kerja
            'filters' => [],
            'limit' => 10,
            'answer_mode' => 'auto',        // auto | top1 | list
        ];

        // --------- LIMIT / TOP N ---------
        if (preg_match('/top\s+(\d+)/i', $msg, $m)) {
            $plan['limit'] = max(1, min(50, (int) $m[1]));
        } elseif (str_contains($q, 'top')) {
            $plan['limit'] = 10;
        }

        // --------- METRIC: tenaga kerja ---------
        $isTenagaKerja = (str_contains($q, 'tenaga kerja') || str_contains($q, 'pekerja'));
        if ($isTenagaKerja) {
            $plan['metric'] = 'sum_tenaga_kerja';
        }

        // --------- ENTITY / DIMENSI (GROUP BY) ---------
        // penting: kalau user menyebut "perusahaan" berarti group_by nama_perusahaan
        if (str_contains($q, 'perusahaan') || str_contains($q, 'nama_perusahaan')) {
            $plan['group_by'] = 'nama_perusahaan';
        } elseif (str_contains($q, 'jenis produk') || str_contains($q, 'jenis_produk')) {
            $plan['group_by'] = 'jenis_produk';
        } elseif (str_contains($q, 'perijinan') || str_contains($q, 'perizinan')) {
            $plan['group_by'] = 'perijinan';
        } elseif (str_contains($q, 'produk utama')) {
            $plan['group_by'] = 'produk_utama';
        } else {
            // default kecamatan
            $plan['group_by'] = 'kecamatan';
        }

        // --------- Kata kunci "paling banyak/terbanyak/tertinggi" ---------
        if (str_contains($q, 'paling banyak') || str_contains($q, 'terbanyak') || str_contains($q, 'tertinggi')) {
            // biasanya user ingin top 1
            if (!str_contains($q, 'top')) {
                $plan['answer_mode'] = 'top1';
                $plan['limit'] = 10; // ambil beberapa untuk konteks
            }
        }

        // --------- LIST MODE ---------
        if (str_contains($q, 'daftar') || str_contains($q, 'list') || str_contains($q, 'tampilkan')) {
            // kalau user minta list perusahaan
            $plan['type'] = 'list';
            $plan['limit'] = 20;
            $plan['answer_mode'] = 'list';
        }

        // --------- COMPARE MODE (bandingkan / vs) ---------
        if (str_contains($q, 'bandingkan') || preg_match('/\s+vs\s+/i', $msg)) {
            $vals = $this->extractCompareValues($msg);

            return [
                'type' => 'compare',
                'compare_col' => 'kecamatan',
                'compare_values' => $vals,
                'metric' => $isTenagaKerja ? 'sum_tenaga_kerja' : 'count',
                'filters' => [],
                'answer_mode' => 'auto',
            ];
        }

        // --------- FILTER KECAMATAN (kecamatan X / kec. X) ---------
        if (preg_match('/\b(kecamatan|kec\.?)\s+([a-zA-Z\s]+)/i', $msg, $m)) {
            $val = $this->cleanName($m[2]);
            if (mb_strlen($val) >= 2) {
                $plan['filters'][] = ['col' => 'kecamatan', 'op' => 'like', 'val' => $val];
            }
        }

        // --------- FILTER JENIS PRODUK ---------
        if (preg_match('/jenis\s+produk\s+([a-zA-Z0-9\s]+)/i', $msg, $m)) {
            $val = $this->cleanName($m[1]);
            if (mb_strlen($val) >= 2) {
                $plan['filters'][] = ['col' => 'jenis_produk', 'op' => 'like', 'val' => $val];
            }
        }

        return $plan;
    }

    private function extractCompareValues(string $message): array
    {
        $msg = trim($message);

        // Pola: "... Kecamatan A ... vs ... Kecamatan B ..."
        if (preg_match('/\b(kecamatan|kec\.?)\s+(.+?)\s+vs\s+\b(kecamatan|kec\.?)\s+(.+?)(\?|$)/i', $msg, $m)) {
            $a = $this->cleanName($m[2]);
            $b = $this->cleanName($m[4]);
            return array_values(array_filter([$a, $b], fn($x) => mb_strlen($x) >= 2));
        }

        // Pola: "... Kecamatan A vs B ..."
        if (preg_match('/\b(kecamatan|kec\.?)\s+(.+?)\s+vs\s+(.+?)(\?|$)/i', $msg, $m)) {
            $a = $this->cleanName($m[2]);
            $b = $this->cleanName($m[3]);
            return array_values(array_filter([$a, $b], fn($x) => mb_strlen($x) >= 2));
        }

        return [];
    }

    private function cleanName(string $text): string
    {
        $t = trim($text);
        $t = preg_replace('/[^a-zA-Z0-9\s]/', ' ', $t);
        $t = preg_replace('/\s+/', ' ', $t);
        return trim($t);
    }
}
