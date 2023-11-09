<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class LaporanController extends BaseController
{
     public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');

        $result = Laporan::whereYear('created_at',$tahun)->get();


        
        return $this->sendResponse($result, 'Data fetched');
    }

    public function download($id){
      
        $laporan= Laporan::find($id);
        $path = public_path($laporan->link);
        $fileName = $laporan->name;

        return Response::download($path, $fileName);
    }

    public function generate(){
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();


        $description = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";


        $section->addText($description);


        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        try {
            $time = Carbon::now()->format('YmdHis');
            $name='helloWorld'.$time.'.docx';
            $objWriter->save(public_path("\laporan\\".$name));
            DB::beginTransaction();
            $result = Laporan::create([
                'name' => $name,
                'link' => "\laporan\\".$name,
            ]);
            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');

        } catch (Exception $e) {
             DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }



        // return response()->download(storage_path('\laporan\helloWorld.docx'));
    }
}
