<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IkmKoperindag extends Model
{
    protected $table = 'ikm_koperindag';

    protected $fillable = [
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
    ];
}
