<?php

namespace App\Http\Controllers;

use App\Models\ProgramKegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramKegiatanController extends BaseController
{
    public function index(Request $request)
    {

        $tahun = $request->input('tahun');

        $data = ProgramKegiatan::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->get();
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = ProgramKegiatan::create([
                'name' => $data->name,
                'tahun' => $data->tahun,
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
            $pegawai = ProgramKegiatan::findOrFail($id);
            $pegawai->update([
                'name' => $data->name,
                'tahun' => $data->tahun,
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
            $data = ProgramKegiatan::find($id);
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
