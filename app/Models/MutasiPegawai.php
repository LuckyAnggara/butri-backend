<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MutasiPegawai extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_sk',
        'notes',
        'employe_id',
        'jabatan_id',
        'jabatan_new_id',
        'unit_id',
        'unit_new_id',
        'tmt_jabatan',
        'created_by',
    ];

    protected $casts = [
        'tmt_pensiun' => 'datetime:d F Y',
        'created_at' => 'datetime:d F Y',
    ];

    public function pegawai()
    {
        return $this->hasOne(Employe::class, 'id', 'employe_id')->withTrashed();
    }
}
