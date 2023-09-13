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
            ['name' => 'Inspektur Jenderal', 'group' => 'Inspektorat Jenderal'],
            ['name' => 'Sekretariat Inspektorat Jenderal', 'group' => 'Sekretariat Inspektorat Jenderal'],
            ['name' => 'Umum', 'group' => 'Sekretariat Inspektorat Jenderal'],
            ['name' => 'Program dan Pelaporan', 'group' => 'Sekretariat Inspektorat Jenderal'],
            ['name' => 'Keuangan', 'group' => 'Sekretariat Inspektorat Jenderal'],
            ['name' => 'Kepegawaian', 'group' => 'Sekretariat Inspektorat Jenderal'],
            ['name' => 'Hubungan Masyarakat dan Sistem Informasi Pengawasan', 'group' => 'Sekretariat Inspektorat Jenderal'],
            ['name' => 'Inspektorat Wilayah I', 'group' => 'Inspektorat Wilayah I'],
            ['name' => 'Inspektorat Wilayah II', 'group' => 'Inspektorat Wilayah II'],
            ['name' => 'Inspektorat Wilayah III', 'group' => 'Inspektorat Wilayah III'],
            ['name' => 'Inspektorat Wilayah IV', 'group' => 'Inspektorat Wilayah IV'],
            ['name' => 'Inspektorat Wilayah V', 'group' => 'Inspektorat Wilayah V'],
            ['name' => 'Inspektorat Wilayah VI', 'group' => 'Inspektorat Wilayah VI'],
        ];

        Unit::insert($data);
    }
}
