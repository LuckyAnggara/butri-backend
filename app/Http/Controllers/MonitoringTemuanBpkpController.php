<?php

namespace App\Http\Controllers;

use App\Bpk;
use App\Models\MonitoringTemuanBpkp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringTemuanBpkpController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $data = MonitoringTemuanBpkp::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();

        if (count($data) < 1) {
            $data1 = new Bpk('Sudah Tuntas', '0', '0');
            $data2 = new Bpk('Belum Tuntas ', '0', '0');
            $data3 = new Bpk('Total', '0', '0');
            $data = [$data1, $data2, $data3];
        }

        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = [];
            $exist = MonitoringTemuanBpkp::where('tahun', $data->tahun)->where('bulan', $data->bulan)->get();
            if ($exist) {
                foreach ($exist as $key => $value) {
                    $value->delete();
                }
            }

            foreach ($data->detail as $key => $vv) {
                $result[] = MonitoringTemuanBpkp::create([
                    'tahun' => $data->tahun,
                    'bulan' => $data->bulan,
                    'created_by' => $data->created_by,
                    'keterangan' => $vv->keterangan,
                    'jumlah' => $vv->jumlah,
                    'nominal' => $vv->nominal,
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
