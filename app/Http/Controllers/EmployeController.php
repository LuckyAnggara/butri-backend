<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Models\Employe;


class EmployeController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('name');

        $data = Employe::with('pangkat', 'jabatan', 'unit')
            ->when($name, function ($query, $name) {
                return $query->where('name', 'like', '%' . $name . '%')
                    ->orWhere('nip', 'like', '%' . $name . '%');
            })
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
}
