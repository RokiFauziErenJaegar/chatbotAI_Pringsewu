<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class IKMDataService
{
    private array $allowedColumns = [
        'id',
        'nama_perusahaan',
        'nama_pemilik',
        'alamat',
        'kecamatan',
        'telepon',
        'jenis_produk',
        'kapasitas_produksi',
        'jumlah_tenaga_kerja',
        'perijinan',
        'produk_utama',
        'created_at',
        'updated_at',
    ];

    public function runPlan(array $plan): array
    {
        $type = $plan['type'] ?? 'aggregate';
        $filters = $plan['filters'] ?? [];
        $limit = max(1, min(50, (int)($plan['limit'] ?? 10)));

        $q = DB::table('ikm_koperindag');

        // filters aman
        foreach ($filters as $f) {
            $col = $f['col'] ?? '';
            $op  = $f['op'] ?? '=';
            $val = $f['val'] ?? null;

            if (!in_array($col, $this->allowedColumns, true)) continue;

            $allowedOps = ['=', '!=', 'like'];
            if (!in_array($op, $allowedOps, true)) $op = '=';

            if ($op === 'like' && is_string($val)) {
                $q->where($col, 'like', '%' . $val . '%');
            } else {
                $q->where($col, $op, $val);
            }
        }

        if ($type === 'list') {
            $rows = $q->select([
                    'nama_perusahaan',
                    'kecamatan',
                    'jenis_produk',
                    'jumlah_tenaga_kerja',
                    'produk_utama',
                ])
                ->limit($limit)
                ->get();

            return ['kind' => 'list', 'rows' => $rows];
        }

        if ($type === 'compare') {
            $compareCol = $plan['compare_col'] ?? 'kecamatan';
            $vals = $plan['compare_values'] ?? [];
            $metric = $plan['metric'] ?? 'count';

            if (!in_array($compareCol, $this->allowedColumns, true)) $compareCol = 'kecamatan';

            $vals = array_values(array_filter(array_map(fn($v) => trim((string)$v), $vals), fn($x) => mb_strlen($x) >= 2));

            $out = [];
            foreach (array_slice($vals, 0, 5) as $v) {
                $qq = clone $q;
                $needle = mb_strtolower(trim($v));

                // 1) exact match: TRIM+LOWER
                $qqExact = clone $qq;
                $qqExact->whereRaw("LOWER(TRIM($compareCol)) = ?", [$needle]);
                $valueExact = $this->computeMetric($qqExact, $metric);

                // 2) fallback LIKE jika exact 0
                if ($valueExact === 0) {
                    $qqLike = clone $qq;
                    $qqLike->whereRaw("LOWER(TRIM($compareCol)) LIKE ?", ['%' . $needle . '%']);
                    $out[$v] = $this->computeMetric($qqLike, $metric);
                } else {
                    $out[$v] = $valueExact;
                }
            }

            return [
                'kind' => 'compare',
                'compare_col' => $compareCol,
                'metric' => $metric,
                'result' => $out,
            ];
        }

        // aggregate
        $groupBy = $plan['group_by'] ?? 'kecamatan';
        if (!in_array($groupBy, $this->allowedColumns, true)) $groupBy = 'kecamatan';

        $metric = $plan['metric'] ?? 'count';

        // penting: abaikan nilai null/kosong agar "Top 10 jenis produk" tidak jadi aneh
        $qAgg = clone $q;
        $qAgg->whereNotNull($groupBy)->whereRaw("TRIM($groupBy) <> ''");

        $select = [$groupBy];

        // SUM tenaga kerja lebih robust:
        // - buang spasi
        // - CAST UNSIGNED
        // catatan: ini aman untuk MySQL umum
        if ($metric === 'sum_tenaga_kerja') {
            $select[] = DB::raw("COALESCE(SUM(CAST(TRIM(jumlah_tenaga_kerja) AS UNSIGNED)),0) as value");
        } else {
            $select[] = DB::raw("COUNT(*) as value");
        }

        $rows = $qAgg->select($select)
            ->groupBy($groupBy)
            ->orderByDesc('value')
            ->limit($limit)
            ->get();

        return [
            'kind' => 'aggregate',
            'group_by' => $groupBy,
            'metric' => $metric,
            'rows' => $rows,
        ];
    }

    private function computeMetric($query, string $metric): int
    {
        if ($metric === 'sum_tenaga_kerja') {
            $row = $query->select(DB::raw("COALESCE(SUM(CAST(TRIM(jumlah_tenaga_kerja) AS UNSIGNED)),0) as s"))->first();
            return (int)($row->s ?? 0);
        }
        return (int)$query->count();
    }
}
