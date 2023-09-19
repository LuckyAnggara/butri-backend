<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CapaianProgramUnggulan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kegiatan_id',
        'program_unggulan_id',
        'unit_id',
        'created_by',
    ];

    public function kegiatan()
    {
        return $this->hasOne(Kegiatan::class, 'id', 'kegiatan_id')->withTrashed();
    }

    public function program()
    {
        return $this->hasOne(ProgramUnggulan::class, 'id', 'program_unggulan_id')->withTrashed();
    }
}
