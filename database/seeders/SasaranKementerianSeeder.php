<?php

namespace Database\Seeders;

use App\Models\SasaranKementerian;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SasaranKementerianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Terwujudnya Pengelolaan Keuangan Kemenkumham yang Akuntabel', 'tahun' => 2024],
        ];
        SasaranKementerian::insert($data);
    }
}
