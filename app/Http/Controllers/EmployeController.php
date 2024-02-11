<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');
        $unit = $request->input('unit');

        $data = Employe::with('pangkat', 'jabatan', 'unit')
            ->where(function ($query) {
                $query->WhereNull('tmt_pensiun')
                    ->orWhereNot('tmt_pensiun', '<=', Carbon::today());
            })

            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%')
                    ->orWhere('nip', 'like', '%' . $name . '%')->orWhereHas('unit', function ($query) use ($name) {
                        $query->where('name', 'like', '%' . $name . '%');
                    })->orWhereHas('jabatan', function ($query) use ($name) {
                        $query->where('name', 'like', '%' . $name . '%');
                    });
            })
            ->when($unit, function ($query, $unit) {
                return $query->where('unit_id', $unit);
            })
            ->orderBy('nip', 'asc')
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }

    public function show($id)
    {
        $result = Employe::where('id', $id)
            ->with(['pangkat', 'jabatan', 'unit',])
            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $pegawai = Employe::create([
                'name' => $data->name,
                'nip' =>  $data->nip,
                'phone_number' =>  $data->phone_number,
                'is_wa' =>  $data->is_wa ?? false,
                'gender' =>  $data->gender->label,
                'email' =>  $data->email,
                'pangkat_id' =>  $data->pangkat,
                'jabatan_id' =>  $data->jabatan,
                'unit_id' =>  $data->unit,
                'eselon_id' =>  $data->eselon ?? null,
                'tmt_pangkat' => Carbon::createFromFormat('d M Y', $data->tmt_pangkat)->format('Y-m-d'),
                'tmt_jabatan' =>  Carbon::createFromFormat('d M Y', $data->tmt_jabatan)->format('Y-m-d'),
                'tmt_pensiun' => Carbon::createFromFormat('d M Y', $data->tmt_pensiun)->format('Y-m-d'),
                'created_by' => $data->created_by,
            ]);
            DB::commit();
            return $this->sendResponse($pegawai, 'Data berhasil dibuat');
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
            $pegawai = Employe::findOrFail($id);
            $pegawai->update([
                'name' => $data->name,
                'nip' =>  $data->nip,
                'phone_number' =>  $data->phone_number,
                'is_wa' =>  $data->is_wa ?? false,
                'gender' =>  $data->gender,
                'email' =>  $data->email,
                'pangkat_id' =>  $data->pangkat->id,
                'jabatan_id' =>  $data->jabatan_id,
                'unit_id' =>  $data->unit->id,
                'eselon_id' =>  $data->eselon->id ?? null,
                'tmt_pangkat' => $data->tmt_pangkat ? Carbon::createFromFormat('d M Y', $data->tmt_pangkat)->format('Y-m-d') : null,
                'tmt_jabatan' =>  $data->tmt_jabatan ? Carbon::createFromFormat('d M Y', $data->tmt_jabatan)->format('Y-m-d') : null,
                'tmt_pensiun' => $data->tmt_pensiun ? Carbon::createFromFormat('d M Y', $data->tmt_pensiun)->format('Y-m-d') : null,
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
            $data = Employe::find($id);
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
