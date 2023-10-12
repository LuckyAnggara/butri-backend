<?php

namespace Database\Seeders;

use App\Models\GroupUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitGroupData = [
            ['name' => 'Sekretariat Inspektorat Jenderal', 'has_child' => true, 'type' => 'management'],
            ['name' => 'Inspektorat Wilayah I', 'has_child' => false, 'type' => 'teknis'],
            ['name' => 'Inspektorat Wilayah II', 'has_child' => false, 'type' => 'teknis'],
            ['name' => 'Inspektorat Wilayah III', 'has_child' => false, 'type' => 'teknis'],
            ['name' => 'Inspektorat Wilayah IV', 'has_child' => false, 'type' => 'teknis'],
            ['name' => 'Inspektorat Wilayah V', 'has_child' => false, 'type' => 'teknis'],
            ['name' => 'Inspektorat Wilayah VI', 'has_child' => false, 'type' => 'teknis'],
            ['name' => 'Inspektorat Jenderal', 'has_child' => true, 'type' => 'management'],
        ];

        // Simpan data ke dalam database atau format yang sesuai dengan kebutuhan Anda
        foreach ($unitGroupData as $group) {
            // Contoh menyimpan data ke database menggunakan Eloquent ORM (Laravel)
            GroupUnit::create($group);
        }
    }
}
