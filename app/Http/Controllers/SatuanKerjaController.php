<?php

namespace App\Http\Controllers;

use App\Models\SatuanKerja;
use Illuminate\Http\Request;

class SatuanKerjaController extends BaseController
{
    public function index(Request $request)
    {

        $perPage = $request->input('limit', 5);
        $name = $request->input('name');

        $data = SatuanKerja::when($name, function ($query, $name) {
            return $query->where('name', 'like', '%' . $name . '%');
        })->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
}
