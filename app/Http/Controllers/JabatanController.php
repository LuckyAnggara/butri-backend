<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends BaseController
{
    public function index(Request $request)
    {
        $data = Jabatan::all();
        return $this->sendResponse($data, 'Data fetched');
    }
}
