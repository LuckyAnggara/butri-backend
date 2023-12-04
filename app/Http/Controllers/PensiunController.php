<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Pensiun;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PensiunController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = Pensiun::select('pensiuns.*', 'employes.name')
            ->join('employes', 'employes.id', '=', 'pensiuns.employe_id')
            ->when($name, function ($query, $name) {
                return $query->where('employes.name', 'like', '%' . $name . '%')
                    ->orWhere('employes.nip', 'like', '%' . $name . '%')
                    ->orWhere('pensiuns.nomor_sk', 'like', '%' . $name . '%')
                    ->orWhere('pensiuns.tentang', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('pensiuns.created_at', [$startDate, $endDate]);
            })
            ->orderBy('employes.nip', 'asc')
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
                $result[] = Pensiun::create([
                    'nomor_sk' => $data->nomor_sk,
                    'tentang' => $data->tentang,
                    'employe_id' => $value->id,
                    'notes' =>  $data->notes,
                    'created_by' =>  $data->created_by,
                    'tmt_pensiun' => Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d'),
                ]);

                $pegawai = Employe::find($value->id);
                $pegawai->tmt_pensiun = Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d');
                $pegawai->save();
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
            $data = Pensiun::find($id);
            if ($data) {
                $pegawai = Employe::find($data->employe_id);
                $pegawai->tmt_pensiun = null;
                $pegawai->save();

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
