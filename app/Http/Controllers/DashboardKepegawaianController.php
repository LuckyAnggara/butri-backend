<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\MutasiPegawai;
use App\Models\Pengembangan;
use App\Models\Pensiun;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardKepegawaianController extends BaseController
{
    public function index(Request $request)
    {

        $today = Carbon::now(); // Mengambil tanggal hari ini menggunakan Carbon
        $date = $request->input('date');
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

        $data = [
            'pegawai' => $pegawai->count(),
            'laki' => $pegawai->where('gender', 'LAKI LAKI')->count(),
            'perempuan' => $pegawai->where('gender', 'PEREMPUAN')->count(),
            'mutasi' => $mutasi->count(),
            'pengembangan' => $pengembangan->count(),
            'kgb' => $kgb->count(),
            'kepangkatan' => $kepangkatan->count(),
            'pensiun' => $pensiun->count(),
        ];

        return $this->sendResponse($data, 'Data fetched');
    }
}
