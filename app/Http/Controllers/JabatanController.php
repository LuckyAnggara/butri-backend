<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JabatanController extends BaseController
{
    public function index(Request $request)
    {
        $data = Jabatan::with('pegawai')->get();

        foreach ($data as $key => $value) {
            $value->jumlah_pegawai = $value->pegawai->count();
        }
        return $this->sendResponse($data, 'Data fetched');
    }

        public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = Jabatan::create([
                'name' => Str::upper($data->name),
                'group' => Str::upper($data->group),
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
        $result = Jabatan::where('id', $id)->first();
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
         
            $jabatan = Jabatan::findOrFail($id);
            $jabatan->update([
                'name' => Str::upper($data->name),
                'group' => Str::upper($data->group),
              
            ]);
            DB::commit();
            return $this->sendResponse($jabatan, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Jabatan::find($id);
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
