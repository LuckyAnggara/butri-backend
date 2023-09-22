<?php

namespace Database\Seeders;

use App\Models\IndikatorKinerjaUtama;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IKUSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedData = [
            [
                'id' => 1,
                'nomor' => 1,
                'tahun' => 2023,
                'name' => 'Opini Audit Eksternal Atas Laporan Keuangan Kemenk...',
                'target' => 'WTP',
                'created_at' => now(),
            ],
            [
                'id' => 2,
                'nomor' => 2,
                'tahun' => 2023,
                'name' => 'Nilai Maturitas SPIP Kemenkumham',
                'target' => 'Level 3 (Terdefinisi)',
                'created_at' => now(),
            ],
            [
                'id' => 3,
                'nomor' => 3,
                'tahun' => 2023,
                'name' => 'Persentase Satuan Kerja yang Nilai AKIP minimal “B...',
                'target' => '92%',
                'created_at' => now(),
            ],
            [
                'id' => 4,
                'nomor' => 4,
                'tahun' => 2023,
                'name' => 'Persentase Satuan Kerja yang nilai capaian RB mini...',
                'target' => '92%',
                'created_at' => now(),
            ],
            [
                'id' => 5,
                'nomor' => 5,
                'tahun' => 2023,
                'name' => 'Persentase Satuan Kerja yang berhasil memperoleh p...',
                'target' => '6%',
                'created_at' => now(),
            ],
            [
                'id' => 6,
                'nomor' => 6,
                'tahun' => 2023,
                'name' => 'Indeks Persepsi Integritas Kemenkumham',
                'target' => '66',
                'created_at' => now(),
            ],
        ];

        // Simpan data ke dalam database atau format yang sesuai dengan kebutuhan Anda
        foreach ($seedData as $data) {
            // Contoh menyimpan data ke database menggunakan Eloquent ORM (Laravel)
            IndikatorKinerjaUtama::create($data);
        }
    }
}
