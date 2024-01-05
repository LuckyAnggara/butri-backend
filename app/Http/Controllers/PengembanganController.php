<?php

namespace App\Http\Controllers;

use App\Models\DetailPengembangan;
use App\Models\Employe;
use App\Models\Pengembangan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembanganController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = Pengembangan::when($name, function ($query, $name) {
            return $query->where('pengembangans.kegiatan', 'like', '%' . $name . '%')
                ->orWhere('pengembangans.tempat', 'like', '%' . $name . '%');
        })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('pengembangans.start_at', [$startDate, $endDate]);
            })
            ->orderBy('pengembangans.created_at', 'asc')
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $startDate =  Carbon::createFromFormat('d M Y', $data->waktu->startDate);
            $endDate = Carbon::createFromFormat('d M Y', $data->waktu->endDate)->format('Y-m-d 23:59:59');
            $diffDate = $startDate->diffInDays($endDate);

            $result = Pengembangan::create([
                'kegiatan' => $data->kegiatan,
                'tempat' => $data->tempat,
                'notes' => $data->notes,
                'start_at' => $startDate->format('Y-m-d 00:00:00'),
                'end_at' =>  $endDate,
                'jumlah_hari' => $diffDate,
                'jumlah_peserta' => count($data->list),
                'created_by' =>  $data->created_by,
            ]);
            if ($result) {
                if ($data->semuaPegawai == true) {
                    $pegawais = Employe::WhereNull('tmt_pensiun')->orWhereNot('tmt_pensiun', '<=', Carbon::today())->get();
                    foreach ($pegawais as $key => $pegawai) {
                        DetailPengembangan::create([
                            'pengembangan_id' => $result->id,
                            'employe_id' => $pegawai->id,
                            'status' => 'LULUS',
                        ]);
                    }
                    $result->jumlah_peserta = $pegawais->count();
                    $result->save();
                } else {
                    foreach ($data->list as $key => $value) {
                        DetailPengembangan::create([
                            'pengembangan_id' => $result->id,
                            'employe_id' => $value->id,
                            'status' => 'LULUS',
                        ]);
                    }
                }
            }

            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function show($id)
    {
        $result = Pengembangan::where('id', $id)
            ->with(['list.pegawai'])
            ->first();
        if ($result) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $startDate =  Carbon::createFromFormat('d M Y', $data->waktu->startDate);
            $endDate = Carbon::createFromFormat('d M Y', $data->waktu->endDate)->format('Y-m-d 23:59:59');
            $diffDate = $startDate->diffInDays($endDate);

            $pengembangan = Pengembangan::findOrFail($id);
            $pengembangan->update([
                'kegiatan' => $data->kegiatan,
                'tempat' => $data->tempat,
                'notes' => $data->notes,
                'start_at' => $startDate->format('Y-m-d 00:00:00'),
                'end_at' =>  $endDate,
                'jumlah_hari' => $diffDate,
                'jumlah_peserta' => count($data->list),
                'created_by' =>  $data->created_by,
            ]);

            if ($pengembangan) {
                $detailPengembangan =  DetailPengembangan::where('pengembangan_id', $pengembangan->id)->get();
                foreach ($detailPengembangan as $key => $value) {
                    $value->delete();
                }

                foreach ($data->list as $key => $value) {
                    DetailPengembangan::create([
                        'pengembangan_id' => $pengembangan->id,
                        'employe_id' => $value->employe_id ?? $value->id,
                        'status' => 'LULUS',
                    ]);
                }
            }

            $result = Pengembangan::where('id', $pengembangan->id)
                ->with(['list.pegawai'])
                ->first();

            DB::commit();
            return $this->sendResponse($result, 'Updated berhasil', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Error');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Pengembangan::find($id);
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
