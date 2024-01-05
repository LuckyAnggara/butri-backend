<?php

namespace App\Http\Controllers;

use App\Models\PenetapanTujuanRisiko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenetapanTujuanRisikoController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');

        $data = PenetapanTujuanRisiko::with('program', 'sasaran', 'iku')->when($name, function ($query, $name) {
            return $query->where('permasalahan', 'like', '%' . $name . '%');
        })
            ->orderBy('created_at', 'asc')
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {

            DB::beginTransaction();
            $result = PenetapanTujuanRisiko::create([
                'tahun' => $data->tahun,
                'program_kegiatan_id' => $data->program_kegiatan_id,
                'sasaran_kementerian_id' => $data->sasaran_kementerian_id,
                'iku_id' => $data->iku_id,
                'permasalahan' => $data->permasalahan,
                'group_id' => Auth::user()->group_id,
                'created_by' => Auth::id()
            ]);

            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = PenetapanTujuanRisiko::find($id);
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
