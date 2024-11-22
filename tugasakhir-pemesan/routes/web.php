<?php

use App\Http\Controllers\LayananController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PemesananController;
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

//semua vendor (belum ada)
Route::get('/vendor', [VendorController::class,'indexVendors']);

//vendor semua layanan
Route::get('/vendor/{idvendor}', [LayananController::class,'index']);

//detail layanan
Route::get('/vendor/{idvendor}/layanan/{idlayanan}', [LayananController::class,'getDetailLayanan']);
Route::get('/loadlayanan/{idvendor}/{idlayanan}/{idjenisbahan}', [LayananController::class,'detail_layanan_load']);

//proses pemesanan
Route::post('/submitpesanan', [PemesananController::class, 'submitpesanan']);
Route::post('/uploadfilepesanan', [PemesananController::class, 'uploadfile']);

//cart
Route::get('/cart', [VendorController::class, 'indexCart']);

//orders
Route::get('/cart/orders/{idvendor}', [PemesananController::class, 'indexOrder']);
Route::get('/pemesanan/{filename}', function ($filename) {
    $path = base_path('../pemesanan/' . $filename);

    if (file_exists($path)) {
        //ambil lokasi file pemesanan
        return response()->file($path);
    }

    abort(404);
});

//checkout
Route::post('/checkout', [PemesananController::class, 'bukacheckout'])->name('bukacheckout');
Route::post('/getjarak', [VendorController::class, 'getSingleLocationRequest']);
Route::post('/placeorder', [NotaController::class, 'placeorder']);

//pesanan
Route::get('/pesanan', [NotaController::class, 'indexPesanan']);

//info pesanan
Route::get('/pesanan/{idnota}', [NotaController::class, 'showDetailPesanan']);