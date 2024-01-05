<?php

namespace Database\Seeders;

use App\Models\ProgramKegiatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramKegiatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Program Dukungan Manajemen', 'tahun' => 2024],
            ['name' => 'Program Pengawasan', 'tahun' => 2024],
        ];
        ProgramKegiatan::insert($data);
    }
}
