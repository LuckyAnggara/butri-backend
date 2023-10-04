<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealisasiAnggaran extends Model
{
    use HasFactory;

     protected $fillable = [
        'bulan',
        'realisasi',
        'dp',
        'dipa_id',
        'created_by',
    ];
}
