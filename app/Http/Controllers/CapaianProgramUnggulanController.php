<?php

namespace App\Http\Controllers;

use App\Models\CapaianProgramUnggulan;
use App\Models\Kegiatan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapaianProgramUnggulanController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $unit = $request->input('unit');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = CapaianProgramUnggulan::with('kegiatan', 'program')->when($unit, function ($query, $unit) {
            return $query->where('unit_id', $unit);
        })
            ->when($name, function ($query, $name) {
                return $query->where('kegiatans.name', 'like', '%' . $name . '%')
                    ->orWhere('kegiatans.output', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('kegiatans.start_at', [$startDate, $endDate]);
            })
            ->orderBy('capaian_program_unggulans.created_at', 'asc')
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

            if (isset($data->id)) {
                $result = CapaianProgramUnggulan::create([
                    'program_unggulan_id' => $data->program_unggulan_id,
                    'kegiatan_id' => $data->id,
                    'unit_id' => $data->unit_id,
                    'created_by' =>  $data->created_by,
                ]);
            } else {
                $kegiatan = Kegiatan::create([
                    'name' => $data->name,
                    'tempat' => $data->tempat,
                    'output' => nl2br(trim($data->output)),
                    'jenis_kegiatan' => $data->jenis_kegiatan,
                    'notes' => nl2br(trim($data->notes)),
                    'start_at' => $startDate,
                    'end_at' => $endDate,
                    'unit_id' => $data->unit_id,
                    'created_by' =>  $data->created_by,
                ]);

                $result = CapaianProgramUnggulan::create([
                    'program_unggulan_id' => $data->program_unggulan_id,
                    'kegiatan_id' => $kegiatan->id,
                    'unit_id' => $data->unit_id,
                    'created_by' =>  $data->created_by,
                ]);
            }

            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }
}
