<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapaianIndikatorKinerjaKegiatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ikk_id',
        'realisasi',
        'analisa',
        'kegiatan',
        'kendala',
        'hambatan',
        'group_id',
        'created_by',
    ];

    public function ikk()
    {
        return $this->hasOne(IndikatorKinerjaKegiatan::class, 'id', 'ikk_id')->withTrashed();
    }
}
