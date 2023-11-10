<?php

namespace Database\Seeders;

use App\Models\IndikatorKinerjaUtama;
use App\Models\ProgramUnggulan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProgramUnggulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedData = [
            [
                'id' => 1,
                'tahun' => 2023,
                'name' => 'Gerbang Transisi',
                'target' => '0',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'tahun' => 2023,
                'name' => 'Emawas',
                'target' => '0',
                'created_at' => now(),
            ],
        ];

        // Simpan data ke dalam database atau format yang sesuai dengan kebutuhan Anda
        foreach ($seedData as $data) {
            // Contoh menyimpan data ke database menggunakan Eloquent ORM (Laravel)
            ProgramUnggulan::create($data);
        }
    }
}
