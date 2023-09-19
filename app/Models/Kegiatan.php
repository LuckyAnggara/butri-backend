<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kegiatan extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'jenis_kegiatan',
        'tempat',
        'notes',
        'output',
        'unit_id',
        'created_by',
        'start_at',
        'end_at'
    ];

    protected $casts = [
        'start_at' => 'datetime:d F Y',
        'end_at' => 'datetime:d F Y',
    ];

    protected $appends = ['waktu', 'capaian'];


    public function getWaktuAttribute()
    {
        return [
            'startDate' => Carbon::create($this->start_at)->format('d F Y'),
            'endDate' => Carbon::create($this->end_at)->format('d F Y'),
        ];
    }

    public function getCapaianAttribute()
    {
        return count($this->list);
    }

    public function list()
    {
        $data = $this->hasMany(CapaianProgramUnggulan::class, 'kegiatan_id', 'id');
        return $data;
    }
}
