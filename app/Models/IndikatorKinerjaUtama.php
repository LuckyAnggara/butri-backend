<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndikatorKinerjaUtama extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'target',
        'tahun',
        'ss_id',
        'nomor',
        'created_by',
    ];

    public function capaian()
    {
        return $this->hasOne(CapaianIndikatorKegiatanUtama::class, 'iku_id', 'id');
    }
}
