<?php

namespace Database\Seeders;

use App\Models\Pangkat;
use Illuminate\Database\Seeder;

class PangkatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['pangkat' => 'Ia', 'ruang' => 'Juru Muda'],
            ['pangkat' => 'Ib', 'ruang' => 'Juru Muda Tingkat I'],
            ['pangkat' => 'Ic', 'ruang' => 'Juru'],
            ['pangkat' => 'Id', 'ruang' => 'Juru Tingkat I'],
            ['pangkat' => 'IIa', 'ruang' => 'Pengatur Muda'],
            ['pangkat' => 'IIb', 'ruang' => 'Pengatur Muda Tingkat I'],
            ['pangkat' => 'IIc', 'ruang' => 'Pengatur'],
            ['pangkat' => 'IId', 'ruang' => 'Pengatur Tingkat I'],
            ['pangkat' => 'IIIa', 'ruang' => 'Penata Muda'],
            ['pangkat' => 'IIIb', 'ruang' => 'Penata Muda Tingkat 1'],
            ['pangkat' => 'IIIc', 'ruang' => 'Penata'],
            ['pangkat' => 'IIId', 'ruang' => 'Penata Tingkat I'],
            ['pangkat' => 'IVa', 'ruang' => 'Pembina'],
            ['pangkat' => 'IVb', 'ruang' => 'Pembina Tingkat I'],
            ['pangkat' => 'IVc', 'ruang' => 'Pembina Muda'],
            ['pangkat' => 'IVd', 'ruang' => 'Pembina Madya'],
            ['pangkat' => 'IVe', 'ruang' => 'Pembina Utama'],
        ];
        Pangkat::insert($data);
    }
}
