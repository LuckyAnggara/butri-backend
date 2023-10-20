<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MonitoringPengawasanItwil extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulan',
        'tahun',
        'group_id',
        'temuan_jumlah',
        'temuan_nominal',
        'tl_jumlah',
        'tl_nominal',
        'btl_jumlah',
        'btl_nominal',
        'created_by'
    ];

    public function group()
    {
        return $this->hasOne(GroupUnit::class, 'id', 'group_id');
    }
}
