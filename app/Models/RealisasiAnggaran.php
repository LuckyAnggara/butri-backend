<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiAnggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'tahun',
        'realisasi',
        'dp',
        'dipa_id',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];

    public function dipa()
    {
        return $this->hasOne(Dipa::class, 'id', 'dipa_id');
    }
}
