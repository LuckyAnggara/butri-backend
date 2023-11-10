<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dipa extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pagu',
        'tahun',
        'name',
        'kode',
        'group_id',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:F Y',
    ];

    public function realisasi()
    {
        return $this->hasMany(RealisasiAnggaran::class, 'dipa_id', 'id');
    }

    public function group()
    {
        return $this->hasOne(GroupUnit::class, 'id', 'group_id');
    }

    public function totalRealisasi($bulan = null)
    {
        $totalRealisasi = 0;
        $this->realisasiAnggaranHinggaTanggal($bulan)->each(function ($realisasiAnggaran) use (&$totalRealisasi) {
            $totalRealisasi += $realisasiAnggaran->realisasi;
        });
        return $totalRealisasi;
    }

    public function totalRealisasiHinggaTanggal($tanggalReferensi = null)
    {
        return $this->totalRealisasi($tanggalReferensi);
    }
}
