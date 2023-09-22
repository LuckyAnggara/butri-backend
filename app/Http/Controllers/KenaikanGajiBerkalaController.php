<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\KenaikanGajiBerkala;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanGajiBerkalaController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = KenaikanGajiBerkala::select('kenaikan_gaji_berkalas.*')->with('pegawai.jabatan')
            ->join('employes', 'employes.id', '=', 'kenaikan_gaji_berkalas.employe_id')
            ->when($name, function ($query, $name) {
                return $query->where('employes.name', 'like', '%' . $name . '%')
                    ->orWhere('employes.nip', 'like', '%' . $name . '%')
                    ->orWhere('kenaikan_gaji_berkalas.nomor_sk', 'like', '%' . $name . '%')
                    ->orWhere('kenaikan_gaji_berkalas.notes', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('kenaikan_gaji_berkalas.tmt_gaji', [$startDate, $endDate]);
            })
            ->orderBy('kenaikan_gaji_berkalas.tmt_gaji', 'asc')
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = [];
            foreach ($data->list as $key => $value) {
                $item = KenaikanGajiBerkala::create([
                    'nomor_sk' => $data->nomor_sk,
                    'notes' => $data->notes,
                    'employe_id' => $value->id,
                    'created_by' =>  $data->created_by,
                    'tmt_gaji' => Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d'),
                ]);
                $result[] = $item;
            }
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
            $data = KenaikanGajiBerkala::find($id);
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
