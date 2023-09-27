<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Arsip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jenis_kegiatan',
        'kegiatan',
        'output',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime:d F Y',
    ];
}
