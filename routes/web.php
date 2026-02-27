<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NasabahController;
use App\Http\Controllers\PinjamanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('auth.auth-login');
});

Route::resource('nasabah', NasabahController::class);

Route::post('nasabah/search', [NasabahController::class,'search']);

Auth::routes();

Route::get('dashboard', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');

Route::post('nasabah/transaksi', [NasabahController::class,'transaksi']);

Route::get('laporan/lappdf',[App\Http\Controllers\LaporanController::class,'lapPdf']);

Route::get('laporan/lapxls',[App\Http\Controllers\LaporanController::class,'lapXls']);
Route::get('rekap-kas', [App\Http\Controllers\RekapKasController::class, 'index'])->name('rekap-kas');
Route::get('penyesuaian-kas', [App\Http\Controllers\PenyesuaianKasController::class, 'index'])->name('penyesuaian-kas.index');
Route::post('penyesuaian-kas', [App\Http\Controllers\PenyesuaianKasController::class, 'store'])->name('penyesuaian-kas.store');

// Pinjaman routes
Route::get('pinjaman/recheck-active', [PinjamanController::class,'recheck_active_loans'])->name('pinjaman.recheck');
Route::get('pinjaman/relaksasi/{id}', [PinjamanController::class, 'relaksasi'])->name('pinjaman.relaksasi');
Route::post('pinjaman/relaksasi', [PinjamanController::class, 'relaksasi_update'])->name('pinjaman.relaksasi.update');
Route::post('pinjaman/{id}/lunas', [PinjamanController::class, 'mark_lunas'])->name('pinjaman.lunas');
Route::post('pinjaman/{id}/proses-angsuran', [PinjamanController::class, 'proses_angsuran'])->name('pinjaman.proses-angsuran');
Route::post('pinjaman/search', [PinjamanController::class,'search']);
Route::resource('pinjaman', PinjamanController::class);

Route::get('shu', [App\Http\Controllers\ShuController::class, 'index']);

Route::post('shu/proc', [App\Http\Controllers\ShuController::class,'proc']);

Route::post('pinjaman/get_name', [App\Http\Controllers\PinjamanController::class, 'get_name']);

Route::get('shu/ttp_buku',[App\Http\Controllers\ShuController::class,'ttp_buku']);

Route::post('shu/ttp',[App\Http\Controllers\ShuController::class,'ttp']);

Route::get('operator',[App\Http\Controllers\HomeController::class,'operator'])->name('operator');

//Route::delete('delete_user/{home}',[App\Http\Controllers\HomeController::class,'destroy']);

//Route::post('adduser', [App\Http\Controllers\HomeController::class,'create']);

Route::post('laporan/transNas',[App\Http\Controllers\LaporanController::class,'transNas']);

Route::post('laporan/pinjNas',[App\Http\Controllers\LaporanController::class,'pinjNas']);

Route::get('profile', [App\Http\Controllers\ProfileController::class, 'index'])->name('profile');

Route::get('profile/{profile}', [App\Http\Controllers\ProfileController::class, 'show']);

Route::get('/db-test', function () {
    try {
        \DB::connection()->getPdo();
        return "Koneksi DB OK: " . \DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/cekdb', function () {
    return [
        'db' => env('DB_DATABASE'),
        'user' => env('DB_USERNAME'),
        'pass' => env('DB_PASSWORD'),
    ];
});

use Illuminate\Support\Facades\DB;

Route::get('/dbtestraw', function () {
    try {
        $pdo = DB::connection()->getPdo();
        return "Connected as: " . $pdo->query("SELECT USER()")->fetchColumn();
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});


