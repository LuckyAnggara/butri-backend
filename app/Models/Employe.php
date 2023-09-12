<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Jabatan;


class Employe extends Model
{
    use HasFactory, SoftDeletes;

    public function pangkat()
    {
        return $this->hasOne(Pangkat::class, 'id', 'pangkat_id')->withTrashed();
    }

    public function unit()
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id')->withTrashed();
    }

    public function jabatan()
    {
        return $this->hasOne(Jabatan::class, 'id', 'jabatan_id')->withTrashed();
    }

    public function eselon()
    {
        return $this->hasOne(Eselon::class, 'id', 'eselon_id')->withTrashed();
    }
}
