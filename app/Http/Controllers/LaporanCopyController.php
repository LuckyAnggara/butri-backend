<?php

namespace App\Http\Controllers;

use App\Bpk;
use App\Classes\ExtendedTemplateProcessor;
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
use App\Models\Laporan;
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
use App\Pengawasan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\Shared\XMLWriter;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Writer\Word2007\Element\Container;

class LaporanCopyController extends BaseController
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
        $parameter = $data->parameter;
        $name = $this->generate($data);

        $ttd_tanggal =  Carbon::createFromFormat('d M Y', $data->ttd_tanggal)->format('Y-m-d 00:00:00');

        try {
            DB::beginTransaction();
            $result = Laporan::create([
                'tahun' => $parameter->tahun,
                'bulan' => $parameter->bulan,
                'name' => $name,
                'link' => $name,
                'ttd_jabatan' => $data->ttd_jabatan,
                'ttd_name'  => $data->ttd_name,
                'ttd_nip'  => $data->ttd_nip,
                'ttd_tanggal'  => $ttd_tanggal,
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

    public function generate($data)
    {
        $parameter = $data->parameter;
        $ttd = $data;

        $dateForMonth = Carbon::create(null, $parameter->bulan, 1);
        // Format the date to get the month name
        $monthName = $dateForMonth->format('F');
        $templateProcessor = new TemplateProcessor(public_path('template.docx'));
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
        $templateProcessor->setComplexBlock('tabel_per_belanja', $anggaran['tabel_per_belanja']);


        $iku = $this->laporanIKU($parameter);
        $templateProcessor->setComplexBlock('tabel_capaian_iku', $iku);

        $ikk = $this->laporanIKK($parameter);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk_1', $ikk['tabel_capaian_ikk_1']);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk_2', $ikk['tabel_capaian_ikk_2']);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk_3', $ikk['tabel_capaian_ikk_3']);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk_4', $ikk['tabel_capaian_ikk_4']);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk_5', $ikk['tabel_capaian_ikk_5']);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk_6', $ikk['tabel_capaian_ikk_6']);
        $templateProcessor->setComplexBlock('tabel_capaian_ikk_7', $ikk['tabel_capaian_ikk_7']);

        $pengawasan = $this->laporanDataPengawasan($parameter);

        $templateProcessor->setComplexBlock('tabel_rekapitulasi_pengawasan', $pengawasan['tabel_rekapitulasi_pengawasan']);
        $templateProcessor->setComplexBlock('tabel_detail_pengawasan', $pengawasan['tabel_detail_pengawasan']);

        $kegiatan = $this->laporanKegiatanLainnya($parameter);
        $templateProcessor->setComplexBlock('tabel_kegiatan', $kegiatan);

        $monitoringInternal = $this->laporanMonitoringInternal($parameter);
        $templateProcessor->setComplexBlock('tabel_temuan_apip', $monitoringInternal);

        $monitoringEksternal = $this->laporanMonitoringExternal($parameter);
        $templateProcessor->setComplexBlock('tabel_bpk', $monitoringEksternal['tabel_bpk']);
        $templateProcessor->setComplexBlock('tabel_bpkp', $monitoringEksternal['tabel_bpkp']);
        $templateProcessor->setComplexBlock('tabel_ori', $monitoringEksternal['tabel_ori']);

        $persuratan = $this->laporanPersuratan($parameter);
        $templateProcessor->setComplexBlock('tabel_persuratan', $persuratan);

        $pengelolaanMedia = $this->laporanPengelolaanMedia($parameter);
        $templateProcessor->setComplexBlock('tabel_pengelolaan_media', $pengelolaanMedia);


        $programUnggulan = $this->laporanCapaianProgramUnggulan($parameter);
        $templateProcessor->setComplexBlock('tabel_capaian_program_unggulan', $programUnggulan);

        $time = Carbon::now()->format('is');
        $name = 'Laporan' . $monthName . $parameter->tahun . $time . '.docx';
        $templateProcessor->saveAs(public_path($name));

        return $name;
    }

    public function laporanCapaianProgramUnggulan()
    {
        $tahun =  2023;
        $bulan =  11;

        $breaks = array("<br />", "<br>", "<br/>");
        $programUnggulan = ProgramUnggulan::with('list')->where('tahun', $tahun)->get();

        foreach ($programUnggulan as $key => $value) {
            $value->jumlah = $value->list->count();
        }

        // TABEL CAPAIAN IKU
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $styleCellSpan = array('valign' => 'center', 'size' => 11, 'gridSpan' => 5);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $table = new Table($styleTable);
        $table->addRow();
        $table->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $table->addCell(5000, $styleCell)->addText('Nama Program', $headerTableStyle);
        $table->addCell(1000, $styleCell)->addText('Total Kegiatan', $headerTableStyle);

        $number = 0;
        foreach ($programUnggulan as $key => $program) {
            $table->addRow();
            $table->addCell(500)->addText(++$number);
            $table->addCell(5000)->addText($program->name);
            $table->addCell(1000)->addText($program->jumlah);
        }


        // RINCIAN PROGRAM UNGGULAN


        //  // TABEL CAPAIAN IKU
        // $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        // $styleCell = array('valign' => 'center', 'size' => 11);
        // $styleCellSpan = array('valign' => 'center', 'size' => 11, 'gridSpan'=>5);
        // $headerTableStyle = array('bold' => true, 'align' => 'center');


        // // ADD TABLE
        // $table = new Table($styleTable);
        // $table->addRow();
        // $table->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        // $table->addCell(1500, $styleCell)->addText('Unit', $headerTableStyle);
        // $table->addCell(6000, $styleCell)->addText('Nama Kegiatan', $headerTableStyle);
        // $table->addCell(3000, $styleCell)->addText('Tmt dan Lokasi', $headerTableStyle);
        // $table->addCell(3000, $styleCell)->addText('No dan Tgl LHP', $headerTableStyle);


        // $number = 0;
        // foreach ($programUnggulan as $key => $program) {
        //     $table->addRow();
        //     $table->addCell(4000, $styleCellSpan)->addText(++$number . '. '. $program->name);
        //     if($program->jumlah == 0){
        //             $table->addRow();
        //             $table->addCell(4000, $styleCellSpan)->addText('nihil');
        //     }else{
        //         $num = 0;
        //         foreach ($program->list as $key => $detail) {
        //             $table->addRow();
        //             $table->addCell(500)->addText(++$num);
        //             $table->addCell(1500)->addText($detail->kegiatan->unit->name);
        //             $table->addCell(6000)->addText(str_ireplace($breaks, "\r\n",$detail->kegiatan->name));
        //             $table->addCell(3000)->addText($detail->kegiatan->tempat . '</w:t><w:br/><w:t>' .Carbon::create($detail->kegiatan->start_at)->format('d F Y') . ' s.d ' . Carbon::create($detail->kegiatan->start_at)->format('d F Y'));
        //         }
        //     }
        // }

        return $table;
    }


    public function laporanPengelolaanMedia($parameter)
    {
        $tahun =  $parameter->tahun;
        $bulan =  $parameter->bulan;

        $data = PengelolaanMedia::where('tahun', $tahun)->where('bulan', $bulan)->get();

        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $table = new Table($styleTable);
        $table->addRow();
        $table->addCell(1000, $styleCell)->addText('Jenis', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $table->addCell(3000, $styleCell)->addText('Link Media', $headerTableStyle);

        foreach ($data as $key => $media) {
            $table->addRow();
            $table->addCell(1000)->addText($media->type);
            $table->addCell(3000)->addText($media->keterangan);
            $table->addCell(3000)->addText($media->link);
        }

        return $table;
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

        // BPK
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_bpk = new Table($styleTable);
        $tabel_bpk->addRow();
        $tabel_bpk->addCell(4000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $tabel_bpk->addCell(2000, $styleCell)->addText('Jumlah', $headerTableStyle);
        $tabel_bpk->addCell(3000, $styleCell)->addText('Nominal (Rp.)', $headerTableStyle);

        foreach ($bpk as $key => $bpk) {
            $tabel_bpk->addRow();
            $tabel_bpk->addCell(4000)->addText($bpk->keterangan);
            $tabel_bpk->addCell(2000)->addText($bpk->jumlah);
            $tabel_bpk->addCell(3000)->addText(number_format(round($bpk->nominal, 2)));
        }

        // BPKP
        $tabel_bpkp = new Table($styleTable);
        $tabel_bpkp->addRow();
        $tabel_bpkp->addCell(4000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $tabel_bpkp->addCell(2000, $styleCell)->addText('Jumlah', $headerTableStyle);
        $tabel_bpkp->addCell(3000, $styleCell)->addText('Nominal (Rp.)', $headerTableStyle);

        foreach ($bpkp as $key => $bpkp) {
            $tabel_bpkp->addRow();
            $tabel_bpkp->addCell(4000)->addText($bpkp->keterangan);
            $tabel_bpkp->addCell(2000)->addText($bpkp->jumlah);
            $tabel_bpkp->addCell(3000)->addText(number_format(round($bpkp->nominal, 2)));
        }

        // ORI
        $tabel_ori = new Table($styleTable);
        $tabel_ori->addRow();
        $tabel_ori->addCell(4000, $styleCell)->addText('Keterangan', $headerTableStyle);
        $tabel_ori->addCell(2000, $styleCell)->addText('Jumlah', $headerTableStyle);
        $tabel_ori->addCell(3000, $styleCell)->addText('Nominal (Rp.)', $headerTableStyle);

        foreach ($ori as $key => $ori) {
            $tabel_ori->addRow();
            $tabel_ori->addCell(4000)->addText($ori->keterangan);
            $tabel_ori->addCell(2000)->addText($ori->jumlah);
            $tabel_ori->addCell(3000)->addText(number_format(round($ori->nominal, 2)));
        }

        return  [
            'tabel_bpk' => $tabel_bpk,
            'tabel_bpkp' => $tabel_bpkp,
            'tabel_ori' => $tabel_ori,
        ];
    }




    public function laporanMonitoringInternal($parameter)
    {

        $tahun =  $parameter->tahun;
        $bulan =  $parameter->bulan;

        $data = MonitoringPengawasanItwil::with('group')->where('tahun', $tahun)->where('bulan', $bulan)->get();

        // TABEL CAPAIAN IKU
        $cellRowSpan = array('vMerge' => 'restart', 'align' => 'center', 'valign' => 'center', 'borderBottomSize' => 18,);
        $cellRowContinue = array('vMerge' => 'continue',);
        $cellColSpan = array('gridSpan' => 2, 'valign' => 'center',);
        $cellVCentered = array('valign' => 'center',);
        $styleTable = array('borderSize' => 6,  'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $table = new Table($styleTable);
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

        foreach ($data as $key => $internal) {
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

        return $table;
    }


    public function laporanKegiatanLainnya($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;

        $breaks = array("<br />", "<br>", "<br/>");


        $kegiatans = Kegiatan::with('unit')->whereYear('start_at', $tahun)->whereMonth('start_at', $bulan)->get();

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
            $tabel_kegiatan->addCell(6500)->addText(str_ireplace($breaks, "\r\n", $kegiatan->name));
            $tabel_kegiatan->addCell(1500)->addText($kegiatan->jenis_kegiatan);
            $tabel_kegiatan->addCell(4000)->addText($kegiatan->tempat . '</w:t><w:br/><w:t>' . Carbon::create($kegiatan->start_at)->format('d F Y') . ' s.d ' . Carbon::create($kegiatan->start_at)->format('d F Y'));
        }

        return $tabel_kegiatan;
    }

    public function laporanDataPengawasan($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;


        $breaks = array("<br />", "<br>", "<br/>");

        $jenisPengawasan = JenisPengawasan::all();
        foreach ($jenisPengawasan as $key => $value) {
            $detail = DataPengawasan::where('jenis_pengawasan_id', $value->id)->with('unit')->where('tahun',  $tahun)
                ->when($bulan, function ($query, $bulan) {
                    return $query->where('bulan', '<=', $bulan);
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
                    $tabel_detail_pengawasan->addCell(1500)->addText($detail->unit->name);
                    $tabel_detail_pengawasan->addCell(6000)->addText(str_ireplace($breaks, "\r\n", $detail->name));
                    $tabel_detail_pengawasan->addCell(3000)->addText($detail->location . '</w:t><w:br/><w:t>' . Carbon::create($detail->start_at)->format('d F Y') . ' s.d ' . Carbon::create($detail->start_at)->format('d F Y'));
                    $tabel_detail_pengawasan->addCell(3000)->addText($detail->output);
                }
            }
        }


        return  [
            'tabel_rekapitulasi_pengawasan' => $tabel_rekapitulasi_pengawasan,
            'tabel_detail_pengawasan' => $tabel_detail_pengawasan,
        ];
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

        // TABEL CAPAIAN IKU
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_capaian_iku = new Table($styleTable);
        $tabel_capaian_iku->addRow();
        $tabel_capaian_iku->addCell(4000, $styleCell)->addText('Indikator', $headerTableStyle);
        $tabel_capaian_iku->addCell(2000, $styleCell)->addText('Target', $headerTableStyle);
        $tabel_capaian_iku->addCell(4000, $styleCell)->addText('Realisasi', $headerTableStyle);
        $tabel_capaian_iku->addCell(4000, $styleCell)->addText('Analisis', $headerTableStyle);
        $tabel_capaian_iku->addCell(4000, $styleCell)->addText('Kendala / Hambatan', $headerTableStyle);
        foreach ($data as $key => $iku) {
            $tabel_capaian_iku->addRow();
            $tabel_capaian_iku->addCell(4000)->addText($iku->name);
            $tabel_capaian_iku->addCell(2000)->addText($iku->target);
            $tabel_capaian_iku->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $iku->realisasi->realisasi ?? ''));
            $tabel_capaian_iku->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $iku->realisasi->analisa ?? ''));
            $tabel_capaian_iku->addCell(3000)->addText(str_replace('<br />', '</w:t><w:br/><w:t>', $iku->realisasi->kendala ?? ''));
        }

        return $tabel_capaian_iku;
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

        // TABEL CAPAIAN IKK
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $number = 0;
        foreach ($groupAll as $key => $value) {
            ++$number;
            $table = new Table($styleTable);
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
            $data['tabel_capaian_ikk_' . $number] = $table;
        }
        return $data;
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

        $realisasi = [
            'totalRealisasi' => $totalRealisasi,
            'totalPagu' => $totalPagu,
            'realisasiKegiatan' => $realisasiKegiatan,
            'realisasiBelanja' => $realisasiBelanja,
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

        // PERBELANJA
        $styleTable = array('borderSize' => 6, 'borderColor' => '006699', 'cellMargin' => 80);
        $styleCell = array('valign' => 'center', 'size' => 11);
        $headerTableStyle = array('bold' => true, 'align' => 'center');
        // ADD TABLE
        $tabel_per_belanja = new Table($styleTable);
        $tabel_per_belanja->addRow();
        $tabel_per_belanja->addCell(500, $styleCell)->addText('#', $headerTableStyle);
        $tabel_per_belanja->addCell(4000, $styleCell)->addText('Kegiatan', $headerTableStyle);
        $tabel_per_belanja->addCell(2000, $styleCell)->addText('Pagu (Rp.)', $headerTableStyle);
        $tabel_per_belanja->addCell(2000, $styleCell)->addText('Realisasi (Rp.)', $headerTableStyle);
        $tabel_per_belanja->addCell(500, $styleCell)->addText('%', $headerTableStyle);
        $number = 0;
        foreach ($realisasi['realisasiBelanja'] as $key => $belanja) {
            $tabel_per_belanja->addRow();
            $tabel_per_belanja->addCell(500)->addText(++$number);
            $tabel_per_belanja->addCell(4000)->addText($belanja->name);
            $tabel_per_belanja->addCell(2000)->addText(number_format(round($belanja->pagu, 2)));
            $tabel_per_belanja->addCell(2000)->addText(number_format(round($belanja->realisasi_saat_ini, 2)));
            $tabel_per_belanja->addCell(500)->addText(round(($belanja->realisasi_saat_ini / $belanja->pagu) * 100, 2) . '%');
        }

        $realisasi = [
            'totalRealisasi' => $totalRealisasi,
            'totalPagu' => $totalPagu,
            'realisasiKegiatan' => $realisasiKegiatan,
            'realisasiBelanja' => $realisasiBelanja,
        ];

        return  [
            'total_realisasi' => number_format(round($realisasi['totalRealisasi'], 2)),
            'total_pagu' => number_format(round($realisasi['totalPagu'], 2)),
            'total_persen_realisasi_anggaran' => number_format(round(($realisasi['totalRealisasi'] / $realisasi['totalPagu']) * 100), 2),
            'tabel_per_kegiatan' => $tabel_per_kegiatan,
            'tabel_per_belanja' => $tabel_per_belanja,
        ];
    }

    public function laporanKepegawaian($parameter)
    {
        $tahun = $parameter->tahun;
        $bulan = $parameter->bulan;


        $date = Carbon::createFromFormat('Y-m-d', $tahun . '-' . $bulan . '-' . '28');
        $dateQuery = $date->format('Y-m-d 23:59:59');

        $pegawai = Employe::all();
        $mutasi = MutasiPegawai::whereMonth('created_at',  $dateQuery)->get();
        $pengembangan = Pengembangan::whereMonth('created_at',  $dateQuery)->get();
        $kgb = KenaikanGajiBerkala::whereMonth('created_at',  $dateQuery)->get();
        $kepangkatan = KenaikanPangkat::whereMonth('created_at',  $dateQuery)->get();
        $pensiun = Pensiun::whereMonth('created_at',  $dateQuery)->get();
        $pangkat = Pangkat::all();
        $jabatan = Jabatan::all();

        foreach ($pangkat as $key => $value) {
            $count = Employe::where('pangkat_id', $value->id)->whereDate('created_at', '<=', $dateQuery)->get()->count();

            $value->jumlah = $count;
        }

        foreach ($jabatan as $key => $value) {
            $count = Employe::where('jabatan_id', $value->id)->whereDate('created_at', '<=', $dateQuery)->get()->count();
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
