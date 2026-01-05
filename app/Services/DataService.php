<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DataService
{
    public function getData(string $intent)
    {
        return match ($intent) {
            'average' => DB::table('ikm_koperindag')->avg('nilai_ikm'),

            'comparison' => DB::table('ikm_koperindag')
                ->select('tahun', DB::raw('AVG(nilai_ikm) as rata'))
                ->groupBy('tahun')
                ->get(),

            'ranking' => DB::table('ikm_koperindag')
                ->select('kecamatan', DB::raw('AVG(nilai_ikm) as nilai'))
                ->groupBy('kecamatan')
                ->orderByDesc('nilai')
                ->limit(5)
                ->get(),

            'trend' => DB::table('ikm_koperindag')
                ->select('tahun', DB::raw('AVG(nilai_ikm) as nilai'))
                ->groupBy('tahun')
                ->orderBy('tahun')
                ->get(),

            default => DB::table('ikm_koperindag')->limit(50)->get()
        };
    }
}
