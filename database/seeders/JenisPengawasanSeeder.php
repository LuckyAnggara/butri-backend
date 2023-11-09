<?php

namespace Database\Seeders;

use App\Models\JenisPengawasan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JenisPengawasanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run()
{
    $jenisPengawasan = [
        "AUDIT",
        "AUDIT TUJUAN TERTENTU / KHUSUS",
        "REVIU",
        "PEMANTAUAN / MONITORING",
        "EVALUASI",
        "PENGAWASAN LAINNYA",
    ];

    foreach ($jenisPengawasan as $jenis) {
       JenisPengawasan::create(['name' => $jenis]);
    }
}
}
