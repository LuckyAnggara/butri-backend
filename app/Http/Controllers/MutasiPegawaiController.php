<?php

namespace App\Http\Controllers;

use App\Models\MutasiPegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MutasiPegawaiController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');
        $startDate = $request->input('start-date');
        $endDate = $request->input('end-date');

        $data = MutasiPegawai::select('mutasi_pegawais.*', 'employes.name')
            ->join('employes', 'employes.id', '=', 'mutasi_pegawais.employe_id')
            ->when($name, function ($query, $name) {
                return $query->where('employes.name', 'like', '%' . $name . '%')
                    ->orWhere('employes.nip', 'like', '%' . $name . '%')
                    ->orWhere('mutasi_pegawais.nomor_sk', 'like', '%' . $name . '%')
                    ->orWhere('mutasi_pegawais.notes', 'like', '%' . $name . '%');
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $startDate = Carbon::createFromFormat('d M Y', $startDate)->format('Y-m-d 00:00:00');
                $endDate = Carbon::createFromFormat('d M Y', $endDate)->format('Y-m-d 23:59:59');
                return $query->whereBetween('mutasi_pegawais.created_at', [$startDate, $endDate]);
            })
            ->orderBy('mutasi_pegawais.created_at', 'asc')
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
}
