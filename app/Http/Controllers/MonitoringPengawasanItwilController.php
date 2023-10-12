<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPengawasanItwil;
use Illuminate\Http\Request;

class MonitoringPengawasanItwilController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $unit = $request->input('unit');

        $data = MonitoringPengawasanItwil::with('group')->when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->when($unit, function ($query, $unit) {
                return $query->where('group_id', $unit);
            })
            ->get();

        return $this->sendResponse($data, 'Data fetched');
    }
}
