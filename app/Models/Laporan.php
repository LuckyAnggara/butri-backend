<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'bulan',
        'name',
        'link',
        'ttd_name',
        'ttd_jabatan',
        'ttd_tanggal',
        'ttd_nip',
        'ttd_location',
        'created_at',
        'created_by',
    ];


    protected $casts = [
        'created_at' => 'datetime:d F Y H:i:s',
    ];
}
