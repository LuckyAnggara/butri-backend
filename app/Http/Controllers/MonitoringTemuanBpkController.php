<?php

namespace App\Http\Controllers;

use App\Models\MonitoringTemuanBpk;
use Illuminate\Http\Request;
use App\Bpk;
use Illuminate\Support\Facades\DB;

class MonitoringTemuanBpkController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $data = MonitoringTemuanBpk::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();
        if (count($data) < 1) {
            $data1 = new Bpk('Sesuai dengan Rekomendasi', '0', '0');
            $data2 = new Bpk('Rekomendasi dalam Proses Reviu BPK ', '0', '0');
            $data3 = new Bpk('Belum Sesuai/Dalam Proses Tindak Lanjut', '0', '0');
            $data4 = new Bpk('Belum Ditindaklanjuti', '0', '0');
            $data5 = new Bpk('Tidak Dapat Ditindaklanjuti dengan Alasan yang Sah', '0', '0');
            $data6 = new Bpk('Temuan Pemeriksaan', '0', '0');
            $data6 = new Bpk('Rekomendasi', '0', '0');

            $data = [$data1, $data2, $data3, $data4, $data5, $data6];
        }

        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = [];
            $exist = MonitoringTemuanBpk::where('tahun', $data->tahun)->where('bulan', $data->bulan)->get();
            if ($exist) {
                foreach ($exist as $key => $value) {
                    $value->delete();
                }
            }

            foreach ($data->detail as $key => $vv) {
                $result[] = MonitoringTemuanBpk::create([
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
