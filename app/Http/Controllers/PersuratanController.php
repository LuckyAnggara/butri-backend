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
        $bulan = $request->input('bulan');
        $group = $request->input('unit');

        $data = Persuratan::with('group')->when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($group, function ($query, $group) {
                return $query->where('group_id', $group);
            })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->orderBy('bulan', 'asc')
            ->get();

        foreach ($data as  $value) {
            $bulan = Carbon::createFromDate(2023, $value->bulan, 1)->format('F');
            $value->bulan_name = $bulan;
        }

        for ($i = 1; $i <= 12; $i++) {
        }

        return $this->sendResponse($data, 'Data fetched');
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
                    'group_id' => $data->group_id,
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
