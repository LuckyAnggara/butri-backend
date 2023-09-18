<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProgramUnggulan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'tahun',
        'target',
        'satuan',

    ];

    public function list()
    {
        $data = $this->hasMany(CapaianProgramUnggulan::class, 'program_unggulan_id', 'id');
        return $data;
    }
}
