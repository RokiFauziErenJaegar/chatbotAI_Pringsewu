<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TableSchemaService
{
    /**
     * Mengambil daftar kolom dan tipe data dari tabel.
     * Cache agar tidak query schema terus-menerus.
     */
    public function getColumns(string $table): array
    {
        return Cache::remember("schema_cols:{$table}", now()->addHours(6), function () use ($table) {
            // MySQL/MariaDB: ambil dari INFORMATION_SCHEMA
            $dbName = DB::getDatabaseName();

            $rows = DB::table('INFORMATION_SCHEMA.COLUMNS')
                ->select('COLUMN_NAME', 'DATA_TYPE')
                ->where('TABLE_SCHEMA', $dbName)
                ->where('TABLE_NAME', $table)
                ->orderBy('ORDINAL_POSITION')
                ->get();

            $columns = [];
            foreach ($rows as $r) {
                $columns[$r->COLUMN_NAME] = strtolower((string) $r->DATA_TYPE);
            }
            return $columns; // ["kecamatan"=>"varchar", "jumlah_tenaga_kerja"=>"int", ...]
        });
    }

    public function getAllowedColumns(string $table): array
    {
        return array_keys($this->getColumns($table));
    }

    public function getNumericColumns(string $table): array
    {
        $cols = $this->getColumns($table);
        $numericTypes = [
            'int','integer','bigint','smallint','tinyint','mediumint',
            'decimal','numeric','float','double','real'
        ];

        $numeric = [];
        foreach ($cols as $name => $type) {
            if (in_array($type, $numericTypes, true)) {
                $numeric[] = $name;
            }
        }

        // kolom yang kadang numeric tapi tersimpan varchar di data nyata:
        // kamu bisa paksa anggap numeric
        if (isset($cols['jumlah_tenaga_kerja'])) $numeric[] = 'jumlah_tenaga_kerja';
        if (isset($cols['kapasitas_produksi'])) $numeric[] = 'kapasitas_produksi';

        return array_values(array_unique($numeric));
    }
}
