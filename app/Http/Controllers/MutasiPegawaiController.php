<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\MutasiPegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MutasiPegawaiController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = MutasiPegawai::select('mutasi_pegawais.*')->with('pegawai', 'jabatan', 'jabatan_new', 'unit', 'unit_new')
            ->join('employes', 'employes.id', '=', 'mutasi_pegawais.employe_id')
            ->when($name, function ($query, $name) {
                return $query->where('employes.name', 'like', '%' . $name . '%')
                    ->orWhere('employes.nip', 'like', '%' . $name . '%')
                    ->orWhere('mutasi_pegawais.nomor_sk', 'like', '%' . $name . '%')
                    ->orWhere('mutasi_pegawais.notes', 'like', '%' . $name . '%');
            })
            //  $data = MutasiPegawai::with('pegawai','jabatan','jabatan_new','unit','unit_new')
            // ->when($name, function ($query, $name) {
            //     return $query->where('employes.name', 'like', '%' . $name . '%')
            //         ->orWhere('employes.nip', 'like', '%' . $name . '%')
            //         ->orWhere('mutasi_pegawais.nomor_sk', 'like', '%' . $name . '%')
            //         ->orWhere('mutasi_pegawais.notes', 'like', '%' . $name . '%');
            // })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('mutasi_pegawais.created_at', [$startDate, $endDate]);
            })
            ->orderBy('mutasi_pegawais.created_at', 'desc')
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
                $item = MutasiPegawai::create([
                    'nomor_sk' => $data->nomor_sk,
                    'notes' => $data->notes,
                    'employe_id' => $value->id,
                    'jabatan_id' => $value->jabatan_id,
                    'jabatan_new_id' => $value->jabatan_new_id,
                    'unit_id' => $value->unit_id,
                    'unit_new_id' => $value->keluar ?? false ? 0 : $value->unit_new_id,
                    'keluar' => $value->keluar ?? false,
                    // 'masuk' => $value->masuk ?? false,
                    'created_by' =>  $data->created_by,
                    'tmt_jabatan' => Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d'),
                ]);

                $pegawai = Employe::find($value->id);
                if ($item->keluar == true) {
                    $pegawai->delete();
                } else {
                    $pegawai->tmt_jabatan = Carbon::createFromFormat('d M Y', $data->date)->format('Y-m-d');
                    $pegawai->jabatan_id = $item->jabatan_new_id;
                    $pegawai->unit_id = $item->unit_new_id;
                    $pegawai->save();
                }

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
            $data = MutasiPegawai::find($id);
            if ($data) {
                $pegawai = Employe::withTrashed()->where('id', $data->employe_id);
                if ($data->keluar == 1) {
                    $pegawai->restore();
                } else {
                    $pegawai->unit_id = $data->unit_id;
                    $pegawai->jabatan_id = $data->jabatan_id;
                    $pegawai->save();
                }

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
