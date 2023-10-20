<?php

namespace App\Http\Controllers;

use App\Models\PengelolaanMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengelolaanMediaController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $data = PengelolaanMedia::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = PengelolaanMedia::create([
                'keterangan' => $data->keterangan,
                'link' => $data->link,
                'type' => $data->type,
                'bulan' => $data->bulan,
                'tahun' => $data->tahun,
                'created_by' =>  $data->created_by,
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
            $result = PengelolaanMedia::findOrFail($id);
            $result->update([
                'keterangan' => $data->keterangan,
                'link' => $data->link,
                'type' => $data->type,
                'bulan' => $data->bulan,
                'tahun' => $data->tahun,
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
            $data = PengelolaanMedia::find($id);
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
