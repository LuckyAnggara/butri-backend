<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapaianIndikatorKegiatanUtama extends Model
{
    use HasFactory;


    protected $fillable = [
        'iku_id',
        'tahun',
        'realisasi',
        'analisa',
        'kegiatan',
        'kendala',
        'hambatan',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];

    public function iku()
    {
        return $this->hasOne(IndikatorKinerjaUtama::class, 'id', 'iku_id')->withTrashed();
    }
}
