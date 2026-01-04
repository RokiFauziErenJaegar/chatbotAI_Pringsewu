<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class IKMDataService
{
    // whitelist kolom untuk mencegah query aneh
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
        // plan minimal:
        // [
        //   "type" => "aggregate"|"list"|"compare",
        //   "group_by" => "kecamatan"|...,
        //   "metric" => "count"|"sum_tenaga_kerja",
        //   "filters" => [ ["col"=>"kecamatan","op"=>"=","val"=>"..."], ... ],
        //   "limit" => 10
        // ]

        $type = $plan['type'] ?? 'aggregate';
        $filters = $plan['filters'] ?? [];
        $limit = (int) ($plan['limit'] ?? 10);
        $limit = max(1, min(50, $limit));

        $q = DB::table('ikm_koperindag');

        // apply filters aman
        foreach ($filters as $f) {
            $col = $f['col'] ?? '';
            $op  = $f['op'] ?? '=';
            $val = $f['val'] ?? null;

            if (!in_array($col, $this->allowedColumns, true)) {
                continue;
            }

            // operator aman
            $allowedOps = ['=', '!=', 'like'];
            if (!in_array($op, $allowedOps, true)) {
                $op = '=';
            }

            if ($op === 'like' && is_string($val)) {
                $q->where($col, 'like', '%' . $val . '%');
            } else {
                $q->where($col, $op, $val);
            }
        }

        if ($type === 'list') {
            // listing data ringkas
            $rows = $q->select([
                    'nama_perusahaan',
                    'kecamatan',
                    'jenis_produk',
                    'jumlah_tenaga_kerja',
                    'produk_utama',
                ])
                ->limit($limit)
                ->get();

            return [
                'kind' => 'list',
                'rows' => $rows,
            ];
        }

        if ($type === 'compare') {
            // compare biasanya 2 filter value (misal kecamatan A vs B)
            // kita jalankan dua agregat berdasarkan compare_values
            $compareCol = $plan['compare_col'] ?? 'kecamatan';
            $vals = $plan['compare_values'] ?? [];
            $metric = $plan['metric'] ?? 'count';

            if (!in_array($compareCol, $this->allowedColumns, true)) {
                $compareCol = 'kecamatan';
            }

            $out = [];
            foreach (array_slice($vals, 0, 5) as $v) {
                $qq = clone $q;
                $qq->where($compareCol, '=', $v);

                $out[$v] = $this->computeMetric($qq, $metric);
            }

            return [
                'kind' => 'compare',
                'compare_col' => $compareCol,
                'metric' => $metric,
                'result' => $out,
            ];
        }

        // default aggregate
        $groupBy = $plan['group_by'] ?? 'kecamatan';
        if (!in_array($groupBy, $this->allowedColumns, true)) {
            $groupBy = 'kecamatan';
        }

        $metric = $plan['metric'] ?? 'count';

        $select = [$groupBy];
        if ($metric === 'sum_tenaga_kerja') {
            $select[] = DB::raw('COALESCE(SUM(jumlah_tenaga_kerja),0) as value');
        } else {
            $select[] = DB::raw('COUNT(*) as value');
        }

        $rows = $q->select($select)
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
            return (int) $query->sum('jumlah_tenaga_kerja');
        }
        return (int) $query->count();
    }
}
