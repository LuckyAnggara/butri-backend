<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\KenaikanPangkat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanPangkatController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = KenaikanPangkat::select('kenaikan_pangkats.*')->with('pegawai', 'pangkat', 'pangkat_new')
            ->join('employes', 'employes.id', '=', 'kenaikan_pangkats.employe_id')
            ->when($name, function ($query, $name) {
                return $query->where('employes.name', 'like', '%' . $name . '%')
                    ->orWhere('employes.nip', 'like', '%' . $name . '%')
                    ->orWhere('kenaikan_pangkats.nomor_sk', 'like', '%' . $name . '%')
                    ->orWhere('kenaikan_pangkats.notes', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('kenaikan_pangkats.tmt_pangkat', [$startDate, $endDate]);
            })
            ->orderBy('kenaikan_pangkats.tmt_pangkat', 'asc')
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
                $item = KenaikanPangkat::create([
                    'nomor_sk' => $data->nomor_sk,
                    'notes' => $data->notes,
                    'employe_id' => $value->id,
                    'pangkat_id' => $value->pangkat_id,
                    'pangkat_new_id' => $value->pangkat_new_id,
                    'created_by' =>  $data->created_by,
                    'tmt_pangkat' => Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d'),
                ]);

                $pegawai = Employe::find($value->id);
                $pegawai->tmt_pangkat = Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d');
                $pegawai->pangkat_id = $item->pangkat_new_id;
                $pegawai->save();
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
            $data = KenaikanPangkat::find($id);
            if ($data) {
                $pegawai = Employe::find($data->employe_id);

                $pegawai->pangkat_id = $data->pangkat_id;
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
