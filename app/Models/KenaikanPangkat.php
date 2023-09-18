<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KenaikanPangkat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nomor_sk',
        'notes',
        'employe_id',
        'pangkat_id',
        'pangkat_new_id',
        'tmt_pangkat',
        'created_by',
    ];

    protected $casts = [
        'tmt_pangkat' => 'datetime:d F Y',
        'created_at' => 'datetime:d F Y',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Employe::class, 'employe_id')
            ->select(['id', 'name'])->withTrashed();
    }

    public function pangkat()
    {
         return $this->belongsTo(Pangkat::class, 'pangkat_id')
            ->select(['id', 'pangkat','ruang'])->withTrashed();
    }

    public function pangkat_new()
    {
         return $this->belongsTo(Pangkat::class, 'pangkat_new_id')
            ->select(['id', 'pangkat','ruang'])->withTrashed();
    }
}
