<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persuratan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bulan',
        'tahun',
        'surat_masuk',
        'surat_keluar',
        // 'group_id',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];

    public function group()
    {
        return $this->hasOne(GroupUnit::class, 'id', 'group_id');
    }
}
