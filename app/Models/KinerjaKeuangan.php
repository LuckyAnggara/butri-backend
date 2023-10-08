<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KinerjaKeuangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'capaian_sasaran_program',
        'penyerapan',
        'konsistensi',
        'capaian_output_program',
        'efisiensi',
        'nilai_efisiensi',
        'rata_nka_satker',
        'nilai_kinerja',
        'created_by',
        'created_at',
    ];


    protected $appends = ['bulan'];
    public function getBulanAttribute()
    {
        return Carbon::create($this->created_at)->format('n');
    }
}
