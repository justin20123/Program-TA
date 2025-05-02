<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\NotaController;
use App\Http\Controllers\PemesananController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\VendorController;
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
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/', function () {
    if (Auth::user()) {
        if (Auth::user()->role != 'pemesan') {
            Auth::logout();
            return redirect()->route('login')->with('error', 'User  ini tidak tercatat sebagai pemesan, silahkan login menggunakan aplikasi yang sesuai.');
        }

        return redirect()->route('home');
    }
    else{
        return redirect()->route('home');
    }
    
});
//register

Route::get('register', [RegisterController::class, 'bukaregister'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('/home', function () {
    return view('home');
})->name('home');
Route::post('/location', [VendorController::class, 'loadVendorsTerdekat']);
Route::get('/vendor/rating/{idvendor}', [VendorController::class, 'getRating']);
Route::get('/vendor/harga/{idlayanan}', [VendorController::class, 'getHarga']);
Route::get('/isvendorada', [VendorController::class, 'isvendorada']);

Route::post('/untukanda', [VendorController::class, 'loadUntukAnda']);
Route::post('/layananterdekat', [VendorController::class, 'loadLayananTerdekat']);
Route::post('/getLayanan', [VendorController::class, 'loadLayanans']);
Route::get('/vendors/{filename}', function ($filename) {
    // Construct the path to the vendors directory
    $path = base_path('../vendors/' . $filename); // Adjust this if necessary

    // Check if the file exists
    if (file_exists($path)) {
        return response()->file($path); // Serve the file if it exists
    }

    // Return a 404 error if the file does not exist
    abort(404);
});

 //fotolayanan
 Route::get('/jenisbahan/{filename}', function ($filename) {
    $path = base_path('../jenisbahan/' . $filename);

    if (file_exists($path)) {
        return response()->file($path);
    }

    abort(404);
});
 Route::get('/imagelayanan/{filename}', function ($filename) {
    $path = base_path('../imagelayanan/' . $filename);

    if (file_exists($path)) {
        return response()->file($path);
    }

    abort(404);
});

Route::get('/pemesanan/{filename}', function ($filename) {
    $path = base_path('../pemesanan/' . $filename);

    if (file_exists($path)) {
        //ambil lokasi file pemesanan
        return response()->file($path);
    }

    abort(404); 
});

//semua vendor
Route::get('/vendor/layanan/{idlayanan}', [VendorController::class, 'index'])->name('vendors');

//vendor semua layanan
Route::get('/vendor/{idvendor}', [LayananController::class, 'index']);;

//detail layanan
Route::get('/vendor/{idvendor}/layanan/{idlayanan}', [LayananController::class, 'getDetailLayanan']);;
Route::get('/loadlayanan/{idvendor}/{idlayanan}/{idjenisbahan}', [LayananController::class, 'detail_layanan_load']);
Route::middleware(['auth'])->group(function () {
    Route::get('/masukdana', [PenggunaController::class, 'openmasukdana']);
    Route::post('/domasukdana', [PenggunaController::class, 'masukdana'])->name('masukdana');

    Route::delete("/deletepesanan", [PemesananController::class, 'deletepesanan']);
    //proses pemesanan
    Route::post('/submitpesanan', [PemesananController::class, 'submitpesanan']);
    Route::post('/uploadfilepesanan', [PemesananController::class, 'uploadfile']);

    //cart
    Route::get('/cart', [VendorController::class, 'indexCart'])->name('indexCart');;

    //orders
    Route::get('/cart/orders/{idvendor}', [PemesananController::class, 'indexOrder'])->name('indexOrder');
    Route::get('/lihatcatatan/{idpemesanan}', [PemesananController::class, 'lihatcatatan']);
    
    //edit pesanan
    Route::get('/editpesanan/{idpemesanan}', [PemesananController::class, 'openeditpesanan']);
    Route::get('/loadeditpesanan/{idpemesanan}', [PemesananController::class, 'load_editpesanan']);
    Route::post('/updatepesanantanpafile', [PemesananController::class, 'updatepesanantanpafile']);
    Route::post('/updatepesanandenganfile', [PemesananController::class, 'updatepesanandenganfile']);

    //checkout
    Route::post('/checkout', [PemesananController::class, 'bukacheckout'])->name('bukacheckout');
    Route::post('/getjarak', [VendorController::class, 'getSingleLocationRequest']);
    Route::post('/placeorder', [NotaController::class, 'placeorder']);

    //pesanan
    Route::get('/pesanan', [NotaController::class, 'indexPesanan']);
    Route::post('/doreview', [RatingController::class, 'doreview'])->name('doreview');

    //info pesanan
    Route::get('/pesanan/{idnota}', [NotaController::class, 'showDetailPesanan'])->name('detailPesanan');

    //verifikasi
    Route::get('/verifikasi/{idpemesanan}/{idnota}/{urutanprogress}', [NotaController::class, 'bukaverifikasi']);
    Route::get('/verifikasi_file/{any}', function ($any) {
        $path = base_path('../verifikasi_file/' . $any);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404);
    })->where('any', '.*');

    Route::post('/verifikasipesanan', [NotaController::class, 'verifikasipesanan']);
    Route::post('/ajukanperubahan', [NotaController::class, 'ajukanperubahan']);
});
