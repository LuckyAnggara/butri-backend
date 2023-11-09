<?php

namespace App\Http\Controllers;

use App\Models\DataPengawasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPengawasanController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $tahun = $request->input('tahun');
        $unit = $request->input('unit');
        $bulan = $request->input('bulan');


        $data = DataPengawasan::with('jenis')->when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
        ->when($bulan, function ($query, $bulan) {
            return $query->where('bulan', $bulan);
        })
        ->when($unit, function ($query, $unit) {
            return $query->where('unit_id', $unit);
        })

        ->orderBy('created_at', 'asc')
        ->latest()
        ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
      
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        $sp_date =  Carbon::createFromFormat('d M Y', $data->sp_date)->format('Y-m-d 00:00:00');
        $startDate =  Carbon::createFromFormat('d M Y', $data->tanggalKegiatan->startDate)->format('Y-m-d 00:00:00');
        $endDate = Carbon::createFromFormat('d M Y', $data->tanggalKegiatan->endDate)->format('Y-m-d 23:59:59');
        try {
            DB::beginTransaction();
            $result = DataPengawasan::create([
                'name' => $data->name,
                'tahun' => $data->tahun,
                'bulan' => $data->bulan,
                'sp_number' => $data->sp_number,
                'sp_date' =>  $sp_date,
                'jenis_pengawasan_id' =>  $data->jenis_pengawasan_id,
                'start_at' =>  $startDate,
                'end_at' => $endDate,
                'location' => $data->location,
                'output' => $data->output,
                'unit_id' => $data->unit_id,
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
            $result = DataPengawasan::findOrFail($id);

            $sp_date =  Carbon::createFromFormat('d M Y', $data->sp_date)->format('Y-m-d 00:00:00');
            $startDate =  Carbon::createFromFormat('d M Y', $data->tanggalKegiatan->startDate)->format('Y-m-d 00:00:00');
            $endDate = Carbon::createFromFormat('d M Y', $data->tanggalKegiatan->endDate)->format('Y-m-d 23:59:59');

            $result->update([
                'name' => $data->name,
                'tahun' => $data->tahun,
                'bulan' => $data->bulan,
                'sp_number' => $data->sp_number,
                'sp_date' =>  $sp_date,
                'jenis_pengawasan_id' =>  $data->jenis_pengawasan_id,
                'start_at' =>  $startDate,
                'end_at' => $endDate,
                'location' => $data->location,
                'output' => $data->output,
                'unit_id' => $data->unit_id,
                'created_by' => $data->created_by,
            ]);

            DB::commit();
            return $this->sendResponse($result, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }
}
