<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndikatorKinerjaKegiatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor',
        'tahun',
        'target',
        'name',
        'group_id',
        'created_by',
    ];

    public function group()
    {
        return $this->hasOne(GroupUnit::class, 'id', 'group_id');
    }
}
