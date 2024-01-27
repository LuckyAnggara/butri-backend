<?php

namespace App\Http\Controllers;

use App\Bpk;
use App\Models\CapaianIndikatorKegiatanUtama;
use App\Models\CapaianIndikatorKinerjaKegiatan;
use App\Models\CapaianProgramUnggulan;
use App\Models\DataPengawasan;
use App\Models\Dipa;
use App\Models\Employe;
use App\Models\GroupUnit;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaUtama;
use App\Models\Jabatan;
use App\Models\JenisPengawasan;
use App\Models\Kegiatan;
use App\Models\KenaikanGajiBerkala;
use App\Models\KenaikanPangkat;
use App\Models\LaporanWilayah;
use App\Models\MonitoringPengawasanItwil;
use App\Models\MonitoringTemuanBpk;
use App\Models\MonitoringTemuanBpkp;
use App\Models\MonitoringTemuanOri;
use App\Models\MutasiPegawai;
use App\Models\Pangkat;
use App\Models\PengelolaanMedia;
use App\Models\Pengembangan;
use App\Models\Pensiun;
use App\Models\Persuratan;
use App\Models\ProgramUnggulan;
use App\Models\Unit;
use App\Pengawasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Writer\Word2007\Element\Container;

class LaporanWilayahController extends BaseController
{
    public function index(Request $request)
    {
        $tahun = $request->input('tahun');
        $bulan = $request->input('bulan');
        $result = LaporanWilayah::where('group_id', Auth::user()->unit_id)->whereYear('created_at', $tahun)->get();
        return $this->sendResponse($result, 'Data fetched');
    }

