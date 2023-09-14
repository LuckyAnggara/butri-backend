<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengembangan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'surat_tugas',
        'kegiatan',
        'tempat',
        'jumlah_peserta',
        'jumlah_hari',
        'start_at',
        'end_at',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime:d F Y',
        'end_at' => 'datetime:d F Y',
    ];

    protected $appends = ['waktu'];

    public function detail()
    {
        return $this->hasMany(DetailPengembangan::class, 'pengembangan_id', 'id');
    }

    public function list()
    {
        $data = $this->hasMany(DetailPengembangan::class, 'pengembangan_id', 'id');

        return $data;
    }

    public function getWaktuAttribute()
    {
        return [
            'startDate' => Carbon::create($this->start_at)->format('d F Y'),
            'endDate' => Carbon::create($this->end_at)->format('d F Y'),
        ];
    }
}
