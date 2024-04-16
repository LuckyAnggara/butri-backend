<?php

namespace App\Http\Controllers;

use App\Models\Sop;
use Illuminate\Http\Request;

class SopController extends BaseController
{
    public function index(Request $request)
    {
        $perPage = $request->input('limit', 5);
        $name = $request->input('query');

        $data = Sop::when($name, function ($query, $name) {
            return $query->where('nomor', 'like', '%' . $name . '%')
                ->orWhere('nama', 'like', '%' . $name . '%');
        })
            ->orderBy('nomor', 'asc')
            ->latest()
            ->paginate($perPage);
        return $this->sendResponse($data, 'Data fetched');
    }
}
