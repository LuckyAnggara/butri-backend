<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupUnit extends Model
{
    use HasFactory;

    public function unit()
    {
        return  $this->hasMany(Unit::class, 'group_id', 'id')->withTrashed();
    }
}
