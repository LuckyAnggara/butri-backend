<?php

namespace Database\Seeders;

use App\Models\SatuanKerja;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SatuanKerjaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $eselonI = [
            [
                'nama' => 'Inspektorat Jenderal',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Sekretariat Jenderal',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Direktorat Jenderal Administrasi Hukum Umum',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Direktorat Jenderal Kekayaan Intelektual',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Direktorat Jenderal Hak Asasi Manusia',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Direktorat Jenderal Imigrasi',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Direktorat Jenderal Pemasyarakatan',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Direktorat Jenderal Peraturan Perundang-Undangan',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Badan Pengembangan Sumber Daya Manusia Hukum dan HAM',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Badan Strategi Kebijakan Hukum dan HAM',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
            [
                'nama' => 'Badan Pebinaan Hukum Nasional Hukum dan HAM',
                'tingkat' => "ESELON I",
                'keterangan' => '',
            ],
        ];

        $kantorWilayah = [
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM ACEH',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SUMATERA UTARA',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SUMATERA BARAT',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM RIAU',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM JAMBI',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SUMATERA SELATAN',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM KEPULAUAN BANGKA BELITUNG',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM BENGKULU',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM LAMPUNG',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM DKI JAKARTA',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM JAWA BARAT',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM BANTEN',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM JAWA TENGAH',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM  D.I. YOGYAKARTA',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM JAWA TIMUR',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM KALIMANTAN BARAT',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM KALIMANTAN TENGAH',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM KALIMANTAN TIMUR',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM KALIMANTAN SELATAN',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM BALI',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM NUSA TENGGARA BARAT',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM NUSA TENGGARA TIMUR',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SULAWESI SELATAN',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SULAWESI TENGAH',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SULAWESI UTARA',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM GORONTALO',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SULAWESI TENGGARA',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM MALUKU',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM MALUKU UTARA',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM PAPUA',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM PAPUA BARAT',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM KEPULAUAN RIAU',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            [
                'nama' => 'KANTOR WILAYAH KEMENTERIAN HUKUM DAN HAM SULAWESI BARAT',
                'tingkat' => "ESELON II",
                'keterangan' => '',
            ],
            // Tambahkan data kantor wilayah lainnya sesuai kebutuhan
        ];
        foreach ($eselonI as $key => $value) {
            SatuanKerja::insert($value);
        }
        foreach ($kantorWilayah as $key => $value) {
            SatuanKerja::insert($value);
        }
    }
}
