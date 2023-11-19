<?php

namespace App\Http\Controllers;

use App\Bpk;
use App\Models\CapaianIndikatorKegiatanUtama;
use App\Models\CapaianIndikatorKinerjaKegiatan;
use App\Models\DataPengawasan;
use App\Models\Dipa;
use App\Models\Employe;
use App\Models\GroupUnit;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaUtama;
use App\Models\JenisPengawasan;
use App\Models\Kegiatan;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\Laporan;
use App\Models\LaporanWilayah;
use App\Models\MonitoringPengawasanItwil;
use App\Models\MonitoringTemuanBpk;
use App\Models\MonitoringTemuanBpkp;
use App\Models\MonitoringTemuanOri;
use App\Models\MutasiPegawai;
use App\Models\PengelolaanMedia;
use App\Models\Pengembangan;
use App\Models\Pensiun;
use App\Models\Persuratan;
use App\Pengawasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\Shared\Converter;

class LaporanWilayahController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $group = $request->input('group_id');

        $result = LaporanWilayah::where('group_id', $group)->whereYear('created_at', $tahun)->get();
        return $this->sendResponse($result, 'Data fetched');
    }

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        $name = $this->generate($data->parameter);

        try {
            DB::beginTransaction();
            $result = LaporanWilayah::create([
                'tahun' => $data->tahun,
                'bulan' => $data->bulan,
                'name' => $name,
                'link' => $name,
                 'ttd_jabatan'=> $data->ttd_jabatan,
                'ttd_name'  => $data->ttd_name,
                'ttd_nip'  => $data->ttd_nip,
                'ttd_tanggal'  => $data->ttd_tanggal,
                'ttd_location'  => $data->ttd_location,
                'group_id'  => $data->parameter->group,
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
            $data = LaporanWilayah::find($id);
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

        $laporan = LaporanWilayah::find($id);
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

    public function generate($parameter)
    {

        $group = GroupUnit::find($parameter->group);
        $dateForMonth = Carbon::create(null, $parameter->bulan, 1);
        // Format the date to get the month name
        $monthName = $dateForMonth->format('F');

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $phpWord->addParagraphStyle('pStyle', array('align' => 'center', 'spaceAfter' => 100));

        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleFirstRow = array('borderBottomSize' => 18, 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        $justifyStyle = array(
            'align' => \PhpOffice\PhpWord\SimpleType\Jc::BOTH,
        );
        $sectionStyle = array(
            'orientation' => 'landscape',
            'marginTop' => 600,
            'colsNum' => 2,
        );
        $phpWord->addFontStyle('tStyle', array('size' => 11,));

        $header = array('size' => 11, 'bold' => true);

        $phpWord->addNumberingStyle(
            'headingNumbering',
            array(
                'type'   => 'multilevel',
                'levels' => array(
                    array('pStyle' => 'Heading1', 'format' => 'upperLetter', 'text' => '%1.'),
                    array('pStyle' => 'Heading2', 'format' => 'decimal', 'text' => '%2.'),
                ),
            )
        );
        $phpWord->addTitleStyle(1, $header, array('numStyle' => 'headingNumbering', 'numLevel' => 0));
        $phpWord->addTitleStyle(2, array('size' => 11), array('numStyle' => 'headingNumbering', 'numLevel' => 1));

        // ANGGARAN
        // PERKEGIATAN
        $sectionStyle = array(
            'orientation' => 'portrait',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $header = array('size' => 11, 'bold' => true);

        $dataAnggaran = $this->laporanAnggaran($parameter);
        $section->addTextBreak(1);

        $section->addTitle(htmlspecialchars('Kinerja Anggaran'), 1);
        $section->addText('Kinerja anggaran berdasarkan realisasi sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah Rp. ' . number_format(round($dataAnggaran['totalRealisasi'])) . ' dari total Pagu ' . number_format(round($dataAnggaran['totalPagu'])) . ' (' . round(($dataAnggaran['totalRealisasi'] / $dataAnggaran['totalPagu']) * 100, 2) . '%)', 'tStyle',  $justifyStyle);

        // $section->addText('Kinerja Anggaran', $header);
        $section->addTitle(htmlspecialchars('Per Jenis Kegiatan'), 2);
        // $section->addListItem(htmlspecialchars('Per Jenis Kegiatan'), 0);
        $phpWord->addTableStyle('Realisasi Anggaran Per Jenis Kegiatan', $styleTable, $styleFirstRow);
        $table = $section->addTable('Realisasi Anggaran Per Jenis Kegiatan');
        $table->addRow();
        $table->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $table->addCell(1000, $styleCell)->addText('Kode', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Kegiatan', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Pagu (Rp.)', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Realisasi (Rp.)', $headerTableStyle);
        $table->addCell(500, $styleCell)->addText('%', $headerTableStyle);
        $number = 0;
        foreach ($dataAnggaran['realisasiKegiatan'] as $key => $kegiatan) {
            $table->addRow();
            $table->addCell(500)->addText(++$number);
            $table->addCell(1000)->addText($kegiatan->kode);
            $table->addCell(4000)->addText($kegiatan->name);
            $table->addCell(2000)->addText(number_format(round($kegiatan->pagu, 2)));
            $table->addCell(2000)->addText(number_format(round($kegiatan->realisasi_saat_ini, 2)));
            $table->addCell(500)->addText(round(($kegiatan->realisasi_saat_ini / $kegiatan->pagu) * 100, 2) . '%');
        }

        //CAPAIAN IKK
        $dataIKK = $this->laporanIKK($parameter);
        $section->addTitle(htmlspecialchars('Capaian Indikator Kinerja Kegiatan' . $group->name), 1);
        $section->addText('Capaian Indikator Kinerja Kegiatan pada ' . $group->name . ' sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);
        // $section->addText('Capaian Indikator Kinerja Kegiatan');

        $phpWord->addTableStyle('Indikator Kinerja Kegiatan', $styleTable, $styleFirstRow);
        $table = $section->addTable('Indikator Kinerja Kegiatan');
        $table->addRow();
        $table->addCell(4000, $styleCell)->addText('Indikator', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Target', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Realisasi', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Analisis', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Kendala / Hambatan', $headerTableStyle);
        foreach ($dataIKK as $key => $ikk) {
            $table->addRow();
            $table->addCell(4000)->addText($ikk->name);
            $table->addCell(2000)->addText($ikk->target);
            $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->realisasi ?? ''));
            $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->analisa ?? ''));
            $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->kendala ?? ''));
        }
        $section->addPageBreak();

        // DATA PENGAWASAN
        $sectionStyle = array(
            'orientation' => 'portrait',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $dataPengawasan = $this->laporanDataPengawasan($parameter);
        $section->addTitle(htmlspecialchars('Data Pengawasan'), 1);
        $section->addText('Data Pengawasan sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);
        $phpWord->addTableStyle('Data Pengawasan', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Pengawasan');
        $table->addRow();
        $table->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Jenis Pengawasan', $headerTableStyle);
        $table->addCell(1000, $styleCell)->addText('Total Kegiatan', $headerTableStyle);

        $number = 0;
        foreach ($dataPengawasan as $key => $pengawasan) {
            $table->addRow();
            $table->addCell(500)->addText(++$number);
            $table->addCell(4000)->addText($pengawasan->name);
            $table->addCell(1000)->addText($pengawasan->jumlah);
        }
        $section->addPageBreak();

        // DATA KEGIATAN LAINNYA
        $dataKegiatan = $this->laporanKegiatanLainnya($parameter);
        $sectionStyle = array(
            'orientation' => 'portrait',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $section->addTextBreak(1);
        $section->addTitle(htmlspecialchars('Data Kegiatan ' . $group->name), 1);
        $section->addText('Data Kegiatan pada bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);
        $phpWord->addTableStyle('Data Kegiatan', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Kegiatan');
        $table->addRow();
        $table->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Nama Kegiatan', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Tempat dan Waktu', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Output', $headerTableStyle);
        $table->addCell(500, $styleCell)->addText('Pelaksana', $headerTableStyle);

        $number = 0;

        foreach ($dataKegiatan as $key => $kegiatan) {
            $table->addRow();
            $table->addCell(500)->addText(++$number);
            $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $kegiatan->name ?? ''));
            $table->addCell(2000)->addText($kegiatan->tempat . '</w:t><w:br/><w:t>' . Carbon::create($kegiatan->start_at)->format('d F Y') . ' s.d ' . Carbon::create($kegiatan->start_at)->format('d F Y'));
            $table->addCell(4000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $kegiatan->output ?? ''));
            $table->addCell(500)->addText($kegiatan->unit->name);
        }



        // Save file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        $time = Carbon::now()->format('YmdHis');
        $name = 'laporan' . $time . '.docx';
        $objWriter->save(public_path($name));

        return $name;
    }


    public function laporanIKK($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $group = $parameter->group;


        $result = IndikatorKinerjaKegiatan::whereYear('created_at', $tahun)->where('group_id', $group)->get();
        foreach ($result as $key => $value) {
            $value->realisasi = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->where('tahun', $tahun)->where('bulan', $bulan)
                ->first();
        }
        return $result;
    }

    public function laporanDataPengawasan($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $group = $parameter->group;
        $unit = $parameter->unit;



        $jenisPengawasan = JenisPengawasan::all();
        $dataPengawasan = [];

        foreach ($jenisPengawasan as $key => $value) {
            $result = DataPengawasan::where('unit_id', $unit)->where('jenis_pengawasan_id', $value->id)->where('tahun',  $tahun)
                ->when($bulan, function ($query, $bulan) {
                    return $query->whereMonth('created_at', '<=', $bulan);
                })
                ->get()->count();
            $dataPengawasan[] = new Pengawasan($value->name, $result);
        }

        return $dataPengawasan;
    }

    public function laporanKegiatanLainnya($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $unit = $parameter->unit;

        $startDate = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '01');
        $endDate = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '31');

        $data = Kegiatan::where('unit_id', $unit)->with('unit')->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $startDate = $startDate->format('Y-m-d 00:00:00');
            $endDate = $endDate->format('Y-m-d 23:59:59');
            return $query->whereBetween('start_at', [$startDate, $endDate]);
        })->get();

        return $data;
    }

    public function laporanAnggaran($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $group = $parameter->group;

        // ANGGARAN
        $realisasiKegiatan = Dipa::where('group_id', $group)->with(['group', 'realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])
            ->where('jenis', 'kegiatan')
            ->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })
            ->get();


        $totalRealisasi = 0;
        $totalPagu = 0;

        foreach ($realisasiKegiatan as $key => $value) {
            $total_realisasi = 0;
            foreach ($value->realisasi as $key => $x) {
                $total_realisasi += $x->realisasi;
            }
            $collection = collect($value->realisasi);
            $filteredCollection = $collection->where('bulan',  $bulan)->first();
            $value->dp_saat_ini = $filteredCollection->dp ?? 0;
            $value->realisasi_saat_ini = $filteredCollection->realisasi ?? 0;
            $value->total_realisasi = $total_realisasi -   $value->realisasi_saat_ini;

            $totalRealisasi += $value->realisasi_saat_ini;
            $totalPagu += $value->pagu;
        }

        $realisasiBelanja = Dipa::with(['group', 'realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])
            ->where('jenis', 'belanja')
            ->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })
            ->get();
        foreach ($realisasiBelanja as $key => $value) {
            $total_realisasi = 0;
            foreach ($value->realisasi as $key => $x) {
                $total_realisasi += $x->realisasi;
            }
            $collection = collect($value->realisasi);
            $filteredCollection = $collection->where('bulan',  $bulan)->first();
            $value->dp_saat_ini = $filteredCollection->dp ?? 0;
            $value->realisasi_saat_ini = $filteredCollection->realisasi ?? 0;
            $value->total_realisasi = $total_realisasi -   $value->realisasi_saat_ini;
        }


        return $realisasi = [
            'totalRealisasi' => $totalRealisasi,
            'totalPagu' => $totalPagu,
            'realisasiKegiatan' => $realisasiKegiatan,
            'realisasiBelanja' => $realisasiBelanja,
        ];
    }


    public function view()
    {
        $tahun = 2023;
        $bulan = 11;
        $group = 1;


        $result = IndikatorKinerjaKegiatan::whereYear('created_at', $tahun)->where('group_id', $group)->get();
        foreach ($result as $key => $value) {
            $value->realisasi = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->where('tahun', $tahun)->where('bulan', $bulan)
                ->first();
        }
        return $result;
    }
}
