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
            'ANALIS ANGGARAN AHLI MUDA|PEJABAT FUNGSIONAL ANALIS ANGGARAN',
            'ANALIS ANGGARAN AHLI PERTAMA|PEJABAT FUNGSIONAL ANALIS ANGGARAN',
            'ANALIS HUKUM AHLI PERTAMA|PEJABAT FUNGSIONAL ANALIS HUKUM',
            'ANALIS JABATAN|PEJABAT FUNGSIONAL ANALIS JABATAN',
            'ANALIS KEBIJAKAN AHLI MUDA|PEJABAT FUNGSIONAL ANALIS KEBIJAKAN',
            'ANALIS KELEMBAGAAN|PEJABAT FUNGSIONAL ANALIS KELEMBAGAAN',
            'ANALIS KEPEGAWAIAN AHLI MADYA|PEJABAT FUNGSIONAL ANALIS KEPEGAWAIAN',
            'ANALIS KEPEGAWAIAN AHLI MUDA|PEJABAT FUNGSIONAL ANALIS KEPEGAWAIAN',
            'ANALIS KEPEGAWAIAN AHLI PERTAMA|PEJABAT FUNGSIONAL ANALIS KEPEGAWAIAN',
            'ANALIS LAPORAN HASIL PENGAWASAN|PEJABAT FUNGSIONAL JABATAN PELAKSANA',
            'ANALIS PENGADUAN MASYARAKAT|PEJABAT FUNGSIONAL JABATAN PELAKSANA',
            'ANALIS PENGELOLAAN KEUANGAN APBN AHLI MADYA|PEJABAT FUNGSIONAL ANALIS PENGELOLAAN',
            'ANALIS PENGELOLAAN KEUANGAN APBN AHLI MUDA|PEJABAT FUNGSIONAL ANALIS PENGELOLAAN',
            'ANALIS PERENCANAAN, PENGGUNAAN DAN PENGHAPUSAN BMN|PEJABAT FUNGSIONAL JABATAN PELAKSANA',
            'ANALIS SISTEM APLIKASI DAN JARINGAN KOMPUTER|PEJABAT FUNGSIONAL JABATAN PELAKSANA',
            'ANALIS STANDARISASI SARANA KERJA|PEJABAT FUNGSIONAL JABATAN PELAKSANA',
            'ARSIPARIS AHLI MUDA|PEJABAT FUNGSIONAL ARSIPARIS',
            'ARSIPARIS AHLI PERTAMA|PEJABAT FUNGSIONAL ARSIPARIS',
            'AUDITOR MADYA|PEJABAT FUNGSIONAL AUDITOR',
            'AUDITOR MUDA|PEJABAT FUNGSIONAL AUDITOR',
            'AUDITOR PELAKSANA|PEJABAT FUNGSIONAL AUDITOR',
            'AUDITOR PELAKSANA LANJUTAN|PEJABAT FUNGSIONAL AUDITOR',
            'AUDITOR PENYELIA|PEJABAT FUNGSIONAL AUDITOR',
            'AUDITOR PERTAMA|PEJABAT FUNGSIONAL AUDITOR',
            'AUDITOR UTAMA|PEJABAT FUNGSIONAL AUDITOR',
            'BENDAHARA PENGELUARAN PUSAT|JABATAN PELAKSANA',
            'INSPEKTUR JENDERAL|PIMPINAN TINGGI MADYA',
            'INSPEKTUR WILAYAH II|PIMPINAN TINGGI PRATAMA',
            'INSPEKTUR WILAYAH III|PIMPINAN TINGGI PRATAMA',
            'INSPEKTUR WILAYAH IV|PIMPINAN TINGGI PRATAMA',
            'INSPEKTUR WILAYAH V|PIMPINAN TINGGI PRATAMA',
            'INSPEKTUR WILAYAH VI|PIMPINAN TINGGI PRATAMA',
            'KASUBAG RUMAH TANGGA|PEJABAT PENGAWAS',
            'KASUBAG TATA USAHA|PEJABAT PENGAWAS',
            'KASUBBAG TATA USAHA PIMPINAN DAN PROTOKOL|PEJABAT PENGAWAS',
            'KEPALA BAGIAN PROGRAM DAN PELAPORAN|PEJABAT ADMINISTRATOR',
            'KEPALA BAGIAN UMUM|PEJABAT ADMINISTRATOR',
            'KEPALA SUBBAG TATA USAHA|PEJABAT PENGAWAS',
            'KUSTODIAN KEKAYAAN NEGARA|JABATAN PELAKSANA',
            'PEMROSES MUTASI KEPEGAWAIAN|JABATAN PELAKSANA',
            'PENATA KEUANGAN|JABATAN PELAKSANA',
            'PENGELOLA BARANG MILIK NEGEARA|JABATAN PELAKSANA',
            'PENGELOLA DATA|JABATAN PELAKSANA',
            'PENGELOLA DATA ANGGARAN|JABATAN PELAKSANA',
            'PENGELOLA DATA KEPEGAWAIAN|JABATAN PELAKSANA',
            'PENGELOLA HASIL KERJA|JABATAN PELAKSANA',
            'PENGELOLA KEUANGAN|JABATAN PELAKSANA',
            'PENGELOLA PENGADAAN BARANG/JASA PERTAMA|JABATAN PELAKSANA',
            'PENGELOLA PROGRAM DAN KEGIATAN|JABATAN PELAKSANA',
            'PENGELOLA TATA NASKAH|JABATAN PELAKSANA',
            'PENGELOLA TEKNOLOGI INFORMASI|JABATAN PELAKSANA',
            'PENGOLAH DATA ANGGARAN|JABATAN PELAKSANA',
            'PENGOLAH DATA KELEMBAGAAN|JABATAN PELAKSANA',
            'PENGOLAH DATA KERJASAMA|JABATAN PELAKSANA',
            'PENGOLAH DATA LAPORAN SISTEM APLIKASI DAN DATABASE|JABATAN PELAKSANA',
            'PENYUSUN BAHAN KERJA SAMA|JABATAN PELAKSANA',
            'PENYUSUN INFORMASI HUKUM|JABATAN PELAKSANA',
            'PENYUSUN LAPORAN DAN HASIL EVALUASI|JABATAN PELAKSANA',
            'PENYUSUN LAPORAN KEUANGAN|JABATAN PELAKSANA',
            'PENYUSUN RENCANA KERJA DAN ANGGARAN|JABATAN PELAKSANA',
            'PERENCANA AHLI MUDA|PEJABAT FUNGSIONAL PERENCANA',
            'PETUGAS PROTOKOL|JABATAN PELAKSANA',
            'PRANATA HUBUNGAN MASYARAKAT AHLI MUDA|PEJABAT FUNGSIONAL PRANATA HUBUNGAN MASYRAKAT',
            'PRANATA HUBUNGAN MASYARAKAT PERTAMA|PEJABAT FUNGSIONAL PRANATA HUBUNGAN MASYRAKAT',
            'PRANATA KOMPUTER AHLI MADYA|PEJABAT FUNGSIONAL PRANATA KOMPUTER',
            'PRANATA KOMPUTER AHLI MUDA|PEJABAT FUNGSIONAL PRANATA KOMPUTER',
            'PRANATA KOMPUTER PERTAMA|PEJABAT FUNGSIONAL PRANATA KOMPUTER',
            'SEKRETARIS INSPEKTORAT JENDERAL|PIMPINAN TINGGI PRATAMA',
            'SEKRETARIS PIMPINAN|JABATAN PELAKSANA',
            'INSPEKTUR WILAYAH I|PIMPINAN TINGGI PRATAMA',
            'DRIVER|PPNPN',
            'SATPAM|PPNPN',
            'PRAMUBAKTI|PPNPN',
            'TENAGA KEBERSIHAN|PPNPN',
            'HELPER|PPNPN',
            'CPNS|CPNS',
        ];

        foreach ($data as $item) {
            list($name, $group) = explode('|', $item);
            Jabatan::create([
                'name' => trim($name),
                'group' => trim($group),
            ]);
        }
    }
}
