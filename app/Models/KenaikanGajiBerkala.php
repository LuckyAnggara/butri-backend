<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KenaikanGajiBerkala extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_sk',
        'notes',
        'employe_id',
        'tmt_gaji',
        'created_by',
    ];

    protected $casts = [
        'tmt_gaji' => 'datetime:d F Y',
        'created_at' => 'datetime:d F Y',
    ];

    public function pegawai()
    {
        return $this->hasOne(Employe::class, 'id', 'employe_id')->withTrashed();
    }

}
