<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'group',
    ];

    public function pegawai(){
        return $this->hasMany(Employe::class, 'jabatan_id', 'id');
    }

    public function jumlah_pegawai(){
        return $this->pegawai->count();
    }
}
