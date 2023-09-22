<?php

namespace App\Http\Controllers;

use App\Models\GroupUnit;
use Illuminate\Http\Request;

class GroupUnitController extends BaseController
{
    public function index(Request $request)
    {
        $data = GroupUnit::with('unit')->get();
        return $this->sendResponse($data, 'Data fetched');
    }
}
