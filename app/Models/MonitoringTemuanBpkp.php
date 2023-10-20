<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringTemuanBpkp extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'tahun',
        'keterangan',
        'jumlah',
        'nominal',
        'created_by'
    ];
}
