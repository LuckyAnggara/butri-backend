<?php

namespace Database\Seeders;

use App\Models\Eselon;
use Illuminate\Database\Seeder;

class EselonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Pelaksana', 'type' => 'Umum'],
            ['name' => 'Penyelia', 'type' => 'Fungsional'],
            ['name' => 'Mahir', 'type' => 'Fungsional'],
            ['name' => 'Terampil', 'type' => 'Fungsional'],
            ['name' => 'Pemula', 'type' => 'Fungsional'],
            ['name' => 'Ahli Pertama', 'type' => 'Fungsional'],
            ['name' => 'Ahli Muda', 'type' => 'Fungsional'],
            ['name' => 'Ahli Madya', 'type' => 'Fungsional'],
            ['name' => 'Ahli Utama', 'type' => 'Fungsional'],
            ['name' => 'Eselon V', 'type' => 'Struktural'],
            ['name' => 'Eselon IVB', 'type' => 'Struktural'],
            ['name' => 'Eselon IVA', 'type' => 'Struktural'],
            ['name' => 'Eselon IIIB', 'type' => 'Struktural'],
            ['name' => 'Eselon IIIA', 'type' => 'Struktural'],
            ['name' => 'Eselon IIB', 'type' => 'Struktural'],
            ['name' => 'Eselon IIA', 'type' => 'Struktural'],
            ['name' => 'Eselon IB', 'type' => 'Struktural'],
            ['name' => 'Eselon IA', 'type' => 'Struktural'],
        ];

        Eselon::insert($data);
    }
}
