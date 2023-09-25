<?php

namespace Database\Seeders;

use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IKKSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitGroupData = [
            [
                'id' => 1,
                'nomor' => 1,
                'name' => 'Level IACM / Kapabilitas APIP ITJEN Kemenkumham',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '3 (Integrated)',
            ],
            [
                'id' => 2,
                'nomor' => 2,
                'name' => 'Persentase Pemanfaatan Penerapan Manajemen Risiko dalam Pelaksanaan Tugas dan Fungsi Satuan Kerja di Lingkungan Kerja Inspektorat Wilayah I',
                'group_id' => 2, // INSPEKTORAT WILAYAH I
                'target' => '52%',
            ],
            [
                'id' => 3,
                'nomor' => 3,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait pengembalian ke kas Negara di Lingkungan Kerja Inspektorat Wilayah I',
                'group_id' => 2, // INSPEKTORAT WILAYAH I
                'target' => '62%',
            ],
            // Lanjutkan dengan data lainnya dengan group_id yang sesuai
            [
                'id' => 4,
                'nomor' => 4,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah I',
                'group_id' => 2, // INSPEKTORAT WILAYAH I
                'target' => '82%',
            ],
            [
                'id' => 5,
                'nomor' => 5,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Kerugian Negara di Lingkungan Kerja Inspektorat Wilayah I',
                'group_id' => 2, // INSPEKTORAT WILAYAH I
                'target' => '22%',
            ],
            [
                'id' => 6,
                'nomor' => 6,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah I',
                'group_id' => 2, // INSPEKTORAT WILAYAH I
                'target' => '78%',
            ],
            [
                'id' => 7,
                'nomor' => 7,
                'name' => 'Persentase Satuan Kerja di Lingkungan Inspektorat Wilayah I yang mendapatkan Predikat WBK/WBBM',
                'group_id' => 2, // INSPEKTORAT WILAYAH I
                'target' => '6%',
            ],
            [
                'id' => 8,
                'nomor' => 1,
                'name' => 'Level IACM / Kapabilitas APIP ITJEN Kemenkumham',
                'group_id' => 3, // INSPEKTORAT WILAYAH II
                'target' => '3 (Integrated)',
            ],
            [
                'id' => 9,
                'nomor' => 2,
                'name' => 'Persentase Pemanfaatan Penerapan Manajemen Risiko dalam Pelaksanaan Tugas dan Fungsi Satuan Kerja di Lingkungan Kerja Inspektorat Wilayah II',
                'group_id' => 3, // INSPEKTORAT WILAYAH II
                'target' => '52%',
            ],
            [
                'id' => 10,
                'nomor' => 3,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait pengembalian ke kas Negara di Lingkungan Kerja Inspektorat Wilayah II',
                'group_id' => 3, // INSPEKTORAT WILAYAH II
                'target' => '62%',
            ],
            [
                'id' => 11,
                'nomor' => 4,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah II',
                'group_id' => 3, // INSPEKTORAT WILAYAH II
                'target' => '82%',
            ],
            [
                'id' => 12,
                'nomor' => 5,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Kerugian Negara di Lingkungan Kerja Inspektorat Wilayah II',
                'group_id' => 3, // INSPEKTORAT WILAYAH II
                'target' => '22%',
            ],
            [
                'id' => 13,
                'nomor' => 6,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah II',
                'group_id' => 3, // INSPEKTORAT WILAYAH II
                'target' => '78%',
            ],
            [
                'id' => 14,
                'nomor' => 7,
                'name' => 'Persentase Satuan Kerja di Lingkungan Inspektorat Wilayah II yang mendapatkan Predikat WBK/WBBM',
                'group_id' => 3, // INSPEKTORAT WILAYAH II
                'target' => '6%',
            ],
            [
                'id' => 15,
                'nomor' => 1,
                'name' => 'Level IACM / Kapabilitas APIP ITJEN Kemenkumham',
                'group_id' => 4, // INSPEKTORAT WILAYAH III
                'target' => '3 (Integrated)',
            ],
            [
                'id' => 16,
                'nomor' => 2,
                'name' => 'Persentase Pemanfaatan Penerapan Manajemen Risiko dalam Pelaksanaan Tugas dan Fungsi Satuan Kerja di Lingkungan Kerja Inspektorat Wilayah III',
                'group_id' => 4, // INSPEKTORAT WILAYAH III
                'target' => '52%',
            ],
            [
                'id' => 17,
                'nomor' => 3,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait pengembalian ke kas Negara di Lingkungan Kerja Inspektorat Wilayah III',
                'group_id' => 4, // INSPEKTORAT WILAYAH III
                'target' => '62%',
            ],
            [
                'id' => 18,
                'nomor' => 4,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah III',
                'group_id' => 4, // INSPEKTORAT WILAYAH III
                'target' => '82%',
            ],
            [
                'id' => 19,
                'nomor' => 5,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Kerugian Negara di Lingkungan Kerja Inspektorat Wilayah III',
                'group_id' => 4, // INSPEKTORAT WILAYAH III
                'target' => '22%',
            ],
            [
                'id' => 20,
                'nomor' => 6,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah III',
                'group_id' => 4, // INSPEKTORAT WILAYAH III
                'target' => '78%',
            ],
            [
                'id' => 21,
                'nomor' => 7,
                'name' => 'Persentase Satuan Kerja di Lingkungan Inspektorat Wilayah III yang mendapatkan Predikat WBK/WBBM',
                'group_id' => 4, // INSPEKTORAT WILAYAH III
                'target' => '6%',
            ],
            [
                'id' => 22,
                'nomor' => 1,
                'name' => 'Level IACM / Kapabilitas APIP ITJEN Kemenkumham',
                'group_id' => 5, // INSPEKTORAT WILAYAH IV
                'target' => '3 (Integrated)',
            ],
            [
                'id' => 23,
                'nomor' => 2,
                'name' => 'Persentase Pemanfaatan Penerapan Manajemen Risiko dalam Pelaksanaan Tugas dan Fungsi Satuan Kerja di Lingkungan Kerja Inspektorat Wilayah IV',
                'group_id' => 5, // INSPEKTORAT WILAYAH IV
                'target' => '52%',
            ],
            [
                'id' => 24,
                'nomor' => 3,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait pengembalian ke kas Negara di Lingkungan Kerja Inspektorat Wilayah IV',
                'group_id' => 5, // INSPEKTORAT WILAYAH IV
                'target' => '62%',
            ],
            [
                'id' => 25,
                'nomor' => 4,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah IV',
                'group_id' => 5, // INSPEKTORAT WILAYAH IV
                'target' => '82%',
            ],
            [
                'id' => 26,
                'nomor' => 5,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Kerugian Negara di Lingkungan Kerja Inspektorat Wilayah IV',
                'group_id' => 5, // INSPEKTORAT WILAYAH IV
                'target' => '22%',
            ],
            [
                'id' => 27,
                'nomor' => 6,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah IV',
                'group_id' => 5, // INSPEKTORAT WILAYAH IV
                'target' => '78%',
            ],
            [
                'id' => 28,
                'nomor' => 7,
                'name' => 'Persentase Satuan Kerja di Lingkungan Inspektorat Wilayah IV yang mendapatkan Predikat WBK/WBBM',
                'group_id' => 5, // INSPEKTORAT WILAYAH IV
                'target' => '6%',
            ],
            [
                'id' => 29,
                'nomor' => 1,
                'name' => 'Level IACM / Kapabilitas APIP ITJEN Kemenkumham',
                'group_id' => 6, // INSPEKTORAT WILAYAH V
                'target' => '3 (Integrated)',
            ],
            [
                'id' => 30,
                'nomor' => 2,
                'name' => 'Persentase Pemanfaatan Penerapan Manajemen Risiko dalam Pelaksanaan Tugas dan Fungsi Satuan Kerja di Lingkungan Kerja Inspektorat Wilayah V',
                'group_id' => 6, // INSPEKTORAT WILAYAH V
                'target' => '52%',
            ],
            [
                'id' => 31,
                'nomor' => 3,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait pengembalian ke kas Negara di Lingkungan Kerja Inspektorat Wilayah V',
                'group_id' => 6, // INSPEKTORAT WILAYAH V
                'target' => '62%',
            ],
            [
                'id' => 32,
                'nomor' => 4,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah V',
                'group_id' => 6, // INSPEKTORAT WILAYAH V
                'target' => '82%',
            ],
            [
                'id' => 33,
                'nomor' => 5,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Kerugian Negara di Lingkungan Kerja Inspektorat Wilayah V',
                'group_id' => 6, // INSPEKTORAT WILAYAH V
                'target' => '22%',
            ],
            [
                'id' => 34,
                'nomor' => 6,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah V',
                'group_id' => 6, // INSPEKTORAT WILAYAH V
                'target' => '78%',
            ],
            [
                'id' => 35,
                'nomor' => 7,
                'name' => 'Persentase Satuan Kerja di Lingkungan Inspektorat Wilayah V yang mendapatkan Predikat WBK/WBBM',
                'group_id' => 6, // INSPEKTORAT WILAYAH V
                'target' => '6%',
            ],
            [
                'id' => 36,
                'nomor' => 1,
                'name' => 'Level IACM / Kapabilitas APIP ITJEN Kemenkumham',
                'group_id' => 7, // INSPEKTORAT WILAYAH VI
                'target' => '3 (Integrated)',
            ],
            [
                'id' => 37,
                'nomor' => 2,
                'name' => 'Persentase Pemanfaatan Penerapan Manajemen Risiko dalam Pelaksanaan Tugas dan Fungsi Satuan Kerja di Lingkungan Kerja Inspektorat Wilayah VI',
                'group_id' => 7, // INSPEKTORAT WILAYAH VI
                'target' => '52%',
            ],
            [
                'id' => 38,
                'nomor' => 3,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait pengembalian ke kas Negara di Lingkungan Kerja Inspektorat Wilayah VI',
                'group_id' => 7, // INSPEKTORAT WILAYAH VI
                'target' => '62%',
            ],
            [
                'id' => 39,
                'nomor' => 4,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pengawasan Internal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah VI',
                'group_id' => 7, // INSPEKTORAT WILAYAH VI
                'target' => '82%',
            ],
            [
                'id' => 40,
                'nomor' => 5,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Kerugian Negara di Lingkungan Kerja Inspektorat Wilayah VI',
                'group_id' => 7, // INSPEKTORAT WILAYAH VI
                'target' => '22%',
            ],
            [
                'id' => 41,
                'nomor' => 6,
                'name' => 'Persentase Peningkatan Pengelolaan Tindak Lanjut Rekomendasi Hasil Pemeriksaan Eksternal terkait Administrasi di Lingkungan Kerja Inspektorat Wilayah VI',
                'group_id' => 7, // INSPEKTORAT WILAYAH VI
                'target' => '78%',
            ],
            [
                'id' => 42,
                'nomor' => 7,
                'name' => 'Persentase Satuan Kerja di Lingkungan Inspektorat Wilayah VI yang mendapatkan Predikat WBK/WBBM',
                'group_id' => 7, // INSPEKTORAT WILAYAH VI
                'target' => '6%',
            ],
            [
                'id' => 43,
                'nomor' => 1,
                'name' => 'Pengelolaan Unit Pemberantasan Pungutan Liar (UPP) Kementerian Hukum dan HAM',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '1 Rekomendasi',
            ],
            [
                'id' => 44,
                'nomor' => 2,
                'name' => 'Indeks RB Inspektorat Jenderal',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '14.17',
            ],
            [
                'id' => 45,
                'nomor' => 3,
                'name' => 'Nilai SAKIP Inspektorat Jenderal "BAIK"',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '82.88',
            ],
            [
                'id' => 46,
                'nomor' => 4,
                'name' => 'Nilai Maturitas SPIP Inspektorat Jenderal',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => 'Level 3 (Terdefinisi)',
            ],
            [
                'id' => 47,
                'nomor' => 5,
                'name' => 'Persentase SDM yang memenuhi standar kompetensi',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '82%',
            ],
            [
                'id' => 48,
                'nomor' => 6,
                'name' => 'Tingkat internalisasi pegawai Inspektorat Jenderal atas Tata Nilai Kemenkumham',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '3',
            ],
            [
                'id' => 49,
                'nomor' => 7,
                'name' => 'Persentase pemenuhan pengembangan teknologi informasi yang menunjang proses bisnis bidang pengawasan/ pengendalian internal',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '82%',
            ],
            [
                'id' => 50,
                'nomor' => 8,
                'name' => 'Persentase realisasi layanan perkantoran yang akuntabel',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '85%',
            ],
            [
                'id' => 51,
                'nomor' => 9,
                'name' => 'Jumlah layanan fasilitas kerumahtanggaan, BMN, dan sarpras internal',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '12 Bulan Layanan',
            ],
            [
                'id' => 52,
                'nomor' => 10,
                'name' => 'Laporan keuangan Itjen yang akuntabel',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => 'WTP',
            ],
            [
                'id' => 53,
                'nomor' => 11,
                'name' => 'Persentase efektivitas pemanfaatan anggaran Itjen',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '87%',
            ],
            [
                'id' => 54,
                'nomor' => 12,
                'name' => 'Persentase fasilitasi pengelolaan tindak lanjut rekomendasi penyusunan RKA-KL Itjen',
                'group_id' => 1, // SEKRETARIAT INSPEKTORAT JENDERAL
                'target' => '95%',
            ],
        ];
        foreach ($unitGroupData as &$group) {
            unset($group['id']);
            $group['tahun'] = 2023;
        }

        foreach ($unitGroupData as $group) {
            // Contoh menyimpan data ke database menggunakan Eloquent ORM (Laravel)
            IndikatorKinerjaKegiatan::create($group);
        }
    }
}
