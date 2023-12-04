<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPengawasanItwil;
use App\Models\SatuanKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringPengawasanItwilController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $unit = $request->input('unit');


        // $data = SatuanKerja::all();

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

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $exist = MonitoringPengawasanItwil::where('tahun', $data->tahun)->where('bulan', $data->bulan)->where('currency', $data->currency)->where('group_id', $data->group_id)->first();
            if ($exist) {
                $exist->delete();
            }
            $dd = MonitoringPengawasanItwil::create(
                [
                    'bulan' => $data->bulan,
                    'tahun' => $data->tahun,
                    'group_id' => $data->group_id,
                    'currency' => $data->currency ?? 'IDR',
                    'temuan_jumlah' => $data->temuan_jumlah,
                    'temuan_nominal' => $data->temuan_nominal,
                    'tl_jumlah' => $data->tl_jumlah,
                    'tl_nominal' => $data->tl_nominal,
                    'btl_jumlah' => $data->temuan_jumlah - $data->tl_jumlah,
                    'btl_nominal' => $data->temuan_nominal - $data->tl_nominal,
                    'created_by' => $data->created_by
                ]
            );
            DB::commit();
            return $this->sendResponse($dd, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = MonitoringPengawasanItwil::find($id);
            if ($data) {
                $data->delete();
                DB::commit();
                return $this->sendResponse($data, 'Data berhasil dihapus', 200);
            } else {
                return $this->sendError('', 'Data tidak ditemukan', 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }
}
