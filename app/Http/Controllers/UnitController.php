<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends BaseController
{
    public function index(Request $request)
    {
        $data = Unit::all();
        return $this->sendResponse($data, 'Data fetched');
    }
}
