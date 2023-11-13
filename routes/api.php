<?php

use App\Http\Controllers\ArsipController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CapaianIndikatorKegiatanUtamaController;
use App\Http\Controllers\CapaianIndikatorKinerjaKegiatanController;
use App\Http\Controllers\CapaianProgramUnggulanController;
use App\Http\Controllers\DashboardKepegawaianController;
use App\Http\Controllers\DashboardKeuanganController;
use App\Http\Controllers\DashboardWilayahController;
use App\Http\Controllers\DataPengawasanController;
use App\Http\Controllers\DipaController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\EselonController;
use App\Http\Controllers\GroupUnitController;
use App\Http\Controllers\IkpaController;
use App\Http\Controllers\IndikatorKinerjaKegiatanController;
use App\Http\Controllers\IndikatorKinerjaUtamaController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\JenisPengawasanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KenaikanGajiBerkalaController;
use App\Http\Controllers\KenaikanPangkatController;
use App\Http\Controllers\KinerjaKeuanganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MonitoringPengaduanController;
use App\Http\Controllers\MonitoringPengawasanItwilController;
use App\Http\Controllers\MonitoringTemuanBpkController;
use App\Http\Controllers\MonitoringTemuanBpkpController;
use App\Http\Controllers\MonitoringTemuanOriController;
use App\Http\Controllers\MutasiPegawaiController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PengelolaanMediaController;
use App\Http\Controllers\PengembanganController;
use App\Http\Controllers\PensiunController;
use App\Http\Controllers\PersuratanController;
use App\Http\Controllers\ProgramUnggulanController;
use App\Http\Controllers\RealiasaiAnggaranController;
use App\Http\Controllers\SatuanKerjaController;
use App\Http\Controllers\UnitController;
use App\Models\MonitoringPengaduan;
use App\Models\MonitoringTemuanBpk;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
});

Route::resource('unit', UnitController::class);
Route::resource('unit-group', GroupUnitController::class);
Route::resource('satuan-kerja', SatuanKerjaController::class);
Route::resource('jenis-pengawasan', JenisPengawasanController::class);

// KEPEGAWAIAN
Route::resource('employee', EmployeController::class);
Route::resource('jabatan', JabatanController::class);
Route::resource('pangkat', PangkatController::class);
Route::resource('eselon', EselonController::class);
Route::resource('pensiun', PensiunController::class);
Route::resource('pengembangan', PengembanganController::class);
Route::resource('mutasi', MutasiPegawaiController::class);
Route::resource('kepangkatan', KenaikanPangkatController::class);
Route::resource('kgb', KenaikanGajiBerkalaController::class);
Route::resource('dashboard-kepegawaian', DashboardKepegawaianController::class);

// PROGRAM dan PELAPORAN // Admin
Route::resource('indikator-kinerja-utama', IndikatorKinerjaUtamaController::class);
Route::resource('indikator-kinerja-kegiatan', IndikatorKinerjaKegiatanController::class);
Route::resource('program-unggulan', ProgramUnggulanController::class);

Route::resource('laporan', LaporanController::class);
Route::get(
    'laporan-generate',
    [LaporanController::class, 'generate']
);
Route::get(
    'laporan-debug',
    [LaporanController::class, 'debug']
);
Route::get(
    'laporan-download/{id}',
    [LaporanController::class, 'download']
);


// UMUM
Route::resource('pengelolaan-persuratan', PersuratanController::class);
Route::resource('pengelolaan-arsip', ArsipController::class);

// SIP
Route::resource('monitoring-temuan-internal', MonitoringPengawasanItwilController::class);
Route::resource('monitoring-temuan-bpk', MonitoringTemuanBpkController::class);
Route::resource('monitoring-temuan-bpkp', MonitoringTemuanBpkpController::class);
Route::resource('monitoring-temuan-ori', MonitoringTemuanOriController::class);
Route::resource('monitoring-pengaduan', MonitoringPengaduanController::class);
Route::resource('pengelolaan-media', PengelolaanMediaController::class);

// KEUANGAN
Route::resource('dipa', DipaController::class);
Route::resource('realisasi-anggaran', RealiasaiAnggaranController::class);
Route::resource('kinerja-keuangan', KinerjaKeuanganController::class);
Route::resource('ikpa', IkpaController::class);

// WILAYAH
Route::resource('data-pengawasan', DataPengawasanController::class);
Route::resource('dashboard-wilayah', DashboardWilayahController::class);
Route::resource('dashboard-keuangan', DashboardKeuanganController::class);

// ALL 
Route::resource('kegiatan', KegiatanController::class);
Route::resource('capaian-program-unggulan', CapaianProgramUnggulanController::class);
Route::resource('capaian-iku', CapaianIndikatorKegiatanUtamaController::class);
Route::resource('capaian-ikk', CapaianIndikatorKinerjaKegiatanController::class);

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
});
