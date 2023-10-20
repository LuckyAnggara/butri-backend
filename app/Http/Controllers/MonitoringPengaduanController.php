<?php

namespace App\Http\Controllers;

use App\Models\MonitoringPengaduan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringPengaduanController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $eselon = $request->input('eselon');

        $data = MonitoringPengaduan::select('monitoring_pengaduans.*')->with('satker')
            ->join('satuan_kerjas', 'satuan_kerjas.id', '=', 'monitoring_pengaduans.satker_id')
            ->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->when($eselon, function ($query, $eselon) {
                return $query->where('satuan_kerjas.tingkat', $eselon);
            })
            ->get();
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $exist = MonitoringPengaduan::where('tahun', $data->tahun)->where('bulan', $data->bulan)->where('satker_id', $data->satker_id)->first();
            if ($exist) {
                $exist->delete();
            }
            $dd = MonitoringPengaduan::create(
                [
                    'bulan' => $data->bulan,
                    'tahun' => $data->tahun,
                    'satker_id' => $data->satker_id,
                    'wbs' => $data->wbs,
                    'kotak_pengaduan' => $data->kotak_pengaduan,
                    'aplikasi_lapor' => $data->aplikasi_lapor,
                    'media_sosial' => $data->media_sosial,
                    'surat_pos' => $data->surat_pos,
                    'website' => $data->website,
                    'sms_gateway' => $data->sms_gateway,
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
            $data = MonitoringPengaduan::find($id);
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
