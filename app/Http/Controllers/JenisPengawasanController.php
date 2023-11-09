<?php

namespace App\Http\Controllers;

use App\Models\JenisPengawasan;
use Illuminate\Http\Request;

class JenisPengawasanController extends BaseController
{
        public function index(Request $request)
    {
        $data = JenisPengawasan::all();
        return $this->sendResponse($data, 'Data fetched');
    }
}
