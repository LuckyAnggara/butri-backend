<?php

namespace App\Http\Controllers;

use App\Models\CapaianIndikatorKinerjaKegiatan;
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

    public function download($id)
    {

        $laporan = Laporan::find($id);
        $path = public_path($laporan->link);
        $fileName = $laporan->name;

        return Response::download($path, $fileName);
    }

    public function generate($paramater)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $header = array('size' => 16, 'bold' => true);

        //CAPAIAN IKK

        $section->addTextBreak(1);
        $section->addText(htmlspecialchars('Fancy table'), $header);

        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $styleCell = array('valign' => 'center');
        $styleCellBTLR = array('valign' => 'center', 'textDirection' => \PhpOffice\PhpWord\Style\Cell::TEXT_DIR_BTLR);
        $fontStyle = array('bold' => true, 'align' => 'center');
        $phpWord->addTableStyle('Fancy Table', $styleTable, $styleFirstRow);
        $table = $section->addTable('Fancy Table');
        $table->addRow(900);
        $table->addCell(2000, $styleCell)->addText(htmlspecialchars('Indikator'), $fontStyle);
        $table->addCell(500, $styleCell)->addText(htmlspecialchars('Target'), $fontStyle);
        $table->addCell(500, $styleCell)->addText(htmlspecialchars('Realisasi'), $fontStyle);
        $table->addCell(2000, $styleCell)->addText(htmlspecialchars('Analisis'), $fontStyle);
        $table->addCell(2000, $styleCellBTLR)->addText(htmlspecialchars('Kendala / Hambatan'), $fontStyle);

        $dataIKK = $this->laporanIKK($paramater);


        foreach ($dataIKK as $key => $value) {
            $table->addRow();
            $table->addCell(2000)->addText(htmlspecialchars($value->name));
            $table->addCell(500)->addText(htmlspecialchars($value->target));
            $table->addCell(500)->addText($value->capaian->realisasi ?? '');
            $table->addCell(2000)->addText(htmlspecialchars($value->capaian->analisis ?? ''));
            $table->addCell(2000)->addText(htmlspecialchars($value->capaian->kendala ?? ''));
        }
        // for ($i = 1; $i <= 8; $i++) {
        //     $table->addRow();
        //     $table->addCell(2000)->addText(htmlspecialchars("Cell {$i}"));
        //     $table->addCell(2000)->addText(htmlspecialchars("Cell {$i}"));
        //     $table->addCell(2000)->addText(htmlspecialchars("Cell {$i}"));
        //     $table->addCell(2000)->addText(htmlspecialchars("Cell {$i}"));
        //     $text = (0 == $i % 2) ? 'X' : '';
        //     $table->addCell(500)->addText(htmlspecialchars($text));
        // }

        // 3. colspan (gridSpan) and rowspan (vMerge)

        $section->addPageBreak();
        $section->addText(htmlspecialchars('Table with colspan and rowspan'), $header);

        $styleTable = array('borderSize' => 6, 'borderColor' => '999999');
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center', 'bgColor' => 'FFFF00');
        $cellRowContinue = array('vMerge' => 'continue');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center');
        $cellHCentered = array('align' => 'center');
        $cellVCentered = array('valign' => 'center');

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');

        $table->addRow();

        $cell1 = $table->addCell(2000, $cellRowSpan);
        $textrun1 = $cell1->addTextRun($cellHCentered);
        $textrun1->addText(htmlspecialchars('A'));
        $textrun1->addFootnote()->addText(htmlspecialchars('Row span'));

        $cell2 = $table->addCell(4000, $cellColSpan);
        $textrun2 = $cell2->addTextRun($cellHCentered);
        $textrun2->addText(htmlspecialchars('B'));
        $textrun2->addFootnote()->addText(htmlspecialchars('Colspan span'));

        $table->addCell(2000, $cellRowSpan)->addText(htmlspecialchars('E'), null, $cellHCentered);

        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(2000, $cellVCentered)->addText(htmlspecialchars('C'), null, $cellHCentered);
        $table->addCell(2000, $cellVCentered)->addText(htmlspecialchars('D'), null, $cellHCentered);
        $table->addCell(null, $cellRowContinue);

        // 4. Nested table

        $section->addTextBreak(2);
        $section->addText(htmlspecialchars('Nested table in a centered and 50% width table.'), $header);

        $table = $section->addTable(array('width' => 50 * 50, 'unit' => 'pct', 'align' => 'center'));
        $cell = $table->addRow()->addCell();
        $cell->addText(htmlspecialchars('This cell contains nested table.'));
        $innerCell = $cell->addTable(array('align' => 'center'))->addRow()->addCell();
        $innerCell->addText(htmlspecialchars('Inside nested table'));

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


        $ikk = IndikatorKinerjaKegiatan::whereYear('created_at', $tahun)->where('group_id', $group)->get();

        foreach ($ikk as $key => $value) {
            $value->capaian = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->whereYear('created_at', $tahun)->whereMonth('created_at', $bulan)->first();
        }

        return $ikk;
    }
}
