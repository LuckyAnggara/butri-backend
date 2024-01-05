<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenetapanTujuanRisiko extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun',
        'program_kegiatan_id',
        'sasaran_kementerian_id',
        'iku_id',
        'permasalahan',
        'group_id',
        'created_by'
    ];

    public function program()
    {
        return $this->hasOne(ProgramKegiatan::class, 'id', 'program_kegiatan_id');
    }

    public function sasaran()
    {
        return $this->hasOne(SasaranKementerian::class, 'id', 'sasaran_kementerian_id');
    }

    public function iku()
    {
        return $this->hasOne(IndikatorKinerjaUtama::class, 'id', 'iku_id');
    }
}
