<?php

namespace App\Http\Controllers;

use App\Models\Pangkat;
use Illuminate\Http\Request;

class PangkatController extends BaseController
{
    public function index(Request $request)
    {
        $data = Pangkat::all();
        return $this->sendResponse($data, 'Data fetched');
    }
}
