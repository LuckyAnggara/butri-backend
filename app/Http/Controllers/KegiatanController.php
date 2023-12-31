<?php

namespace App\Http\Controllers;

use App\Models\CapaianProgramUnggulan;
use App\Models\Kegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KegiatanController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $unit = $request->input('unit');
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = Kegiatan::when($unit, function ($query, $unit) {
            return $query->where('unit_id', $unit);
        })->when($name, function ($query, $name) {
            return $query->where('name', 'like', '%' . $name . '%')
                ->orWhere('jenis_kegiatan', 'like', '%' . $name . '%')
                ->orWhere('tempat', 'like', '%' . $name . '%');
        })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('start_at', [$startDate, $endDate]);
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
            $startDate =  Carbon::createFromFormat('d M Y', $data->waktu->startDate)->format('Y-m-d 00:00:00');
            $endDate = Carbon::createFromFormat('d M Y', $data->waktu->endDate)->format('Y-m-d 23:59:59');

            DB::beginTransaction();
            $result = Kegiatan::create([
                'name' => (trim($data->name)),
                'tempat' => $data->tempat,
                'output' => (trim($data->output)),
                'jenis_kegiatan' => $data->jenis_kegiatan,
                'notes' => (trim($data->notes)),
                'start_at' => $startDate,
                'end_at' => $endDate,
                'unit_id' => $data->unit_id,
                'created_by' =>  $data->created_by,
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
            $data = Kegiatan::find($id);
            if ($data) {
                $capaian = CapaianProgramUnggulan::where('kegiatan_id', $id)->get();
                foreach ($capaian as $key => $value) {
                    $value->delete();
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
