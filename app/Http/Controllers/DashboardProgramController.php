<?php

namespace App\Http\Controllers;

use App\Models\CapaianIndikatorKegiatanUtama;
use App\Models\DataPengawasan;
use App\Models\Dipa;
use App\Models\Employe;
use App\Models\IndikatorKinerjaUtama;
use App\Models\JenisPengawasan;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\MutasiPegawai;
use App\Models\Pengembangan;
use App\Models\Pensiun;
use App\Pengawasan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardProgramController extends BaseController
{
     public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $today = Carbon::now(); // Mengambil tanggal hari ini menggunakan Carbon
        $date = $request->input('date');
        

        // ANGGARAN
        $realisasi = Dipa::with(['realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])
            ->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })
            ->get();

        foreach ($realisasi as $key => $value) {
            $total_realisasi = 0;
            foreach ($value->realisasi as $key => $x) {
                $total_realisasi += $x->realisasi;
            }

            $collection = collect($value->realisasi);

            $filteredCollection = $collection->where('bulan',  $bulan)->first();

            $value->dp_saat_ini = $filteredCollection->dp ?? 0;
            $value->realisasi_saat_ini = $filteredCollection->realisasi ?? 0;
            $value->total_realisasi = $total_realisasi -   $value->realisasi_saat_ini;
        }

        // KEPEGAWAIAN

        if($date){
            $date = Carbon::createFromFormat('d M Y', $date);
            $dateQuery = $date->format('Y-m-d 23:59:59');
        }else{
            $dateQuery = $today;
        }
        
        $pegawai = Employe::whereDate('created_at', '<=', $dateQuery)->get();
        $mutasi = MutasiPegawai::whereMonth('created_at',  $dateQuery)->get();
        $pengembangan = Pengembangan::whereMonth('created_at',  $dateQuery)->get();
        $kgb = KenaikanGajiBerkala::whereMonth('created_at',  $dateQuery)->get();
        $kepangkatan = KenaikanPangkat::whereMonth('created_at',  $dateQuery)->get();
        $pensiun = Pensiun::whereMonth('created_at',  $dateQuery)->get();

        $kepegawaian = [
            'pegawai' => $pegawai->count(),
            'laki' => $pegawai->where('gender', 'LAKI LAKI')->count(),
            'perempuan' => $pegawai->where('gender', 'PEREMPUAN')->count(),
            'mutasi' => $mutasi->count(),
            'pengembangan' => $pengembangan->count(),
            'kgb' => $kgb->count(),
            'kepangkatan' => $kepangkatan->count(),
            'pensiun' => $pensiun->count(),
        ];

        // DATA PENGAWASAN

        $jenisPengawasan = JenisPengawasan::all();
        $dataPengawasan = [];

        foreach ($jenisPengawasan as $key => $value) {
            $result = DataPengawasan::where('jenis_pengawasan_id', $value->id)->where('tahun',  $tahun)
                ->when($bulan, function ($query, $bulan) {
                    return $query->whereMonth('created_at', '<=', $bulan);
                })
                ->get()->count();
            $dataPengawasan[] = new Pengawasan($value->name, $result);
        }


        // CAPAIAN IKU

        $capaianIKU = IndikatorKinerjaUtama::when($tahun, function ($query, $tahun) {
            return $query->whereYear('created_at', $tahun);
        })
            ->get();

        foreach ($capaianIKU as $key => $value) {
            $value->realisasi = CapaianIndikatorKegiatanUtama::where('iku_id', $value->id)->when($tahun, function ($query, $tahun) {
                return $query->whereYear('created_at', $tahun);
            })
            ->first();
        }




        $result = [
            'dataRealisasi' => $realisasi,
            'dataKepegawaian' => $kepegawaian,
            'dataPengawasan' => $dataPengawasan,
            'dataCapaianIku'=> $capaianIKU,
        ];
      
        return $this->sendResponse($result, 'Data fetched');
    }
}
