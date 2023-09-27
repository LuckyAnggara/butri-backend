<?php

namespace App\Http\Controllers;

use App\Models\IndikatorKinerjaUtama;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndikatorKinerjaUtamaController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $tahun = $request->input('tahun');


        $data = IndikatorKinerjaUtama::with('capaian')->when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
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
            $result = IndikatorKinerjaUtama::create([
                'name' => $data->name,
                'target' => $data->target,
                'tahun' => $data->tahun,
                'nomor' => $data->nomor ?? 1,
                'ss_id' => $data->ss_id ?? null,
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
            $pegawai = IndikatorKinerjaUtama::findOrFail($id);
            $pegawai->update([
                'name' => $data->name,
                'target' => $data->target,
                'tahun' => $data->tahun,
                'nomor' => $data->nomor ?? 1,
                'ss_id' => $data->ss_id ?? null,
                'created_by' =>  $data->created_by,
            ]);

            DB::commit();
            return $this->sendResponse($pegawai, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = IndikatorKinerjaUtama::find($id);
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
