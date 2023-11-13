<?php

namespace App\Http\Controllers;

use App\Models\CapaianIndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaUtama;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CapaianIndikatorKinerjaKegiatanController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('query');
        $unit = $request->input('unit');
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');


        $data = IndikatorKinerjaKegiatan::when($tahun, function ($query, $tahun) {
            return $query->whereYear('created_at', $tahun);
        })
            ->when($unit, function ($query, $unit) {
                return $query->where('group_id', $unit);
            })->get();

        foreach ($data as $key => $value) {
            $value->realisasi = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })
                ->when($bulan, function ($query, $bulan) {
                    return $query->where('bulan', $bulan);
                })->first();
        }

        return $this->sendResponse($data, 'Data fetched');
    }

    public function store(Request $request)
    {
        $data = json_decode($request->getContent());

        $exist = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $data->ikk->id)->where('bulan', $data->bulan)->where('tahun', $data->tahun)->first();

        try {
            DB::beginTransaction();

            if ($exist) {
                $exist->delete();
            }

            $result = CapaianIndikatorKinerjaKegiatan::create([
                'ikk_id' => $data->ikk->id,
                'tahun' => $data->tahun,
                'bulan' => $data->bulan,
                'realisasi' => $data->realisasi,
                'analisa' => $data->analisa,
                'kegiatan' => $data->kegiatan,
                'kendala' => $data->kendala,
                'hambatan' => $data->hambatan,
                'group_id' => $data->group_id,
                'created_by' =>  $data->created_by,
            ]);
            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function show($id)
    {
        $result = IndikatorKinerjaKegiatan::where('id', $id)->first();

        $detail = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $id)->get();
        $result->capaian = $detail;
        if ($detail) {
            return $this->sendResponse($result, 'Data fetched');
        }
        return $this->sendError('Data not found');
    }

    public function update(Request $request, $id)
    {
        $data = json_decode($request->getContent());
        try {
            DB::beginTransaction();
            $result = CapaianIndikatorKinerjaKegiatan::findOrFail($id);
            $result->update([
                'ikk_id' => $data->ikk->id,
                'realisasi' => $data->realisasi,
                'analisa' => $data->analisa,
                'kegiatan' => $data->kegiatan,
                'kendala' => $data->kendala,
                'hambatan' => $data->hambatan,
                'group_id' => $data->group_id,
                'created_by' =>  $data->created_by,
            ]);

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
            $data = CapaianIndikatorKinerjaKegiatan::find($id);
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
