<?php

namespace App\Http\Controllers;

use App\Models\CapaianIndikatorKinerjaKegiatan;
use App\Models\GroupUnit;
use App\Models\IndikatorKinerjaKegiatan;
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
        $result = Laporan::whereYear('created_at', $tahun)->get();
        return $this->sendResponse($result, 'Data fetched');
    }

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        $name = $this->generate($data->parameter);

        try {
            DB::beginTransaction();
            $result = Laporan::create([
                'name' => $name,
                'link' => "\laporan\\" . $name,
                'ttd_name'  => $data->ttd_name,
                'ttd_nip'  => $data->ttd_nip,
                'ttd_location'  => $data->ttd_location,
                'created_by' =>  $data->created_by,
            ]);
            DB::commit();
            return $this->sendResponse($result, 'Data berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 'Failed to saved data');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $data = Laporan::find($id);
            if ($data) {
                $data->delete();
                $path = public_path($data->link);
                if (file_exists($path)) {
                    unlink($path);
                } else {
                    echo "File does not exist.";
                }

                DB::commit();

                return $this->sendResponse($data, 'Data berhasil dihapus', 200);
            } else {
                return $this->sendError('', 'Data tidak ditemukan', 404);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError('Terjadi kesalahan', $e->getMessage(), 500);
        }
    }


    public function download($id)
    {

        $laporan = Laporan::find($id);
        $path = public_path($laporan->link);
        $fileName = $laporan->name;

        return Response::download($path, $fileName);
    }

    public function debug()
    {


        $tahun = 2023;
        $bulan = 11;
        $group = 2;

        $groupAll = GroupUnit::all();
        foreach ($groupAll as $key => $x) {
            if ($x->id != 8) {
                $result = IndikatorKinerjaKegiatan::whereYear('created_at', $tahun)->where('group_id', $x->id)->get();
                foreach ($result as $key => $value) {
                    $value->realisasi = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->where('tahun', $tahun)->where('bulan', $bulan)
                        ->first();
                }
                $x->ikk = $result;
            }
        }

        return $groupAll;
    }

    public function generate($paramater)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();


        $sectionStyle = array(
            'orientation' => 'landscape',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $header = array('size' => 12, 'bold' => true);

        //CAPAIAN IKK
        $dataIKK = $this->laporanIKK($paramater);

        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $styleCell = array('valign' => 'center');
        $fontStyle = array('bold' => true, 'align' => 'center');

        foreach ($dataIKK as $key => $value) {
            $section->addTextBreak(1);
            $section->addText(htmlspecialchars('Capaian Indikator Kinerja Kegiatan ' . $value->name), $header);
            $phpWord->addTableStyle('Indikator Kinerja Kegiatan', $styleTable, $styleFirstRow);
            $table = $section->addTable('Indikator Kinerja Kegiatan');
            $table->addRow();
            $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Indikator'), $fontStyle);
            $table->addCell(2000, $styleCell)->addText(htmlspecialchars('Target'), $fontStyle);
            $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Realisasi'), $fontStyle);
            $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Analisis'), $fontStyle);
            $table->addCell(4000, $styleCell)->addText(htmlspecialchars('Kendala / Hambatan'), $fontStyle);


            foreach ($value->ikk as $key => $ikk) {
                $table->addRow();
                $table->addCell(4000)->addText(htmlspecialchars($ikk->name));
                $table->addCell(2000)->addText(htmlspecialchars($ikk->target));
                $table->addCell(3000)->addText(htmlspecialchars($ikk->realisasi->realisasi ?? '-'));
                $table->addCell(3000)->addText(htmlspecialchars($ikk->realisasi->analisa ?? '-'));
                // $table->addCell(3000)->addText(\PhpOffice\PhpWord\IOFactory::load($ikk->realisasi->kendala ?? '', 'HTML'));
                $table->addCell(4000)->addText(htmlspecialchars($ikk->realisasi->kendala ?? ''));
            }


            $section->addPageBreak();
        }


        // Save file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        $time = Carbon::now()->format('YmdHis');
        $name = 'laporan' . $time . '.docx';
        $objWriter->save(public_path("\laporan\\" . $name));

        return $name;



        // try {
        //     $time = Carbon::now()->format('YmdHis');
        //     $name = 'helloWorld' . $time . '.docx';
        //     $objWriter->save(public_path("\laporan\\" . $name));
        //     DB::beginTransaction();
        //     $result = Laporan::create([
        //         'name' => $name,
        //         'link' => "\laporan\\" . $name,
        //     ]);
        //     DB::commit();
        //     return $this->sendResponse($result, 'Data berhasil dibuat');
        // } catch (Exception $e) {
        //     DB::rollBack();
        //     return $this->sendError($e->getMessage(), 'Failed to saved data');
        // }



        // return response()->download(storage_path('\laporan\helloWorld.docx'));
    }

    public function laporanIKK($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $group = $parameter->group;

        $groupAll = GroupUnit::whereNot('id', 8)
            ->get();
        foreach ($groupAll as $key => $x) {
            $result = IndikatorKinerjaKegiatan::whereYear('created_at', $tahun)->where('group_id', $x->id)->get();
            foreach ($result as $key => $value) {
                $value->realisasi = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->where('tahun', $tahun)->where('bulan', $bulan)
                    ->first();
            }
            $x->ikk = $result;
        }
        return $groupAll;
    }

    public function view()
    {
        $tahun = 2023;
        $bulan = 11;
        $group = 1;

        $groupAll = GroupUnit::whereNot('id', 8)
            ->get();
        foreach ($groupAll as $key => $x) {
            $result = IndikatorKinerjaKegiatan::whereYear('created_at', $tahun)->where('group_id', $x->id)->get();
            foreach ($result as $key => $value) {
                $value->realisasi = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->where('tahun', $tahun)->where('bulan', $bulan)
                    ->first();
            }
            $x->ikk = $result;
        }

        return view('laporan.view', ['groups' => $groupAll]);
    }
}
