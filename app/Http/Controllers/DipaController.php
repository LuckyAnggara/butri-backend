<?php

namespace App\Http\Controllers;

use App\Models\Dipa;
use Illuminate\Http\Request;

class DipaController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $data = Dipa::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            // ->when($tahun, function ($query, $bulan) {
            //     return $query->where('bulan', $bulan);
            // })
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
                $query->orWhere('kode', 'like', '%' . $name . '%');
            })
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
}
