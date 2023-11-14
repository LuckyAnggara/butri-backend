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

    public function generate($parameter)
    {

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



        // Data Kepegawaian 2D charts

        $dataKepegawaian = $this->laporanKepegawaian($parameter);
        $section = $phpWord->addSection();
        // Numbered heading


        $section->addTitle(htmlspecialchars('Demografi Pegawai Inspektorat Jenderal'), 1);
        $section->addTitle(htmlspecialchars('Total Pegawai'), 2);
        // $section->addText('Demografi Pegawai Inspektorat Jenderal', $header);
        // $section->addListItem(htmlspecialchars('Total Pegawai'), 0);
        $section->addText('Inspektorat Jenderal sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' memiliki pegawai dengan Jumlah ' . $dataKepegawaian['pegawai'] . ' pegawai', 'tStyle',  $justifyStyle);
        $section->addTitle(htmlspecialchars('Berdasarkan Jenis Kelamin'), 2);
        // $section->addListItem(htmlspecialchars('Berdasarkan Jenis Kelamin'), 0);
        $section->addTextBreak();
        $chart = $section->addChart('pie', $dataKepegawaian['categories1'], $dataKepegawaian['series1']);
        $chart->getStyle()->setWidth(Converter::inchToEmu(5))->setHeight(Converter::inchToEmu(4));

        $section->addText('Berdasarkan jenis kelamin, bisa dilihat pada diagram diatas, jumlah Pegawai dengan jenis kelamin Laki-laki adalah sebanyak ' . $dataKepegawaian['series1'][0] . ' pegawai dan Pegawai dengan jenis kelamin perempuan sebanyak' . $dataKepegawaian['series1'][1]  . ' pegawai', 'tStyle', $justifyStyle);

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

        // PERBELANJA
        $section->addTextBreak(1);
        // $section->addListItem(htmlspecialchars('Per Jenis Belanja'), 0);
        $section->addTitle(htmlspecialchars('Per Jenis Belanja'), 2);
        $phpWord->addTableStyle('Realisasi Anggaran Per Jenis Belanja', $styleTable, $styleFirstRow);
        $table = $section->addTable('Realisasi Anggaran Per Jenis Belanja');
        $table->addRow();
        $table->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Kegiatan', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Pagu (Rp.)', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Realisasi (Rp.)', $headerTableStyle);
        $table->addCell(500, $styleCell)->addText('%', $headerTableStyle);
        $number = 0;
        foreach ($dataAnggaran['realisasiBelanja'] as $key => $belanja) {
            $table->addRow();
            $table->addCell(500)->addText(++$number);
            $table->addCell(4000)->addText($belanja->name);
            $table->addCell(2000)->addText(number_format(round($belanja->pagu, 2)));
            $table->addCell(2000)->addText(number_format(round($belanja->realisasi_saat_ini, 2)));
            $table->addCell(500)->addText(round(($belanja->realisasi_saat_ini / $belanja->pagu) * 100, 2) . '%');
        }
        $section->addPageBreak();

        //CAPAIAN IKU
        $sectionStyle = array(
            'orientation' => 'landscape',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $dataIKU = $this->laporanIKU($parameter);

        $section->addTextBreak(1);
        $section->addTitle(htmlspecialchars('Capaian Indikator Kinerja Utama'), 1);
        $section->addText('Capaian Indikator Kinerja Utama Inspektorat Jenderal sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);
        // $section->addText('Capaian Indikator Kinerja Utama', $header);
        $phpWord->addTableStyle('Indikator Kinerja Utama', $styleTable, $styleFirstRow);
        $table = $section->addTable('Indikator Kinerja Utama');

        $table->addRow();
        $table->addCell(4000, $styleCell)->addText('Indikator', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Target', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Realisasi', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Analisis', $headerTableStyle);
        $table->addCell(4000, $styleCell)->addText('Kendala / Hambatan', $headerTableStyle);
        foreach ($dataIKU as $key => $iku) {
            $table->addRow();
            $table->addCell(4000)->addText($iku->name);
            $table->addCell(2000)->addText($iku->target);
            $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $iku->realisasi->realisasi ?? ''));
            $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $iku->realisasi->analisa ?? ''));
            $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $iku->realisasi->kendala ?? ''));
        }
        $section->addPageBreak();


        //CAPAIAN IKK

        $dataIKK = $this->laporanIKK($parameter);
        $section->addTitle(htmlspecialchars('Capaian Indikator Kinerja Kegiatan'), 1);
        $section->addText('Capaian Indikator Kinerja Kegiatan pada masing - masing Unit Eselon II di lingkungan Inspektorat Jenderal sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);
        // $section->addText('Capaian Indikator Kinerja Kegiatan');
        foreach ($dataIKK as $key => $value) {
            $section->addTitle(htmlspecialchars($value->name), 2);
            // $section->addListItem(htmlspecialchars('Capaian Indikator Kinerja Kegiatan ' . $value->name), 0);
            $phpWord->addTableStyle('Indikator Kinerja Kegiatan', $styleTable, $styleFirstRow);
            $table = $section->addTable('Indikator Kinerja Kegiatan');
            $table->addRow();
            $table->addCell(4000, $styleCell)->addText('Indikator', $headerTableStyle);
            $table->addCell(2000, $styleCell)->addText('Target', $headerTableStyle);
            $table->addCell(4000, $styleCell)->addText('Realisasi', $headerTableStyle);
            $table->addCell(4000, $styleCell)->addText('Analisis', $headerTableStyle);
            $table->addCell(4000, $styleCell)->addText('Kendala / Hambatan', $headerTableStyle);
            foreach ($value->ikk as $key => $ikk) {
                $table->addRow();
                $table->addCell(4000)->addText($ikk->name);
                $table->addCell(2000)->addText($ikk->target);
                $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->realisasi ?? ''));
                $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->analisa ?? ''));
                $table->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $ikk->realisasi->kendala ?? ''));
            }
            $section->addPageBreak();
        }

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
        $section->addTitle(htmlspecialchars('Data Kegiatan Inspektorat Jenderal'), 1);
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

        // DATA PERSURATAN
        $dataPersuratan = $this->laporanPersuratan($parameter);
        $sectionStyle = array(
            'orientation' => 'portrait',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $section->addTextBreak(1);
        $section->addTitle(htmlspecialchars('Data Kegiatan Inspektorat Jenderal'), 1);
        $section->addText('Data Kegiatan pada bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);
        $phpWord->addTableStyle('Data Kegiatan', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Kegiatan');
        $table->addRow();
        $table->addCell(500, $styleCell)->addText('Bulan', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Surat Masuk', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Surat Keluar', $headerTableStyle);

        $number = 0;
        $totalSuratMasuk = 0;
        $totalSuratKeluar = 0;
        foreach ($dataPersuratan as $key => $persuratan) {
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

        // DATA MONITORING INTERNAL
        $dataMonitoringInternal = $this->laporanMonitoringInternal($parameter);
        $section->addPageBreak();
        $sectionStyle = array(
            'orientation' => 'landscape',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $section->addTitle(htmlspecialchars('Data Monitoring Temuan Internal'), 1);
        $section->addText('Data Monitoring Temuan Internal sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);

        $styleTable = array('borderSize' => 6, 'borderColor' => '999999');
        $cellRowSpan = array('vMerge' => 'restart', 'align' => 'center', 'valign' => 'center', 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF', 'borderBottomSize' => 18,);
        $cellRowContinue = array('vMerge' => 'continue', 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center', 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');
        $cellVCentered = array('valign' => 'center', 'borderBottomColor' => '0000FF', 'bgColor' => '66BBFF');

        $phpWord->addTableStyle('Data Temuan Internal', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Temuan Internal');

        $table->addRow();

        $table->addCell(2000, $cellRowSpan)->addText('Unit', $headerTableStyle);
        $table->addCell(4000, $cellColSpan)->addText('Temuan', $headerTableStyle);
        $table->addCell(4000, $cellColSpan)->addText('Sudah Tindak Lanjut', $headerTableStyle);
        $table->addCell(4000, $cellColSpan)->addText('Belum Tindak Lanjut', $headerTableStyle);
        $table->addRow();
        $table->addCell(null, $cellRowContinue);
        $table->addCell(2000, $cellVCentered)->addText('Jumlah', $headerTableStyle);
        $table->addCell(2000, $cellVCentered)->addText('Nominal (Rp.)', $headerTableStyle);
        $table->addCell(2000, $cellVCentered)->addText('Jumlah', $headerTableStyle);
        $table->addCell(2000, $cellVCentered)->addText('Nominal (Rp.)', $headerTableStyle);
        $table->addCell(2000, $cellVCentered)->addText('Jumlah', $headerTableStyle);
        $table->addCell(2000, $cellVCentered)->addText('Nominal (Rp.)', $headerTableStyle);

        $totalTemuanJumlah = 0;
        $totalTemuanNominal = 0;
        $totalTlJumlah = 0;
        $totalTlNominal = 0;
        $totalBtlJumlah = 0;
        $totalBtlNominal = 0;

        foreach ($dataMonitoringInternal as $key => $internal) {
            $totalTemuanJumlah += $internal->temuan_jumlah;
            $totalTemuanNominal += $internal->temuan_jumlah;
            $totalTlJumlah += $internal->tl_jumlah;
            $totalTlNominal += $internal->tl_nominal;
            $totalBtlJumlah += $internal->btl_jumlah;
            $totalBtlNominal += $internal->btl_nominal;

            $table->addRow();
            $table->addCell(2000)->addText($internal->group->name);
            $table->addCell(2000)->addText(number_format(round($internal->temuan_jumlah, 2)));
            $table->addCell(2000)->addText(number_format(round($internal->temuan_nominal, 2)));
            $table->addCell(2000)->addText(number_format(round($internal->tl_jumlah, 2)));
            $table->addCell(2000)->addText(number_format(round($internal->tl_nominal, 2)));
            $table->addCell(2000)->addText(number_format(round($internal->btl_jumlah, 2)));
            $table->addCell(2000)->addText(number_format(round($internal->btl_nominal, 2)));
        }
        $table->addRow();

        $table->addCell(2000)->addText('Total', $headerTableStyle);
        $table->addCell(2000)->addText(number_format(round($totalTemuanJumlah)), $headerTableStyle);
        $table->addCell(2000)->addText(number_format(round($totalTemuanNominal)), $headerTableStyle);
        $table->addCell(2000)->addText(number_format(round($totalTlJumlah)), $headerTableStyle);
        $table->addCell(2000)->addText(number_format(round($totalTlNominal)), $headerTableStyle);
        $table->addCell(2000)->addText(number_format(round($totalBtlJumlah)), $headerTableStyle);
        $table->addCell(2000)->addText(number_format(round($totalBtlNominal)), $headerTableStyle);


        // DATA MONITORING EKSTERNAL
        $dataEksternal = $this->laporanMonitoringExternal($parameter);
        $sectionStyle = array(
            'orientation' => 'portrait',
            'marginTop' => 600,
        );
        $section = $phpWord->addSection($sectionStyle);
        $section->addTextBreak(1);
        $section->addTitle(htmlspecialchars('Data Monitoring Temuan Internal'), 1);
        $section->addText('Data Monitoring Temuan Internal sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);

        // BPK
        $section->addTitle(htmlspecialchars('Temuan Badan Pemeriksa Keuangan'), 2);
        $phpWord->addTableStyle('Data Montoring Eksternal', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Montoring Eksternal');

        $table->addRow();
        $table->addCell(4000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Jumlah', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Nominal', $headerTableStyle);

        foreach ($dataEksternal['bpk'] as $key => $bpk) {
            $table->addRow();
            $table->addCell(4000)->addText($bpk->keterangan);
            $table->addCell(2000)->addText($bpk->jumlah);
            $table->addCell(3000)->addText(number_format(round($bpk->nominal, 2)));
        }
        // BPKP
        $section->addTitle(htmlspecialchars('Temuan Badan Pengawasan Keuangan dan Pembangunan'), 2);
        $phpWord->addTableStyle('Data Montoring Eksternal', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Montoring Eksternal');

        $table->addRow();
        $table->addCell(4000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Jumlah', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Nominal', $headerTableStyle);
        $section->addTextBreak(1);

        foreach ($dataEksternal['bpkp'] as $key => $bpkp) {
            $table->addRow();
            $table->addCell(4000)->addText($bpkp->keterangan);
            $table->addCell(2000)->addText($bpkp->jumlah);
            $table->addCell(3000)->addText(number_format(round($bpkp->nominal, 2)));
        }

        // ORI
        $section->addTitle(htmlspecialchars('Temuan Ombudsman Republik Indonesia'), 2);
        $phpWord->addTableStyle('Data Montoring Eksternal', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Montoring Eksternal');

        $table->addRow();
        $table->addCell(4000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $table->addCell(2000, $styleCell)->addText('Jumlah', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Nominal', $headerTableStyle);
        $section->addTextBreak(1);

        foreach ($dataEksternal['ori'] as $key => $ori) {
            $table->addRow();
            $table->addCell(4000)->addText($ori->keterangan);
            $table->addCell(2000)->addText($ori->jumlah);
            $table->addCell(3000)->addText(number_format(round($ori->nominal, 2)));
        }


        // PENGELOLAAN MEDIA

        $dataPengelolaanMedia = $this->laporanPengelolaanMedia($parameter);

        $section->addTextBreak(1);
        $section->addTitle(htmlspecialchars('Data Pengelolaan Media'), 1);
        $section->addText('Data Pengelolaan Media Inspektorat Jenderal sampai dengan bulan ' . $monthName . ' tahun ' . $parameter->tahun . ' adalah sebagai berikut:', 'tStyle',  $justifyStyle);

        $phpWord->addTableStyle('Data Pengelolaan Media', $styleTable, $styleFirstRow);
        $table = $section->addTable('Data Pengelolaan Media');

        $table->addRow();
        $table->addCell(1000, $styleCell)->addText('Jenis', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Link Media', $headerTableStyle);
        $section->addTextBreak(1);

        foreach ($dataPengelolaanMedia as $key => $media) {
            $table->addRow();
            $table->addCell(1000)->addText($media->type);
            $table->addCell(3000)->addText($media->keterangan);
            $table->addCell(3000)->addText($media->link);
        }



        // Save file
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');

        $time = Carbon::now()->format('YmdHis');
        $name = 'laporan' . $time . '.docx';
        $objWriter->save(public_path("\laporan\\" . $name));

        return $name;
    }

    public function laporanPengelolaanMedia($parameter)
    {
        $tahun =  $parameter->tahun;
        $bulan =  $parameter->bulan;

        $data = PengelolaanMedia::where('tahun', $tahun)->where('bulan', $bulan)->get();
        return $data;
    }


    public function laporanMonitoringExternal($parameter)
    {

        $tahun =  $parameter->tahun;
        $bulan =  $parameter->bulan;

        $bpk = MonitoringTemuanBpk::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();
        if (count($bpk) < 1) {
            $data1 = new Bpk('Sesuai dengan Rekomendasi', '0', '0');
            $data2 = new Bpk('Rekomendasi dalam Proses Reviu BPK ', '0', '0');
            $data3 = new Bpk('Belum Sesuai/Dalam Proses Tindak Lanjut', '0', '0');
            $data4 = new Bpk('Belum Ditindaklanjuti', '0', '0');
            $data5 = new Bpk('Tidak Dapat Ditindaklanjuti dengan Alasan yang Sah', '0', '0');
            $data6 = new Bpk('Temuan Pemeriksaan', '0', '0');
            $data6 = new Bpk('Rekomendasi', '0', '0');

            $bpk = [$data1, $data2, $data3, $data4, $data5, $data6];
        }

        $bpkp = MonitoringTemuanBpkp::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();

        if (count($bpkp) < 1) {
            $data1 = new Bpk('Sudah Tuntas', '0', '0');
            $data2 = new Bpk('Belum Tuntas ', '0', '0');
            $data3 = new Bpk('Total', '0', '0');
            $bpkp = [$data1, $data2, $data3];
        }

        $ori = MonitoringTemuanOri::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();

        if (count($ori) < 1) {
            $data1 = new Bpk('Jumlah Aduan', '0', '0');
            $data2 = new Bpk('Tuntas ', '0', '0');
            $data3 = new Bpk('Belum Tuntas', '0', '0');
            $ori = [$data1, $data2, $data3];
        }


        return [
            'bpk' => $bpk,
            'bpkp' => $bpkp,
            'ori' => $ori,
        ];
    }

    public function laporanMonitoringInternal($parameter)
    {

        $tahun =  $parameter->tahun;
        $bulan =  $parameter->bulan;

        $data = MonitoringPengawasanItwil::with('group')->where('tahun', $tahun)->where('bulan', $bulan)->get();

        return $data;
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

        return $result;
    }

    public function laporanKegiatanLainnya($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;

        $startDate = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '01');
        $endDate = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '31');

        $data = Kegiatan::with('unit')->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $startDate = $startDate->format('Y-m-d 00:00:00');
            $endDate = $endDate->format('Y-m-d 23:59:59');
            return $query->whereBetween('start_at', [$startDate, $endDate]);
        })->get();


        return $data;
    }

    public function laporanIKU($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $group = $parameter->group;

        $data = IndikatorKinerjaUtama::when($tahun, function ($query, $tahun) {
            return $query->whereYear('created_at', $tahun);
        })->get();

        foreach ($data as $key => $value) {
            $value->realisasi = CapaianIndikatorKegiatanUtama::where('iku_id', $value->id)->when($tahun, function ($query, $tahun) {
                return $query->whereYear('created_at', $tahun);
            })->first();
        }
        return $data;
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

    public function laporanDataPengawasan($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;
        $jenisPengawasan = JenisPengawasan::all();
        $dataPengawasan = [];

        foreach ($jenisPengawasan as $key => $value) {
            $result = DataPengawasan::where('jenis_pengawasan_id', $value->id)->where('tahun',  $tahun)
                ->when($bulan, function ($query, $bulan) {
                    return $query->whereMonth('created_at', '<=', $bulan);
                })
                ->get()->count();
            $dataPengawasan[] = new Pengawasan($value->name, $result);
        }

        return $dataPengawasan;
    }

    public function laporanAnggaran($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;


        // ANGGARAN
        $realisasiKegiatan = Dipa::with(['group', 'realisasi' => function ($query) use ($bulan) {
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

    public function laporanKepegawaian($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;

        $date = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '28');
        $dateQuery = $date->format('Y-m-d 23:59:59');

        $pegawai = Employe::whereDate('created_at', '<=', $dateQuery)->get();
        $mutasi = MutasiPegawai::whereMonth('created_at',  $dateQuery)->get();
        $pengembangan = Pengembangan::whereMonth('created_at',  $dateQuery)->get();
        $kgb = KenaikanGajiBerkala::whereMonth('created_at',  $dateQuery)->get();
        $kepangkatan = KenaikanPangkat::whereMonth('created_at',  $dateQuery)->get();
        $pensiun = Pensiun::whereMonth('created_at',  $dateQuery)->get();


        $data = [
            'categories1' => array('Laki - Laki', 'Perempuan'),
            'series1' => array($pegawai->where('gender', 'LAKI LAKI')->count(), $pegawai->where('gender', 'PEREMPUAN')->count()),
            'pegawai' => $pegawai->count(),
            'mutasi' => $mutasi->count(),
            'pengembangan' => $pengembangan->count(),
            'kgb' => $kgb->count(),
            'kepangkatan' => $kepangkatan->count(),
            'pensiun' => $pensiun->count(),
        ];

        return $data;
    }

    public function view()
    {
        $parameter = [
            'tahun' => 2023,
            'bulan' => 11,
        ];
        $tahun = 2023;
        $bulan = 11;
        $group = 1;

        $pengelolaanMedia = PengelolaanMedia::where('tahun', $tahun)->where('bulan', $bulan)->get();


        $bpk = MonitoringTemuanBpk::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();
        if (count($bpk) < 1) {
            $data1 = new Bpk('Sesuai dengan Rekomendasi', '0', '0');
            $data2 = new Bpk('Rekomendasi dalam Proses Reviu BPK ', '0', '0');
            $data3 = new Bpk('Belum Sesuai/Dalam Proses Tindak Lanjut', '0', '0');
            $data4 = new Bpk('Belum Ditindaklanjuti', '0', '0');
            $data5 = new Bpk('Tidak Dapat Ditindaklanjuti dengan Alasan yang Sah', '0', '0');
            $data6 = new Bpk('Temuan Pemeriksaan', '0', '0');
            $data6 = new Bpk('Rekomendasi', '0', '0');

            $bpk = [$data1, $data2, $data3, $data4, $data5, $data6];
        }

        $bpkp = MonitoringTemuanBpkp::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();

        if (count($bpkp) < 1) {
            $data1 = new Bpk('Sudah Tuntas', '0', '0');
            $data2 = new Bpk('Belum Tuntas ', '0', '0');
            $data3 = new Bpk('Total', '0', '0');
            $bpkp = [$data1, $data2, $data3];
        }

        $ori = MonitoringTemuanOri::when($tahun, function ($query, $tahun) {
            return $query->where('tahun', $tahun);
        })
            ->when($bulan, function ($query, $bulan) {
                return $query->where('bulan', $bulan);
            })
            ->get();

        if (count($ori) < 1) {
            $data1 = new Bpk('Jumlah Aduan', '0', '0');
            $data2 = new Bpk('Tuntas ', '0', '0');
            $data3 = new Bpk('Belum Tuntas', '0', '0');
            $ori = [$data1, $data2, $data3];
        }


        return [
            'bpk' => $bpk,
            'bpkp' => $bpkp,
            'ori' => $ori,
        ];


        $startDate = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '01');
        $endDate = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '31');

        $dataMonitoringInternal = MonitoringPengawasanItwil::with('group')->where('tahun', $tahun)->where('bulan', $bulan)->get();
        return $dataMonitoringInternal;

        $dataKegiatan = Kegiatan::with('unit')->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $startDate = $startDate->format('Y-m-d 00:00:00');
            $endDate = $endDate->format('Y-m-d 23:59:59');
            return $query->whereBetween('start_at', [$startDate, $endDate]);
        })->get();



        $jenisPengawasan = JenisPengawasan::all();
        $dataPengawasan = [];

        foreach ($jenisPengawasan as $key => $value) {
            $result = DataPengawasan::where('jenis_pengawasan_id', $value->id)->where('tahun',  $tahun)
                ->when($bulan, function ($query, $bulan) {
                    return $query->whereMonth('created_at', '<=', $bulan);
                })
                ->get()->count();
            $dataPengawasan[] = new Pengawasan($value->name, $result);
        }


        // IKK
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

        // ANGGARAN
        $realisasiKegiatan = Dipa::with(['group', 'realisasi' => function ($query) use ($bulan) {
            $query->where('bulan', '<=', $bulan);
        }])
            ->where('jenis', 'kegiatan')
            ->when($tahun, function ($query, $tahun) {
                return $query->where('tahun', $tahun);
            })
            ->get();

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


        $realisasiIKU = IndikatorKinerjaUtama::when($tahun, function ($query, $tahun) {
            return $query->whereYear('created_at', $tahun);
        })->get();

        foreach ($realisasiIKU as $key => $value) {
            $value->realisasi = CapaianIndikatorKegiatanUtama::where('iku_id', $value->id)->when($tahun, function ($query, $tahun) {
                return $query->whereYear('created_at', $tahun);
            })->first();
        }

        return view('laporan.view', ['groups' => $groupAll, 'realisasiKegiatan' => $realisasiKegiatan, 'realisasiBelanja' => $realisasiBelanja, 'realisasiIKU' => $realisasiIKU]);
    }
}
