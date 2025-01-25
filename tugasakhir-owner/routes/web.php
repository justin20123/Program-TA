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

//register
Route::post('tambahappkey', [RegisterController::class, 'tambahappkey'])->name('tambahappkey');
Route::get('/register', [RegisterController::class, 'bukaregister'])->name('register');
Route::post('doregister', [RegisterController::class, 'register'])->name('doregister');


Route::middleware(['auth'])->group(function () {
    //pengguna
    Route::get('/', [PenggunaController::class, 'index'])->name('home');
    Route::get('/admin/tambah', [PenggunaController::class, 'create'])->name('admin.create');
    Route::post('/tambah', [PenggunaController::class,'store'])->name('admin.store');
    Route::get('/admin/{id}/edit', [PenggunaController::class, 'edit'])->name('admin.edit');
    Route::put('/update/{id}', [PenggunaController::class, 'update'])->name('admin.update');
    Route::delete('/delete', [PenggunaController::class, 'destroy'])->name('admin.destroy');
});
