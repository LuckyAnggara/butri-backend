<?php

namespace App\Http\Controllers;

use App\Models\DataPengawasan;
use App\Models\Dipa;
use App\Models\GroupUnit;
use App\Models\JenisPengawasan;
use App\Models\Unit;
use App\Pengawasan;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardWilayahController extends BaseController
{
   public function index(Request $request)
    {

        $today = Carbon::now();
        $unit_id = $request->input('unit');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $unit = Unit::find($unit_id);

         // Mengambil tanggal hari ini menggunakan Carbon
         // Mengambil tanggal hari ini menggunakan Carbon
         $jenisPengawasan = JenisPengawasan::all(); 
         $dataPengawasan = [];

         foreach ($jenisPengawasan as $key => $value) {
          
            $result = DataPengawasan::where('jenis_pengawasan_id', $value->id)->where('unit_id', $unit_id)->whereYear('created_at', '<=', $today)->get()->count();
          $dataPengawasan[]= new Pengawasan($value->name, $result);
        }
                 
        $realisasiAnggaran = Dipa::where('group_id', $unit->group_id)->with(['realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])
        ->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
        })
        ->first();
        $realisasiAnggaran->total_realisasi = 0;
         foreach ($realisasiAnggaran->realisasi as $key => $x) {
                $realisasiAnggaran->total_realisasi += $x->realisasi;
         };

            
        $data = [
           'pengawasan' => $dataPengawasan,
           'anggaran' => $realisasiAnggaran,
        ]; 
     


        return $this->sendResponse($data, 'Data fetched');
    } 
}
