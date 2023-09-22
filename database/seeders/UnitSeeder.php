<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Inspektur Jenderal', 'group_id' => '8'],
            ['name' => 'Sekretariat Inspektorat Jenderal', 'group_id' => '1'],
            ['name' => 'Umum', 'group_id' => '1'],
            ['name' => 'Program dan Pelaporan', 'group_id' => '1'],
            ['name' => 'Keuangan', 'group_id' => '1'],
            ['name' => 'Kepegawaian', 'group_id' => '1'],
            ['name' => 'Hubungan Masyarakat dan Sistem Informasi Pengawasan', 'group_id' => '1'],
            ['name' => 'Inspektorat Wilayah I', 'group_id' => '2'],
            ['name' => 'Inspektorat Wilayah II', 'group_id' => '3'],
            ['name' => 'Inspektorat Wilayah III', 'group_id' => '4'],
            ['name' => 'Inspektorat Wilayah IV', 'group_id' => '5'],
            ['name' => 'Inspektorat Wilayah V', 'group_id' => '6'],
            ['name' => 'Inspektorat Wilayah VI', 'group_id' => '7'],
        ];

        Unit::insert($data);
    }
}
