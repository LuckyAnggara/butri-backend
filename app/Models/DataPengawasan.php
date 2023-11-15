<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPengawasan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tahun',
        'bulan',
        'sp_number',
        'sp_date',
        'jenis_pengawasan_id',
        'start_at',
        'end_at',
        'lhp',
        'location',
        'output',
        'unit_id',
        'created_by',
    ];

    protected $casts = [
        'sp_date' => 'datetime:d F Y',

        'start_at' => 'datetime:d M Y',
        'end_at' => 'datetime:d M Y',
    ];

    protected $appends = ['tanggalKegiatan'];

    public function jenis()
    {
        return $this->hasOne(JenisPengawasan::class, 'id', 'jenis_pengawasan_id');
    }

    public function getTanggalKegiatanAttribute()
    {
        return [
            'startDate' => $this->start_at->format('d F Y'),
            'endDate' => $this->end_at->format('d F Y'),
        ];
    }
}
