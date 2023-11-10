<?php

namespace App\Http\Controllers;

use App\Models\Dipa;
use App\Models\Employe;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\MutasiPegawai;
use App\Models\Pengembangan;
use App\Models\Pensiun;
use App\Models\RealisasiAnggaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardKeuanganController extends BaseController
{
    public function index(Request $request)
    {

        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');


        $dipa = Dipa::with('realisasi')->get();
        foreach ($dipa as $key => $value) {
            $value->label = $value->name;
            $result = null;
            for ($i = 1; $i < 13; $i++) {
                $realisasi =  RealisasiAnggaran::where('tahun', $tahun)->where('bulan', $i)->where('dipa_id', $value->id)->first()->realisasi ?? 0;
                $result[] = round(($realisasi / $value->pagu) * 100, 2);
                $value->data = $result;
                $value->backgroundColor = $this->random_color();
            }
        }

        return $this->sendResponse($dipa, 'Data fetched');
    }

    function random_color_part()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }

    function random_color()
    {
        $r = rand(128, 255);
        $g = rand(128, 255);
        $b = rand(128, 255);
        return "rgb(" . $r . "," . $g . "," . $b . ")";
    }
}
