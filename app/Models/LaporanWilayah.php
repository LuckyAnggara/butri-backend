<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanWilayah extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'name',
        'link',
        'ttd_name',
        'ttd_nip',
                'ttd_tanggal',
                'ttd_jabatan',
        'ttd_location',
        'group_id',
        'created_at',
        'created_by',
    ];


    protected $casts = [
        'created_at' => 'datetime:d F Y H:i:s',
    ];
}
