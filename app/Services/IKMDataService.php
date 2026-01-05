<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class IKMDataService
{
    private string $table = 'ikm_koperindag';

    public function __construct(private TableSchemaService $schema) {}

    public function runPlan(array $plan): array
    {
        $allowedCols = $this->schema->getAllowedColumns($this->table);
        $numericCols = $this->schema->getNumericColumns($this->table);

        $type = $plan['type'] ?? 'aggregate';
        $limit = max(1, min(50, (int)($plan['limit'] ?? 10)));
        $filters = $plan['filters'] ?? [];
        if (!is_array($filters)) $filters = [];

        $q = DB::table($this->table);

        // apply filters aman
        foreach ($filters as $f) {
            $col = $f['col'] ?? null;
            $op  = $f['op'] ?? '=';
            $val = $f['val'] ?? null;

            if (!is_string($col) || !in_array($col, $allowedCols, true)) continue;
            if (!in_array($op, ['=','!=','like','>','>=','<','<='], true)) $op = '=';

            if ($op === 'like') {
                $q->where($col, 'like', '%' . (string)$val . '%');
            } else {
                $q->where($col, $op, $val);
            }
        }

        if ($type === 'list') {
            $select = $plan['select'] ?? [];
            if (!is_array($select) || count($select) === 0) {
                $select = array_values(array_slice($allowedCols, 0, 6));
            }
            $select = array_values(array_filter($select, fn($c) => is_string($c) && in_array($c, $allowedCols, true)));
            $select = array_slice($select, 0, 12);

            $rows = $q->select($select)->limit($limit)->get();
            return ['kind' => 'list', 'select' => $select, 'rows' => $rows];
        }

        if ($type === 'compare') {
            $compareCol = $plan['compare_col'] ?? 'kecamatan';
            if (!is_string($compareCol) || !in_array($compareCol, $allowedCols, true)) {
                $compareCol = in_array('kecamatan', $allowedCols, true) ? 'kecamatan' : $allowedCols[0];
            }

            $vals = $plan['compare_values'] ?? [];
            if (!is_array($vals)) $vals = [];
            $vals = array_values(array_filter(array_map(fn($v) => trim((string)$v), $vals), fn($v) => $v !== ''));
            $vals = array_slice($vals, 0, 5);

            $metric = $plan['metric'] ?? 'count';
            $metric = is_string($metric) ? strtolower($metric) : 'count';
            if (!in_array($metric, ['count','sum','avg','min','max'], true)) $metric = 'count';

            $metricCol = $plan['metric_col'] ?? null;
            if ($metric !== 'count') {
                if (!is_string($metricCol) || !in_array($metricCol, $numericCols, true)) {
                    $metricCol = $numericCols[0] ?? null;
                }
            } else {
                $metricCol = null;
            }

            $out = [];
            foreach ($vals as $v) {
                $qq = clone $q;
                // match compare lebih tahan spasi dan case
                $needle = mb_strtolower(trim($v));
                $qq->whereRaw("LOWER(TRIM($compareCol)) = ?", [$needle]);

                $out[$v] = $this->computeMetric($qq, $metric, $metricCol);
            }

            return [
                'kind' => 'compare',
                'compare_col' => $compareCol,
                'metric' => $metric,
                'metric_col' => $metricCol,
                'result' => $out,
            ];
        }

        // aggregate
        $groupBy = $plan['group_by'] ?? (in_array('kecamatan', $allowedCols, true) ? 'kecamatan' : $allowedCols[0]);
        if (!is_string($groupBy) || !in_array($groupBy, $allowedCols, true)) {
            $groupBy = in_array('kecamatan', $allowedCols, true) ? 'kecamatan' : $allowedCols[0];
        }

        $metric = $plan['metric'] ?? 'count';
        $metric = is_string($metric) ? strtolower($metric) : 'count';
        if (!in_array($metric, ['count','sum','avg','min','max'], true)) $metric = 'count';

        $metricCol = $plan['metric_col'] ?? null;
        if ($metric !== 'count') {
            if (!is_string($metricCol) || !in_array($metricCol, $numericCols, true)) {
                $metricCol = $numericCols[0] ?? null;
            }
        } else {
            $metricCol = null;
        }

        // hindari group kosong
        $qAgg = clone $q;
        $qAgg->whereNotNull($groupBy)->whereRaw("TRIM($groupBy) <> ''");

        $select = [$groupBy];

        if ($metric === 'count') {
            $select[] = DB::raw("COUNT(*) as value");
        } else {
            // tahan kalau numeric tersimpan varchar: cast unsigned
            // avg/min/max juga pakai CAST agar stabil
            $colExpr = "CAST(TRIM($metricCol) AS DECIMAL(20,4))";
            if ($metric === 'sum') $select[] = DB::raw("COALESCE(SUM($colExpr),0) as value");
            if ($metric === 'avg') $select[] = DB::raw("COALESCE(AVG($colExpr),0) as value");
            if ($metric === 'min') $select[] = DB::raw("COALESCE(MIN($colExpr),0) as value");
            if ($metric === 'max') $select[] = DB::raw("COALESCE(MAX($colExpr),0) as value");
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
            'metric_col' => $metricCol,
            'rows' => $rows,
        ];
    }

    private function computeMetric($query, string $metric, ?string $metricCol): float|int
    {
        if ($metric === 'count') return (int) $query->count();

        if (!$metricCol) return 0;

        $colExpr = "CAST(TRIM($metricCol) AS DECIMAL(20,4))";

        if ($metric === 'sum') {
            $row = $query->select(DB::raw("COALESCE(SUM($colExpr),0) as v"))->first();
        } elseif ($metric === 'avg') {
            $row = $query->select(DB::raw("COALESCE(AVG($colExpr),0) as v"))->first();
        } elseif ($metric === 'min') {
            $row = $query->select(DB::raw("COALESCE(MIN($colExpr),0) as v"))->first();
        } else { // max
            $row = $query->select(DB::raw("COALESCE(MAX($colExpr),0) as v"))->first();
        }

        $v = $row->v ?? 0;
        // kembalikan int kalau hasilnya bulat
        return (floor((float)$v) == (float)$v) ? (int)$v : (float)$v;
    }
}
