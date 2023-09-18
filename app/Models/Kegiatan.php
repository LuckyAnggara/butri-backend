<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'jenis_kegiatan',
        'tempat',
        'notes',
        'output',
        'unit_id',
        'created_by',
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'start_at' => 'datetime:d F Y',
        'end_at' => 'datetime:d F Y',
    ];
}
