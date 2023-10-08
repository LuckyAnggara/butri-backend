<?php

namespace App\Http\Controllers;

use App\Models\Dipa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DipaController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $unit = $request->input('unit');

        $data = Dipa::with('group')->when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
        ->when($unit, function ($query, $unit) {
            return $query->where('group_id', $unit);
        })
        ->get();
      
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = Dipa::create([
                'kode' => $data->kode,
                'tahun' => $data->tahun,
                'name' =>  $data->name,
                'pagu' =>  $data->pagu,
                'group_id' =>  $data->group_id,
                'created_by' => $data->created_by,
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
            $result = Dipa::findOrFail($id);
            $result->update([
                'kode' => $data->kode,
                'tahun' => $data->tahun,
                'name' =>  $data->name,
                'group_id'=> $data->group_id,
                'pagu' =>  $data->pagu,
                'created_by' => $data->created_by,
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
            $data = Dipa::find($id);
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
