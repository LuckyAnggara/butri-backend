<?php

namespace App\Http\Controllers;

use App\Models\Ikpa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IkpaController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $data = Ikpa::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->first();
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $ikpa = Ikpa::where('tahun', $data->tahun)->where('bulan', $data->bulan)->first();
            if ($ikpa) {
                $ikpa->delete();
            }
            $ikpa = Ikpa::create(
                [
                    'bulan' => $data->bulan,
                    'tahun' => $data->tahun,
                    'revisi_dipa' => $data->revisi_dipa,
                    'halaman_tiga_dipa' => $data->halaman_tiga_dipa,
                    'penyerapan_anggaran' => $data->penyerapan_anggaran,
                    'belanja_kontraktual' => $data->belanja_kontraktual,
                    'penyelesaian_tagihan' => $data->penyelesaian_tagihan,
                    'pengelolaan_up_tup' => $data->pengelolaan_up_tup,
                    'dispensasi_spm' => $data->dispensasi_spm,
                    'capaian_output' => $data->capaian_output,
                    'created_by' => $data->created_by
                ]
            );
            DB::commit();
            return $this->sendResponse($ikpa, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }
}
