<?php

namespace App\Http\Controllers;

use App\Models\CapaianProgramUnggulan;
use App\Models\Kegiatan;
use App\Models\ProgramUnggulan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramUnggulanController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('name');
        $date = $request->input('date');


        $data = ProgramUnggulan::when($date, function ($query, $date) {
            return $query->where('tahun', $date);
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
            $result = ProgramUnggulan::create([
                'name' => $data->name,
                'target' => $data->target,
                'tahun' => $data->tahun,
                'satuan' => $data->satuan ?? '',
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
            $program = ProgramUnggulan::findOrFail($id);
            $program->update([
                'name' => $data->name,
                'target' => $data->target,
                'tahun' => $data->tahun,
                'satuan' => $data->satuan ?? '',
            ]);

            DB::commit();
            return $this->sendResponse($program, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = ProgramUnggulan::find($id);
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
