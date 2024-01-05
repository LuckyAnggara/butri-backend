<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SasaranKementerian extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tahun',
    ];
}
