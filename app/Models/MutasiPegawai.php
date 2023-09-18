<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        // 'masuk',
        'keluar',
        'tmt_jabatan',
        'created_by',
    ];

    protected $casts = [
        'tmt_jabatan' => 'datetime:d F Y',
        'created_at' => 'datetime:d F Y',
        // 'masuk' => 'boolean',
        'keluar' => 'boolean',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Employe::class, 'employe_id')
            ->select(['id', 'name'])->withTrashed();
    }

    public function jabatan()
    {
         return $this->belongsTo(Jabatan::class, 'jabatan_id')
            ->select(['id', 'name'])->withTrashed();
    }

    public function jabatan_new()
    {
         return $this->belongsTo(Jabatan::class, 'jabatan_new_id')
            ->select(['id', 'name'])->withTrashed();
    }

    
    public function unit()
    {
         return $this->belongsTo(Unit::class, 'unit_id')
            ->select(['id', 'name'])->withTrashed();
    }

    
    public function unit_new()
    {
         return $this->belongsTo(Unit::class, 'unit_new_id')
            ->select(['id', 'name'])->withTrashed();
    }


}
