<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'ANALIS ANGGARAN AHLI MUDA',
            'ANALIS ANGGARAN AHLI PERTAMA',
            'ANALIS HUKUM AHLI PERTAMA',
            'ANALIS JABATAN',
            'ANALIS KEBIJAKAN AHLI MUDA',
            'ANALIS KELEMBAGAAN',
            'ANALIS KEPEGAWAIAN AHLI MADYA',
            'ANALIS KEPEGAWAIAN AHLI MUDA',
            'ANALIS KEPEGAWAIAN AHLI PERTAMA',
            'ANALIS LAPORAN HASIL PENGAWASAN',
            'ANALIS PENGADUAN MASYARAKAT',
            'ANALIS PENGELOLAAN KEUANGAN APBN AHLI MADYA',
            'ANALIS PENGELOLAAN KEUANGAN APBN AHLI MUDA',
            'ANALIS PERENCANAAN, PENGGUNAAN DAN PENGHAPUSAN BMN',
            'ANALIS SISTEM APLIKASI DAN JARINGAN KOMPUTER',
            'ANALIS STANDARISASI SARANA KERJA',
            'ARSIPARIS AHLI MUDA',
            'ARSIPARIS AHLI PERTAMA',
            'AUDITOR MADYA',
            'AUDITOR MUDA',
            'AUDITOR PELAKSANA',
            'AUDITOR PELAKSANA LANJUTAN',
            'AUDITOR PENYELIA',
            'AUDITOR PERTAMA',
            'AUDITOR UTAMA',
            'BENDAHARA PENGELUARAN PUSAT',
            'INSPEKTUR JENDERAL',
            'INSPEKTUR WILAYAH II',
            'INSPEKTUR WILAYAH III',
            'INSPEKTUR WILAYAH IV',
            'INSPEKTUR WILAYAH V',
            'INSPEKTUR WILAYAH VI',
            'KASUBAG RUMAH TANGGA',
            'KASUBAG TATA USAHA',
            'KASUBBAG TATA USAHA PIMPINAN DAN PROTOKOL',
            'KEPALA BAGIAN PROGRAM DAN PELAPORAN',
            'KEPALA BAGIAN UMUM',
            'KEPALA SUBBAG TATA USAHA',
            'KUSTODIAN KEKAYAAN NEGARA',
            'PEMROSES MUTASI KEPEGAWAIAN',
            'PENATA KEUANGAN',
            'PENGELOLA BARANG MILIK NEGEARA',
            'PENGELOLA DATA',
            'PENGELOLA DATA ANGGARAN',
            'PENGELOLA DATA KEPEGAWAIAN',
            'PENGELOLA HASIL KERJA',
            'PENGELOLA KEUANGAN',
            'PENGELOLA PENGADAAN BARANG/JASA PERTAMA',
            'PENGELOLA PROGRAM DAN KEGIATAN',
            'PENGELOLA TATA NASKAH',
            'PENGELOLA TEKNOLOGI INFORMASI',
            'PENGOLAH DATA ANGGARAN',
            'PENGOLAH DATA KELEMBAGAAN',
            'PENGOLAH DATA KERJASAMA',
            'PENGOLAH DATA LAPORAN SISTEM APLIKASI DAN DATABASE',
            'PENYUSUN BAHAN KERJA SAMA',
            'PENYUSUN INFORMASI HUKUM',
            'PENYUSUN LAPORAN DAN HASIL EVALUASI',
            'PENYUSUN LAPORAN KEUANGAN',
            'PENYUSUN RENCANA KERJA DAN ANGGARAN',
            'PERENCANA AHLI MUDA',
            'PETUGAS PROTOKOL',
            'PRANATA HUBUNGAN MASYARAKAT AHLI MUDA',
            'PRANATA HUBUNGAN MASYARAKAT PERTAMA',
            'PRANATA KOMPUTER AHLI MADYA',
            'PRANATA KOMPUTER AHLI MUDA',
            'PRANATA KOMPUTER PERTAMA',
            'SEKRETARIS INSPEKTORAT JENDERAL',
            'SEKRETARIS PIMPINAN',
        ];

        // Mengurutkan data berdasarkan abjad
        sort($data);

        // Format dan insert data ke database
        foreach ($data as $unitName) {
            Jabatan::insert([
                'name' => $unitName
            ]);
        }
    }
}
