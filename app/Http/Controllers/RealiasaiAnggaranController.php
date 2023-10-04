<?php

namespace App\Http\Controllers;

use App\Models\Dipa;
use App\Models\RealisasiAnggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RealiasaiAnggaranController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

     
        $data = Dipa::with(['realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])
        ->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
        })
        ->get();

        foreach ($data as $key => $value) {
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
     
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        
        $data = json_decode($request->getContent());
        
        try {
            DB::beginTransaction();
            $realisasiAnggaran = RealisasiAnggaran::where('bulan', $data->head->currentMonth)->get();
            foreach ($realisasiAnggaran as $key => $value) {
                $value->delete();
            }

            foreach ($data->detail as $key => $value) {
                $result = RealisasiAnggaran::create([
                    'bulan' => $data->head->currentMonth,
                    'dipa_id' => $value->id,
                    'realisasi' => $value->realisasi_saat_ini,
                    'dp' =>  $value->dp_saat_ini,
                    'created_by' =>  $data->created_by,
                ]);
            }
            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

}
