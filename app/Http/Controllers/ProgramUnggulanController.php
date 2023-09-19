<?php

namespace App\Http\Controllers;

use App\Models\CapaianProgramUnggulan;
use App\Models\Kegiatan;
use App\Models\ProgramUnggulan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramUnggulanController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 1000);
        $name = $request->input('name');
        $date = $request->input('date');


        $data = ProgramUnggulan::when($date, function ($query, $date) {
            return $query->where('tahun', $date);
        })
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%');
            })
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
}
