<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardKepegawaianController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\EselonController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KenaikanGajiBerkalaController;
use App\Http\Controllers\KenaikanPangkatController;
use App\Http\Controllers\MutasiPegawaiController;
use App\Http\Controllers\PangkatController;
use App\Http\Controllers\PengembanganController;
use App\Http\Controllers\PensiunController;
use App\Http\Controllers\ProgramUnggulanController;
use App\Http\Controllers\UnitController;
use Illuminate\Http\Request;
use App\Models\Jabatan;
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

Route::resource('employee', EmployeController::class);
Route::resource('jabatan', JabatanController::class);
Route::resource('pangkat', PangkatController::class);
Route::resource('unit', UnitController::class);
Route::resource('eselon', EselonController::class);

Route::resource('pensiun', PensiunController::class);
Route::resource('pengembangan', PengembanganController::class);
Route::resource('mutasi', MutasiPegawaiController::class);
Route::resource('kepangkatan', KenaikanPangkatController::class);
Route::resource('kgb', KenaikanGajiBerkalaController::class);
Route::resource('dashboard-kepegawaian', DashboardKepegawaianController::class);

Route::resource('kegiatan', KegiatanController::class);
Route::resource('program-unggulan', ProgramUnggulanController::class);




Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::get('logout', 'logout');
});
