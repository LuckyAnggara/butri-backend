<?php

namespace App\Http\Controllers;

use App\Models\SatuanKerja;
use Illuminate\Http\Request;

class SatuanKerjaController extends BaseController
{
    public function index(Request $request)
    {
        $data = SatuanKerja::all();
        return $this->sendResponse($data, 'Data fetched');
    }
}
