<?php

namespace App\Http\Controllers;

use App\Models\Dipa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DipaController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

     
        $data = Dipa::with(['realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])     
        ->get();
        foreach ($data as $key => $value) {
            $total_realisasi = 0;
            foreach ($value->realisasi as $key => $x) {
                $total_realisasi += $x->realisasi;
            }

            $collection = collect($value->realisasi);

            $filteredCollection = $collection->where('bulan',  $bulan)->first();

            $value->total_realisasi = $total_realisasi;
            $value->dp_saat_ini = $filteredCollection->dp ?? 0;
            $value->realisasi_saat_ini = $filteredCollection->realisasi ?? 0;
           
        }
     
          
        
    
        
        // ->when($tahun, function ($query, $tahun) {
        //     return $query->where('tahun', $tahun);
        // })
        //     // ->when($tahun, function ($query, $bulan) {
        //     //     return $query->where('bulan', $bulan);
        //     // })
        //     ->when($name, function ($query, $name) {
        //         return $query->where('name', 'like', '%' . $name . '%');
        //         $query->orWhere('kode', 'like', '%' . $name . '%');
        //     })
        //     ->latest()
        //     ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
}
