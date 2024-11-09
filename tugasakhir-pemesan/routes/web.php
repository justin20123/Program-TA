<?php

use App\Http\Controllers\LayananController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\VendorController;
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

//home
Route::get('/', [VendorController::class, 'index'])->name('home');
Route::post('/location', [VendorController::class, 'loadVendorsTerdekat']);
Route::get('/vendor/rating/{idvendor}', [VendorController::class, 'getRating']);
Route::get('/vendor/harga/{idlayanan}', [VendorController::class, 'getHarga']);
Route::post('/untukanda', [VendorController::class, 'loadUntukAnda']);
Route::post('/layananterdekat', [VendorController::class, 'loadLayananTerdekat']);
Route::post('/getLayanan', [VendorController::class, 'loadLayanans']);

//vendor semua layanan
Route::get('/vendor/{idvendor}', [LayananController::class,'index']);

//detail layanan
Route::get('/vendor/{idvendor}/layanan/{idlayanan}', [VendorController::class,'getDetailLayanan']);
