<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengembangan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employe_id',
        'status',
        'pengembangan_id',

    ];

    public function pegawai()
    {
        return $this->hasOne(Employe::class, 'id', 'employe_id')->withTrashed();
    }
}
