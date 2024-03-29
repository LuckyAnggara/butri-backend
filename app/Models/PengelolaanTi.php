<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengelolaanTi extends Model
{
    use HasFactory;
    protected $fillable = [
        'bulan',
        'tahun',
        'jenis',
        'keterangan',
        'created_by'
    ];
}
