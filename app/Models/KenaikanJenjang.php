<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KenaikanJenjang extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_sk',
        'notes',
        'employe_id',
        'jabatan_id',
        'jabatan_new_id',
        'tmt_jabatan',
        'created_by',
    ];

    protected $casts = [
        'tmt_jabatan' => 'datetime:d F Y',
        'created_at' => 'datetime:d F Y',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Employe::class, 'employe_id')
            ->select(['id', 'name'])->withTrashed();
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_id')
            ->select(['id', 'name', 'group'])->withTrashed();
    }

    public function jabatan_new()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_new_id')
            ->select(['id', 'name', 'group'])->withTrashed();
    }
}
