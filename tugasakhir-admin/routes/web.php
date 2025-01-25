<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DetailCetaksController;
use App\Http\Controllers\HargaCetakController;
use App\Http\Controllers\JenisBahanController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\OpsiDetailController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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



//login
Route::get('login', [LoginController::class, 'bukalogin'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
//buka app


Route::middleware(['auth'])->group(function () {

    Route::get('/', [VendorController::class, 'index'])->name('home');
    //tinjau
    Route::get('/tinjau/{id}', [NotaController::class, 'tinjau'])->name('tinjau');

    //blokir vendor
    Route::put('blokir', [VendorController::class, 'blokir'])->name('blokir');

    //aktifkan vendor
    Route::put('aktifkan', [VendorController::class, 'aktifkan'])->name('aktifkan');

    //verifikasi
    Route::get('/verifikasi', [VendorController::class, 'bukaverifikasi'])->name('bukaverifikasi');

    //verifikasi
    Route::get('/lepasblokir', [VendorController::class, 'bukalepasblokir'])->name('bukalepasblokir');

    Route::get('/vendors/{filename}', function ($filename) {
        $path = base_path('../vendors/' . $filename);
    
        if (file_exists($path)) {
            //ambil lokasi file pemesanan
            return response()->file($path);
        }
    
        abort(404);
    });
});
