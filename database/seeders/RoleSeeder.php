<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $data = [
            ['name' => 'kepegawaian'],
            ['name' => 'keuangan'],
            ['name' => 'program dan pelaporan'],
            ['name' => 'humas dan sip'],
            ['name' => 'umum'],
            ['name' => 'inspektorat wilayah'],
        ];
        Role::insert($data);
    }
}
