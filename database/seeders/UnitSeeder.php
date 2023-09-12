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
            ['name' => 'Sekretariat Inspektorat Jenderal'],
            ['name' => 'Inspektorat Wilayah I'],
            ['name' => 'Inspektorat Wilayah II'],
            ['name' => 'Inspektorat Wilayah III'],
            ['name' => 'Inspektorat Wilayah IV'],
            ['name' => 'Inspektorat Wilayah V'],
            ['name' => 'Inspektorat Wilayah VI'],
        ];

        Unit::insert($data);
    }
}
