<?php

namespace App\Http\Controllers;

use App\Models\KinerjaKeuangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KinerjaKeuanganController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $results = [];
        for ($i = 1; $i < 13; $i++) {
            $result = KinerjaKeuangan::when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })->whereMonth('created_at', $i)
                ->first();
            if (!$result) {
                $result['bulan'] = $i;
                $result['tahun'] = $tahun;
                $result['capaian_sasaran_program'] = 0;
                $result['penyerapan'] = 0;
                $result['konsistensi'] = 0;
                $result['capaian_output_program'] = 0;
                $result['efisiensi'] = 0;
                $result['nilai_efisiensi'] = 0;
                $result['rata_nka_satker'] = 0;
                // $result->penyerapan = 0;
                // $result->konsistensi = 0;
                // $result->capaian_output_program = 0;
                // $result->efisiensi = 0;
                // $result->nilai_efisiensi = 0;
                // $result->rata_nka_satker = 0;
            }

            $results[] = $result;
        }
        return $this->sendResponse($results, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = KinerjaKeuangan::create([
                'tahun' => $data->tahun,
                'capaian_sasaran_program' => $data->capaian_sasaran_program,
                'penyerapan' =>  $data->penyerapan,
                'konsistensi' =>  $data->konsistensi,
                'capaian_output_program' =>  $data->capaian_output_program,
                'efisiensi' =>  $data->efisiensi,
                'nilai_efisiensi' =>  $data->nilai_efisiensi,
                'rata_nka_satker' =>  $data->rata_nka_satker,
                'created_by' => $data->created_by,
                'created_at' => Carbon::createFromDate($data->tahun, $data->bulan, 1),
            ]);
            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            if ($id == 'new') {
                $result = KinerjaKeuangan::create([
                    'tahun' => $data->tahun,
                    'capaian_sasaran_program' => $data->capaian_sasaran_program,
                    'penyerapan' =>  $data->penyerapan,
                    'konsistensi' =>  $data->konsistensi,
                    'capaian_output_program' =>  $data->capaian_output_program,
                    'efisiensi' =>  $data->efisiensi,
                    'nilai_efisiensi' =>  $data->nilai_efisiensi,
                    'rata_nka_satker' =>  $data->rata_nka_satker,
                    'created_by' => $data->created_by,
                    'created_at' => Carbon::createFromDate($data->tahun, $data->bulan, 1),
                ]);
            } else {
                $result = KinerjaKeuangan::findOrFail($id);
                $result->update([
                    'tahun' => $data->tahun,
                    'capaian_sasaran_program' => $data->capaian_sasaran_program,
                    'penyerapan' =>  $data->penyerapan,
                    'konsistensi' =>  $data->konsistensi,
                    'capaian_output_program' =>  $data->capaian_output_program,
                    'efisiensi' =>  $data->efisiensi,
                    'nilai_efisiensi' =>  $data->nilai_efisiensi,
                    'rata_nka_satker' =>  $data->rata_nka_satker,
                    'created_by' => $data->created_by,
                    'created_at' => Carbon::createFromDate($data->tahun, $data->bulan, 1),
                ]);
            }

            DB::commit();
            return $this->sendResponse($result, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }
}
