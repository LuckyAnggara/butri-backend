<?php

namespace Database\Seeders;

use App\Models\Dipa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DipaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seedData = [
            [
                'kode' => 1563,
                'tahun' => 2023,
                'name' => 'Pengawasan Inspektorat Jenderal Wilayah I',
                'pagu' => 4429775000,
                'created_by' => 1,
                'jenis' => 'kegiatan',
                'group_id' => 1,
                'created_at' => now(),
            ],
            [
                'kode' => 1564,
                'tahun' => 2023,
                'name' => 'Pengawasan Inspektorat Jenderal Wilayah II',
                'pagu' => 4429775000,
                'created_by' => 1,
                'jenis' => 'kegiatan',
                'group_id' => 2,
                'created_at' => now(),
            ],
            [
                'kode' => 1565,
                'tahun' => 2023,
                'name' => 'Pengawasan Inspektorat Jenderal Wilayah III',
                'pagu' => 4429775000,
                'created_by' => 1,
                'jenis' => 'kegiatan',
                'group_id' => 3,
                'created_at' => now(),
            ],
            [
                'kode' => 1566,
                'tahun' => 2023,
                'name' => 'Pengawasan Inspektorat Jenderal Wilayah VI',
                'pagu' => 4429775000,
                'created_by' => 1,
                'jenis' => 'kegiatan',
                'group_id' => 4,
                'created_at' => now(),
            ],
            [
                'kode' => 1567,
                'tahun' => 2023,
                'name' => 'Pengawasan Inspektorat Jenderal Wilayah V',
                'pagu' => 4429775000,
                'created_by' => 1,
                'jenis' => 'kegiatan',
                'group_id' => 5,
                'created_at' => now(),
            ],
            [
                'kode' => 1568,
                'tahun' => 2023,
                'name' => 'Pengawasan Inspektorat Jenderal Wilayah VI',
                'pagu' => 6100526000,
                'created_by' => 1,
                'jenis' => 'kegiatan',
                'group_id' => 6,
                'created_at' => now(),
            ],
            [
                'kode' => 1569,
                'tahun' => 2023,
                'name' => 'Dukungan Manajemen dan Teknis Inspektorat Jenderal',
                'pagu' => 40777534000,
                'created_by' => 1,
                'jenis' => 'kegiatan',
                'group_id' => 7,
                'created_at' => now(),
            ],

        ];

        // Simpan data ke dalam database atau format yang sesuai dengan kebutuhan Anda
        foreach ($seedData as $data) {
            // Contoh menyimpan data ke database menggunakan Eloquent ORM (Laravel)
            Dipa::create($data);
        }
    }
}
