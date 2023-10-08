<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ikpa extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'tahun',
        'revisi_dipa',
        'halaman_tiga_dipa',
        'penyerapan_anggaran',
        'belanja_kontraktual',
        'penyelesaian_tagihan',
        'pengelolaan_up_tup',
        'dispensasi_spm',
        'capaian_output',
        'created_by',
    ];
}
