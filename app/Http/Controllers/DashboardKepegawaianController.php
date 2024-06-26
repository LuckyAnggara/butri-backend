<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Jabatan;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\MutasiPegawai;
use App\Models\Pangkat;
use App\Models\Pengembangan;
use App\Models\Pensiun;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardKepegawaianController extends BaseController
{
    public function index(Request $request)
    {

        $today = Carbon::now(); // Mengambil tanggal hari ini menggunakan Carbon
        $date = $request->input('date');
        $group = $request->input('group');
        $unit = $request->input('unit');

        if ($date) {
            $date = Carbon::createFromFormat('d M Y', $date);
            $dateQuery = $date->format('Y-m-d 23:59:59');
        } else {
            $dateQuery = $today;
        }


        // if ($group) {
        //     $pegawai = Employe::whereHas('unit', function ($query) use ($group) {
        //         $query->where('group_id', $group);
        //     })->get();
        // } else {
        //     $pegawai = Employe::all();
        // }



        $pegawai = Employe::when($unit, function ($query, $unit) {
            return $query->where('unit_id', $unit);
        })->where(function ($query) {
            $query->WhereNull('tmt_pensiun')
                ->orWhereNot('tmt_pensiun', '<=', Carbon::today());
        })->get();

        // $mutasi = MutasiPegawai::whereMonth('created_at',  $dateQuery)->get();
        // $pengembangan = Pengembangan::whereMonth('created_at',  $dateQuery)->get();
        // $kgb = KenaikanGajiBerkala::whereMonth('created_at',  $dateQuery)->get();
        // $kepangkatan = KenaikanPangkat::whereMonth('created_at',  $dateQuery)->get();
        // $pensiun = Pensiun::whereMonth('created_at',  $dateQuery)->get();

        $data = [
            'pegawai' => $pegawai->count(),
            'laki' => $pegawai->where('gender', 'LAKI LAKI')->count(),
            'perempuan' => $pegawai->where('gender', 'PEREMPUAN')->count(),
            // 'mutasi' => $mutasi->count(),
            // 'pengembangan' => $pengembangan->count(),
            // 'kgb' => $kgb->count(),
            // 'kepangkatan' => $kepangkatan->count(),
            // 'pensiun' => $pensiun->count(),
        ];

        $pangkat = Pangkat::all();

        $jabatan = Jabatan::all();

        foreach ($pangkat as $key => $value) {
            $count = Employe::where('pangkat_id', $value->id)->whereDate('created_at', '<=', $dateQuery)->when($unit, function ($query, $unit) {
                return $query->where('unit_id', $unit);
            })->count();
            $value->jumlah = $count;
        }

        foreach ($jabatan as $key => $value) {
            $count = Employe::where('jabatan_id', $value->id)->whereDate('created_at', '<=', $dateQuery)->when($unit, function ($query, $unit) {
                return $query->where('unit_id', $unit);
            })->count();
            $value->jumlah = $count;
        }

        return $this->sendResponse(['data' => $data, 'pangkat' => $pangkat, 'jabatan' => $jabatan], 'Data fetched');
    }
}