    public function store(Request $request)
    {

        $data = json_decode($request->getContent());
        $parameter = $data->parameter;
        $name = $this->generate($data);

        $ttd_tanggal =  Carbon::createFromFormat('d M Y', $data->ttd_tanggal)->format('Y-m-d 00:00:00');
        try {
            DB::beginTransaction();
            $result = LaporanWilayah::create([
                'name' => $name,
                 'link' => $name,
                'ttd_jabatan' => $data->ttd_jabatan,
                'ttd_name'  => $data->ttd_name,
                'ttd_nip'  => $data->ttd_nip,
                'ttd_tanggal'  => $ttd_tanggal,
                'ttd_location'  => $data->ttd_location,
                'created_by' =>  Auth::id(),
                'group_id'=> Auth::user()->unit_id
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

    
    public function splitAngkaRomawiWilayah($unit_id)
{
    // Membagi string berdasarkan spasi dan mengambil elemen terakhir
    $unit = Unit::find($unit_id);
    $parts = explode(' ', $unit->name);
    $angkaRomawi = end($parts);

    return $angkaRomawi;
}

    public function getGroup($unit_id)
{
    // Membagi string berdasarkan spasi dan mengambil elemen terakhir
    $unit = Unit::find($unit_id);
    return $unit->group_id;
}

    public function generate($data)
    {
        $parameter = $data->parameter;
        $wilayahNumber = $this->splitAngkaRomawiWilayah(Auth::user()->unit_id);
        $ttd = $data;
        // \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $dateForMonth = Carbon::create(null, $parameter->bulan, 1);
        // Format the date to get the month name
        $monthName = $dateForMonth->format('F');
        $templateProcessor = new TemplateProcessor(public_path('template_wilayah.docx'));
        $templateProcessor->setValue('n', $wilayahNumber);
        $templateProcessor->setValue('tahun', $parameter->tahun);
        $templateProcessor->setValue('bulan', $monthName);

        $templateProcessor->setValue('ttd_name', $ttd->ttd_name ?? '-');
        $templateProcessor->setValue('ttd_location', $ttd->ttd_location ?? '-');
        $templateProcessor->setValue('ttd_tanggal', $ttd->ttd_tanggal ?? '-');
        $templateProcessor->setValue('ttd_jabatan', $ttd->ttd_jabatan ?? '-');
        $templateProcessor->setValue('ttd_nip', $ttd->ttd_nip ?? '-');

        $kepegawaian = $this->laporanKepegawaian($parameter);
        $templateProcessor->setValue('total_pegawai', $kepegawaian['total_pegawai']);
        $templateProcessor->setValue('total_pegawai_laki', $kepegawaian['total_pegawai_laki']);
        $templateProcessor->setValue('total_pegawai_perempuan', $kepegawaian['total_pegawai_perempuan']);
        $templateProcessor->setComplexBlock('tabel_total_pegawai', $kepegawaian['tabel_total_pegawai']);
        $templateProcessor->setComplexBlock('tabel_kepangkatan', $kepegawaian['tabel_kepangkatan']);
        $templateProcessor->setComplexBlock('tabel_jabatan', $kepegawaian['tabel_jabatan']);

        $anggaran = $this->laporanAnggaran($parameter);
        $templateProcessor->setValue('total_realisasi', $anggaran['total_realisasi']);
        $templateProcessor->setValue('total_pagu', $anggaran['total_pagu']);
        $templateProcessor->setValue('total_persen_realisasi_anggaran', $anggaran['total_persen_realisasi_anggaran']);
        $templateProcessor->setComplexBlock('tabel_per_kegiatan', $anggaran['tabel_per_kegiatan']);

        $ikk = $this->laporanIKK($parameter);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk', $ikk['tabel_capaian_ikk']);

        $pengawasan = $this->laporanDataPengawasan($parameter);

        $templateProcessor->setComplexBlock('tabel_rekapitulasi_pengawasan', $pengawasan['tabel_rekapitulasi_pengawasan']);
        $templateProcessor->setComplexBlock('tabel_detail_pengawasan', $pengawasan['tabel_detail_pengawasan']);

        $kegiatan = $this->laporanKegiatanLainnya($parameter);
        $templateProcessor->setComplexBlock('tabel_kegiatan', $kegiatan);

        $programUnggulan = $this->laporanCapaianProgramUnggulan($parameter);
        $templateProcessor->setComplexBlock('tabel_capaian_program_unggulan', $programUnggulan['tabel_capaian_program_unggulan']);
        $templateProcessor->setComplexBlock('tabel_detail_program_unggulan', $programUnggulan['tabel_detail_program_unggulan']);


        $time = Carbon::now()->format('is');
        $name = 'Laporan Wilayah '.$wilayahNumber. ' Bulan ' . $monthName . ' Tahun ' . $parameter->tahun . $time . '.docx';
        $templateProcessor->saveAs(public_path($name));

        return $name;
    }

    public function laporanCapaianProgramUnggulan ($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;

        $breaks = array("<br />", "<br>", "<br/>");
        $programUnggulan = ProgramUnggulan::with('list')->where('tahun', $tahun)->get();

        foreach ($programUnggulan as $key => $value) {
            $detail = CapaianProgramUnggulan::with('kegiatan')->whereMonth('created_at', $bulan)->whereYear('created_at', $tahun)->where('program_unggulan_id', $value->id)->where('unit_id', Auth::user()->unit_id)->get();
            $value->jumlah = $detail->count();
        }

        // TABEL CAPAIAN IKU
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $styleCellSpan = array('valign' => 'center', 'size' => 11, 'gridSpan' => 5);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_capaian_program_unggulan = new Table($styleTable);
        $tabel_capaian_program_unggulan->addRow();
        $tabel_capaian_program_unggulan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_capaian_program_unggulan->addCell(5000, $styleCell)->addText('Nama Program', $headerTableStyle);
        $tabel_capaian_program_unggulan->addCell(1000, $styleCell)->addText('Total Kegiatan', $headerTableStyle);

        $number = 0;
        foreach ($programUnggulan as $key => $program) {
            $tabel_capaian_program_unggulan->addRow();
            $tabel_capaian_program_unggulan->addCell(500)->addText(++$number);
            $tabel_capaian_program_unggulan->addCell(5000)->addText($program->name);
            $tabel_capaian_program_unggulan->addCell(1000)->addText($program->jumlah);
        }

        // RINCIAN PROGRAM UNGGULAN


        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $styleCellSpan = array('valign' => 'center', 'size' => 11, 'gridSpan'=>5);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // // ADD TABLE
        $tabel_detail_program_unggulan = new Table($styleTable);
        $tabel_detail_program_unggulan->addRow();
        $tabel_detail_program_unggulan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_detail_program_unggulan->addCell(1000, $styleCell)->addText('Program', $headerTableStyle);
        $tabel_detail_program_unggulan->addCell(6000, $styleCell)->addText('Nama Kegiatan', $headerTableStyle);
        $tabel_detail_program_unggulan->addCell(3000, $styleCell)->addText('Tanggal Pelaksanaan', $headerTableStyle);
        $tabel_detail_program_unggulan->addCell(2000, $styleCell)->addText('No dan Tanggal Laporan', $headerTableStyle);

        $detailPU = CapaianProgramUnggulan::with('kegiatan','program')->where('program_unggulan_id', $value->id)->where('unit_id', Auth::user()->unit_id)->get();
        $number = 0;
        foreach ($detailPU as $key => $value) {
            $tabel_detail_program_unggulan->addRow();
            $tabel_detail_program_unggulan->addCell(500, $styleCellSpan)->addText(++$number);
            $tabel_detail_program_unggulan->addCell(1000)->addText($value->program->name);
            $tabel_detail_program_unggulan->addCell(6500)->addText(str_ireplace($breaks, "\r\n", $this->escapeSingleValue($value->kegiatan->name)));
            $tabel_detail_program_unggulan->addCell(3000)->addText($this->escapeSingleValue($value->kegiatan->tempat) . "\r\n" . Carbon::create($value->kegiatan->start_at)->format('d F Y') . ' s.d ' . Carbon::create($value->kegiatan->start_at)->format('d F Y'));
            $tabel_detail_program_unggulan->addCell(2000)->addText($value->kegiatan->output);     
        }

        return  [
            'tabel_capaian_program_unggulan' => $tabel_capaian_program_unggulan,
            'tabel_detail_program_unggulan' => $tabel_detail_program_unggulan,
        ];

    }


    public function laporanPersuratan($parameter)
    {
        $tahun =  $parameter->tahun;
        $result = [];
        for ($i = 1; $i < 13; $i++) {
            $bulan = Carbon::createFromDate(2023, $i, 1)->format('F');
            $data = Persuratan::where('tahun', $tahun)->where('bulan', $i)->first();
            if ($data) {
                $result[] = array(
                    'tahun' => $tahun,
                    'bulan_name' =>  $bulan,
                    'bulan' => $i,
                    'surat_masuk' => $data->surat_masuk ?? 0,
                    'surat_keluar' => $data->surat_keluar ?? 0,
                );
            } else {
                $result[] = array(
                    'tahun' => $tahun,
                    'bulan_name' =>   $bulan,
                    'bulan' => $i,
                    'surat_masuk' => 0,
                    'surat_keluar' => 0,
                );
            }
        }
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $table = new Table($styleTable);
        $table->addRow();
        $table->addCell(500, $styleCell)->addText('Bulan', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Surat Masuk', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Surat Keluar', $headerTableStyle);

        $number = 0;
        $totalSuratMasuk = 0;
        $totalSuratKeluar = 0;
        foreach ($result as $key => $persuratan) {
            $table->addRow();
            $totalSuratMasuk += $persuratan['surat_masuk'];
            $totalSuratKeluar += $persuratan['surat_keluar'];
            $table->addCell(500)->addText($persuratan['bulan_name']);
            $table->addCell(500)->addText($persuratan['surat_masuk']);
            $table->addCell(500)->addText($persuratan['surat_keluar']);
        }
        $table->addRow();
        $table->addCell(500)->addText('Total', $headerTableStyle);
        $table->addCell(500)->addText($totalSuratMasuk);
        $table->addCell(500)->addText($totalSuratKeluar);

        return $table;
    }


    public function laporanKegiatanLainnya($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $unit_id = Auth::user()->unit_id;

        $breaks = array("<br />", "<br>", "<br/>");


        $kegiatans = Kegiatan::with('unit')->where('unit_id', $unit_id)->whereYear('start_at', $tahun)->whereMonth('start_at', $bulan)->get();

        // TABEL CAPAIAN IKU
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80,);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_kegiatan = new Table($styleTable);
        $tabel_kegiatan->addRow();
        $tabel_kegiatan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_kegiatan->addCell(2000, $styleCell)->addText('Unit', $headerTableStyle);
        $tabel_kegiatan->addCell(6500, $styleCell)->addText('Nama Kegiatan', $headerTableStyle);
        $tabel_kegiatan->addCell(1500, $styleCell)->addText('Jenis Kegiatan', $headerTableStyle);
        $tabel_kegiatan->addCell(4000, $styleCell)->addText('Waktu dan Lokasi Kegiatan', $headerTableStyle);
        $number = 0;
        foreach ($kegiatans as $key => $kegiatan) {
            $tabel_kegiatan->addRow();
            $tabel_kegiatan->addCell(500)->addText(++$number);
            $tabel_kegiatan->addCell(2000)->addText($kegiatan->unit->name);
            $tabel_kegiatan->addCell(6500)->addText(str_ireplace($breaks, "\r\n", $this->escapeSingleValue($kegiatan->name)));
            $tabel_kegiatan->addCell(1500)->addText($kegiatan->jenis_kegiatan);
            $tabel_kegiatan->addCell(4000)->addText($this->escapeSingleValue($kegiatan->tempat) . "\r\n" . Carbon::create($kegiatan->start_at)->format('d F Y') . ' s.d ' . Carbon::create($kegiatan->start_at)->format('d F Y'));
        }

        return $tabel_kegiatan;
    }

    public function laporanDataPengawasan($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $unit_id = Auth::user()->unit_id;

        $breaks = array("<br />", "<br>", "<br/>", ";", "/");
        $breaks2 = array("&");

        $jenisPengawasan = JenisPengawasan::all();
        foreach ($jenisPengawasan as $key => $value) {
            $detail = DataPengawasan::where('unit_id',$unit_id)->where('jenis_pengawasan_id', $value->id)->with('unit')->where('tahun',  $tahun)
                ->when($bulan, function ($query, $bulan) {
                    return $query->where('bulan', $bulan);
                })
                ->get();
            $count = $detail->count();
            $value->detail = $detail;
            $value->jumlah = $count;
            // $dataPengawasan[] = new Pengawasan($value->name, $result);
        }

        // TABEL CAPAIAN IKU
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $styleCellSpan = array('valign' => 'center', 'size' => 11, 'gridSpan' => 5);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_rekapitulasi_pengawasan = new Table($styleTable);
        $tabel_rekapitulasi_pengawasan->addRow();
        $tabel_rekapitulasi_pengawasan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_rekapitulasi_pengawasan->addCell(5000, $styleCell)->addText('Jenis Pengawasan', $headerTableStyle);
        $tabel_rekapitulasi_pengawasan->addCell(1000, $styleCell)->addText('Total Kegiatan', $headerTableStyle);

        $number = 0;
        foreach ($jenisPengawasan as $key => $pengawasan) {
            $tabel_rekapitulasi_pengawasan->addRow();
            $tabel_rekapitulasi_pengawasan->addCell(500)->addText(++$number);
            $tabel_rekapitulasi_pengawasan->addCell(5000)->addText($pengawasan->name);
            $tabel_rekapitulasi_pengawasan->addCell(1000)->addText($pengawasan->jumlah);
        }

        // ADD TABLE
        $tabel_detail_pengawasan = new Table($styleTable);
        $tabel_detail_pengawasan->addRow();
        $tabel_detail_pengawasan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_detail_pengawasan->addCell(1500, $styleCell)->addText('Unit', $headerTableStyle);
        $tabel_detail_pengawasan->addCell(6000, $styleCell)->addText('Nama Kegiatan', $headerTableStyle);
        $tabel_detail_pengawasan->addCell(3000, $styleCell)->addText('Tmt dan Lokasi', $headerTableStyle);
        $tabel_detail_pengawasan->addCell(3000, $styleCell)->addText('No dan Tgl LHP', $headerTableStyle);


        $number = 0;
        foreach ($jenisPengawasan as $key => $pengawasan) {
            $tabel_detail_pengawasan->addRow();
            $tabel_detail_pengawasan->addCell(4000, $styleCellSpan)->addText(++$number . '. ' . $pengawasan->name, $headerTableStyle);
            if ($pengawasan->jumlah == 0) {
                $tabel_detail_pengawasan->addRow();
                $tabel_detail_pengawasan->addCell(4000, $styleCellSpan)->addText('nihil');
            } else {
                $num = 0;
                foreach ($pengawasan->detail as $key => $detail) {
                    $tabel_detail_pengawasan->addRow();
                    $tabel_detail_pengawasan->addCell(500)->addText(++$num);
                    $tabel_detail_pengawasan->addCell(1500)->addText(trim($detail->unit->name));
                    $tabel_detail_pengawasan->addCell(6000)->addText(str_ireplace($breaks, "\r\n", $this->escapeSingleValue($detail->name)));
                    $tabel_detail_pengawasan->addCell(3000)->addText($this->escapeSingleValue($detail->location)  . "\r\n" . Carbon::create($detail->start_at)->format('d F Y') . ' s.d ' . Carbon::create($detail->start_at)->format('d F Y'));
                    $tabel_detail_pengawasan->addCell(3000)->addText($detail->output);
                }
            }
        }


        return  [
            'tabel_rekapitulasi_pengawasan' => $tabel_rekapitulasi_pengawasan,
            'tabel_detail_pengawasan' => $tabel_detail_pengawasan,
        ];
    }

    protected function escapeSingleValue($input)
    {
        $escaped = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        // we don't want to escape the newline code, so we replace it with html tag again
        $escaped_but_newlines_allowed = str_replace('&lt;/w:t&gt;&lt;w:br/&gt;&lt;w:t&gt;', '</w:t><w:br/><w:t>', $escaped);
        return $escaped_but_newlines_allowed;
    }


    public function laporanIKK($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $group_id = $this->getGroup(Auth::user()->unit_id);

        $result = IndikatorKinerjaKegiatan::whereYear('created_at', $tahun)->where('group_id', $group_id)->get();
        foreach ($result as $key => $value) {
            $value->realisasi = CapaianIndikatorKinerjaKegiatan::where('ikk_id', $value->id)->where('tahun', $tahun)->where('bulan', $bulan)
                ->first();
        }
        
        // TABEL CAPAIAN IKK
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $number = 0;

            $table = new Table($styleTable);
            $table->addRow();
            $table->addCell(4000, $styleCell)->addText('Indikator', $headerTableStyle);
            $table->addCell(2000, $styleCell)->addText('Target', $headerTableStyle);
            $table->addCell(4000, $styleCell)->addText('Realisasi', $headerTableStyle);
            $table->addCell(4000, $styleCell)->addText('Analisis', $headerTableStyle);
            $table->addCell(4000, $styleCell)->addText('Kendala / Hambatan', $headerTableStyle);
            foreach ($result as $key => $ikk) {
                $table->addRow();
                $table->addCell(4000)->addText($ikk->name);
                $table->addCell(2000)->addText($ikk->target);
                $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->realisasi ?? ''));
                $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->analisa ?? ''));
                $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->kendala ?? ''));
            }
            $data['tabel_capaian_ikk'] = $table;
        
        return $data;
    }

    public function laporanAnggaran($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $group_id = $this->getGroup(Auth::user()->unit_id);
        // ANGGARAN
        $realisasiKegiatan = Dipa::with(['group', 'realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])
            ->where('jenis', 'kegiatan')
            ->where('group_id', $group_id)
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

        $realisasi = [
            'totalRealisasi' => $totalRealisasi,
            'totalPagu' => $totalPagu,
            'realisasiKegiatan' => $realisasiKegiatan,
        ];

        // TABEL ANGGARAN
        // PERKEGIATAN
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_per_kegiatan = new Table($styleTable);
        $tabel_per_kegiatan->addRow();
        $tabel_per_kegiatan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_per_kegiatan->addCell(1000, $styleCell)->addText('Kode', $headerTableStyle);
        $tabel_per_kegiatan->addCell(4000, $styleCell)->addText('Kegiatan', $headerTableStyle);
        $tabel_per_kegiatan->addCell(2000, $styleCell)->addText('Pagu (Rp.)', $headerTableStyle);
        $tabel_per_kegiatan->addCell(2000, $styleCell)->addText('Realisasi (Rp.)', $headerTableStyle);
        $tabel_per_kegiatan->addCell(500, $styleCell)->addText('%', $headerTableStyle);
        $number = 0;
        foreach ($realisasi['realisasiKegiatan'] as $key => $kegiatan) {
            $tabel_per_kegiatan->addRow();
            $tabel_per_kegiatan->addCell(500)->addText(++$number);
            $tabel_per_kegiatan->addCell(1000)->addText($kegiatan->kode);
            $tabel_per_kegiatan->addCell(4000)->addText($kegiatan->name);
            $tabel_per_kegiatan->addCell(2000)->addText(number_format(round($kegiatan->pagu, 2)));
            $tabel_per_kegiatan->addCell(2000)->addText(number_format(round($kegiatan->realisasi_saat_ini, 2)));
            $tabel_per_kegiatan->addCell(500)->addText(round(($kegiatan->realisasi_saat_ini / $kegiatan->pagu) * 100, 2) . '%');
        }

        $realisasi = [
            'totalRealisasi' => $totalRealisasi,
            'totalPagu' => $totalPagu,
            'realisasiKegiatan' => $realisasiKegiatan,
        ];

        return  [
            'total_realisasi' => number_format(round($realisasi['totalRealisasi'], 2)),
            'total_pagu' => number_format(round($realisasi['totalPagu'], 2)),
            'total_persen_realisasi_anggaran' => number_format(round(($realisasi['totalRealisasi'] / $realisasi['totalPagu']) * 100), 2),
            'tabel_per_kegiatan' => $tabel_per_kegiatan,
        ];
    }

    public function laporanKepegawaian($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $unit_id = Auth::user()->unit_id;

        $date = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '31');
        $dateQuery = $date->format('Y-m-d 23:59:59');

        $pegawai = Employe::where('unit_id', $unit_id)->get();
        $mutasi = MutasiPegawai::whereMonth('created_at',  $dateQuery)->get();
        $pengembangan = Pengembangan::whereMonth('created_at',  $dateQuery)->get();
        $kgb = KenaikanGajiBerkala::whereMonth('created_at',  $dateQuery)->get();
        $kepangkatan = KenaikanPangkat::whereMonth('created_at',  $dateQuery)->get();
        $pensiun = Pensiun::whereMonth('created_at',  $dateQuery)->get();
        $pangkat = Pangkat::all();
        $jabatan = Jabatan::all();

        foreach ($pangkat as $key => $value) {
            $count = Employe::where('unit_id', $unit_id)->where('pangkat_id', $value->id)->whereMonth('created_at', '<=', $dateQuery)->get()->count();
            $value->jumlah = $count;
        }

        foreach ($jabatan as $key => $value) {
            $count = Employe::where('unit_id', $unit_id)->where('jabatan_id', $value->id)->whereDate('created_at', '<=', $dateQuery)->get()->count();
            $value->jumlah = $count;
        }

        $umum = [
            'total_pegawai' => $pegawai->count(),
            'total_pegawai_laki' => $pegawai->where('gender', 'LAKI LAKI')->count(),
            'total_pegawai_perempuan' => $pegawai->where('gender', 'PEREMPUAN')->count(),
            'mutasi' => $mutasi->count(),
            'pengembangan' => $pengembangan->count(),
            'kgb' => $kgb->count(),
            'kepangkatan' => $kepangkatan->count(),
            'pensiun' => $pensiun->count(),
            'pangkat' => $pangkat,
            'jabatan' => $jabatan,
        ];



        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_total_pegawai = new Table($styleTable);
        // Tabel pegawai berdasarkan jenis
        $tabel_total_pegawai->addRow();
        $tabel_total_pegawai->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_total_pegawai->addCell(3000, $styleCell)->addText('Jenis', $headerTableStyle);
        $tabel_total_pegawai->addCell(2000, $styleCell)->addText('Jumlah Pegawai', $headerTableStyle);
        $tabel_total_pegawai->addRow();
        $tabel_total_pegawai->addCell(500, $styleCell)->addText('1');
        $tabel_total_pegawai->addCell(3000, $styleCell)->addText('Laki - Laki');
        $tabel_total_pegawai->addCell(2000, $styleCell)->addText($umum['total_pegawai_laki']);
        $tabel_total_pegawai->addRow();
        $tabel_total_pegawai->addCell(500, $styleCell)->addText('2');
        $tabel_total_pegawai->addCell(3000, $styleCell)->addText('Perempuan');
        $tabel_total_pegawai->addCell(2000, $styleCell)->addText($umum['total_pegawai_perempuan']);

        $data = [];

        // ADD TABLE
        $tabel_kepangkatan = new Table($styleTable);
        // Tabel pegawai berdasarkan jenis Pangkat
        $tabel_kepangkatan->addRow();
        $tabel_kepangkatan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_kepangkatan->addCell(3000, $styleCell)->addText('Pangkat', $headerTableStyle);
        $tabel_kepangkatan->addCell(2000, $styleCell)->addText('Jumlah Pegawai', $headerTableStyle);
        $number = 0;
        foreach ($umum['pangkat'] as $key => $pangkat) {
            if ($pangkat->jumlah == 0) {
                continue;
            }
            $tabel_kepangkatan->addRow();
            $tabel_kepangkatan->addCell(500)->addText(++$number);
            $tabel_kepangkatan->addCell(3000)->addText($pangkat->pangkat . ' - ' . $pangkat->ruang);
            $tabel_kepangkatan->addCell(2000)->addText($pangkat->jumlah);
        }
        $tabel_jabatan = new Table($styleTable);
        // Tabel pegawai berdasarkan jenis Jabatan
        $tabel_jabatan->addRow();
        $tabel_jabatan->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_jabatan->addCell(3000, $styleCell)->addText('Jabatan', $headerTableStyle);
        $tabel_jabatan->addCell(2000, $styleCell)->addText('Jumlah Pegawai', $headerTableStyle);
        $number = 0;
        foreach ($umum['jabatan'] as $key => $jabatan) {
            if ($jabatan->jumlah == 0) {
                continue;
            }
            $tabel_jabatan->addRow();
            $tabel_jabatan->addCell(500)->addText(++$number);
            $tabel_jabatan->addCell(3000)->addText($jabatan->name);
            $tabel_jabatan->addCell(2000)->addText($jabatan->jumlah);
        }

        $data = [
            'total_pegawai_perempuan' => $umum['total_pegawai_perempuan'],
            'total_pegawai_laki' => $umum['total_pegawai_laki'],
            'total_pegawai' => $umum['total_pegawai'],
            'tabel_jabatan' => $tabel_jabatan,
            'tabel_kepangkatan' => $tabel_kepangkatan,
            'tabel_total_pegawai' => $tabel_total_pegawai
        ];
        return $data;
    }

}
