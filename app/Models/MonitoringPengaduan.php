<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonitoringPengaduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'tahun',
        'satker_id',
        'wbs',
        'aplikasi_lapor',
        'kotak_pengaduan',
        'website',
        'sms_gateway',
        'media_sosial',
        'surat_pos',
        'created_by'
    ];

    public function satker()
    {
        return $this->hasOne(SatuanKerja::class, 'id', 'satker_id');
    }
}
