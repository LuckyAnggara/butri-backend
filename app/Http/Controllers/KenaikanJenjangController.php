<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\KenaikanJenjang;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanJenjangController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = KenaikanJenjang::select('kenaikan_jenjangs.*')->with('pegawai', 'jabatan', 'jabatan_new')
            ->join('employes', 'employes.id', '=', 'kenaikan_jenjangs.employe_id')
            ->when($name, function ($query, $name) {
                return $query->where('employes.name', 'like', '%' . $name . '%')
                    ->orWhere('employes.nip', 'like', '%' . $name . '%')
                    ->orWhere('kenaikan_jenjangs.nomor_sk', 'like', '%' . $name . '%')
                    ->orWhere('kenaikan_jenjangs.notes', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('kenaikan_jenjangs.tmt_jabatan', [$startDate, $endDate]);
            })
            ->orderBy('kenaikan_jenjangs.tmt_jabatan', 'asc')
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
                $item = KenaikanJenjang::create([
                    'nomor_sk' => $data->nomor_sk,
                    'notes' => $data->notes,
                    'employe_id' => $value->id,
                    'jabatan_id' => $value->jabatan_id,
                    'jabatan_new_id' => $value->jabatan_new_id,
                    'created_by' =>  $data->created_by,
                    'tmt_jabatan' => Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d'),
                ]);

                $pegawai = Employe::find($value->id);
                $pegawai->tmt_jabatan = Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d');
                $pegawai->jabatan_id = $item->jabatan_new_id;
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
            $data = KenaikanJenjang::find($id);
            if ($data) {

                $employe = Employe::find($data->employe_id);
                $employe->jabatan_id = $data->jabatan_id;
                $employe->save();

                if ($employe) {
                    $data->delete();
                }
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
