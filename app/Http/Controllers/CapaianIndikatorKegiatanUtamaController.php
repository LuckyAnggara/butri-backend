<?php

namespace App\Http\Controllers;

use App\Models\CapaianIndikatorKegiatanUtama;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapaianIndikatorKegiatanUtamaController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $tahun = $request->input('tahun');

        $data = CapaianIndikatorKegiatanUtama::with('iku')
            ->when($tahun, function ($query, $tahun) {
                return $query->whereYear('created_at', $tahun);
            })
            ->when($name, function ($query, $name) {
                return $query->where('realisasi', 'like', '%' . $name . '%');
                $query->orWhere('analisa', 'like', '%' . $name . '%');
            })
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = CapaianIndikatorKegiatanUtama::create([
                'iku_id' => $data->iku->id,
                'realisasi' => $data->realisasi,
                'analisa' => $data->analisa,
                'kegiatan' => $data->kegiatan,
                'kendala' => $data->kendala,
                'hambatan' => $data->hambatan,
                'created_by' =>  $data->created_by,
            ]);
            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function show($id)
    {
        $result = CapaianIndikatorKegiatanUtama::where('id', $id)
            ->with('iku')
            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = CapaianIndikatorKegiatanUtama::findOrFail($id);
            $result->update([
                'iku_id' => $data->iku->id,
                'realisasi' => $data->realisasi,
                'analisa' => $data->analisa,
                'kegiatan' => $data->kegiatan,
                'kendala' => $data->kendala,
                'hambatan' => $data->hambatan,
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
            $data = CapaianIndikatorKegiatanUtama::find($id);
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
