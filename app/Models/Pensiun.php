<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pensiun extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'nomor_sk',
        'tentang',
        'employe_id',
        'tmt_pensiun',
        'notes',
        'email',
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
