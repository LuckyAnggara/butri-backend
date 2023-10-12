<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringPengawasanItwil extends Model
{
    use HasFactory, SoftDeletes;

    public function group()
    {
        return $this->hasOne(GroupUnit::class, 'id', 'group_id');
    }
}
