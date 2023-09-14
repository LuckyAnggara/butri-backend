<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Jabatan;


class Employe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nip',
        'phone_number',
        'is_wa',
        'gender',
        'email',
        'pangkat_id',
        'jabatan_id',
        'unit_id',
        'eselon_id',
        'tmt_pangkat',
        'tmt_jabatan',
        'tmt_pensiun',
        'created_by',
    ];

    protected $casts = [
        'tmt_jabatan' => 'datetime:d F Y',
        'tmt_pangkat' => 'datetime:d F Y',
        'tmt_pensiun' => 'datetime:d F Y',
    ];

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
