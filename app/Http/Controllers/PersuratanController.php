<?php

namespace App\Http\Controllers;

use App\Models\Persuratan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersuratanController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $result = [];
        for ($i = 1; $i < 13; $i++) {
            $bulan = Carbon::createFromDate(2023, $i, 1)->format('F');
            $data = Persuratan::where('tahun', $tahun)->where('bulan', $i)->first();
            if ($data) {
                $result[] = array(
                    'tahun' => $tahun,
                    'bulan_name' =>  $bulan,
                    'bulan' => $i,
                    'surat_masuk' => $data->surat_masuk ?? 0,
                    'surat_keluar' => $data->surat_keluar ?? 0,
                );
            } else {
                $result[] = array(
                    'tahun' => $tahun,
                    'bulan_name' =>   $bulan,
                    'bulan' => $i,
                    'surat_masuk' => 0,
                    'surat_keluar' => 0,
                );
            }
        }

        return $this->sendResponse($result, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $existing = Persuratan::where('tahun', $data->tahun)->where('bulan', $data->bulan)->first();

        if (!$existing) {
            try {
                DB::beginTransaction();
                $result = Persuratan::create([
                    'bulan' => Carbon::create(null, $data->bulan, 1)->format('n'),
                    'tahun' => $data->tahun,
                    'surat_masuk' => $data->surat_masuk,
                    'surat_keluar' => $data->surat_keluar,
                    // 'group_id' => $data->group_id,
                    'created_by' =>  $data->created_by,
                ]);
                DB::commit();
                return $this->sendResponse($result, 'Data berhasil dibuat');
            } catch (\Exception $e) {
                DB::rollBack();
                return $this->sendError($e->getMessage(), 'Failed to saved data');
            }
        } else {
            $response = [
                'success' => true,
                'data'    => 'existing',
                'message' => 'Month already input',
            ];
            return response()->json($response, 202);
        }
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());


        try {
            DB::beginTransaction();
            $result = Persuratan::findOrFail($id);
            $result->update([
                'bulan' =>  Carbon::create(null, $data->bulan, 1)->format('n'),
                'tahun' => $data->tahun,
                'surat_masuk' => $data->surat_masuk,
                'surat_keluar' => $data->surat_keluar,
                'group_id' => $data->group_id,
                'created_by' =>  $data->created_by,
            ]);

            DB::commit();
            return $this->sendResponse($result, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Persuratan::find($id);
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
