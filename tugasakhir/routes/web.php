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
use App\Http\Controllers\RatingController;
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
Route::get('register', [RegisterController::class, 'bukaregister'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

//buka app
Route::get('/', [VendorController::class, 'index']);

Route::middleware(['auth'])->group(function () {
    //tambah vendor
    Route::post('/tambahvendor', [VendorController::class, 'tambahvendor'])->name('tambahvendor');
    Route::get('/setup/{vendorid}', [VendorController::class, 'opensetup'])->name('opensetup');
    Route::post('/dosetup', [LayananController::class, 'dosetup']);

    //edit vendor
    Route::get('/editvendor/{vendorid}', [VendorController::class, 'edit'])->name('vendor.edit');
    Route::post('/editvendor', [VendorController::class, 'update'])->name('vendor.update');
    Route::get('/vendor/{filename}', function ($filename) {
        $path = base_path('../vendors/' . $filename);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404);
    });


    //home
    Route::get('/home', [VendorController::class, 'index'])->name('home');
    Route::get('/layanans', [LayananController::class, 'index'])->name('layananindex');
    Route::get('/layanans/{vendor_id}/details/{idlayanan}', [LayananController::class, 'detail_layanan'])->name('layanan.detail_layanan');
    //opsi detail
    Route::get('/layanans/{vendor_id}/details/{idlayanan}/{idjenisbahan}', [LayananController::class, 'detail_layanan_load'])->name('layanan.detail_layanan_load');
    Route::get('/layanans/{idvendor}/details/{idlayanan}/edit/{idjenisbahan}/{iddetail}/{idopsi}', [LayananController::class, 'edit_opsi'])->name('layanan.edit_opsi');
    Route::get('/opsidetail/list/{idopsi}', [OpsiDetailController::class, 'index'])->name('opsidetail.index');
    Route::get('/opsidetail/edit/{idopsi}', [OpsiDetailController::class, 'edit'])->name('opsidetail.edit');
    Route::delete('/deleteopsidetail', [OpsiDetailController::class, 'destroy'])->name('opsidetail.destroy');
    Route::get('/addopsidetail/{iddetail?}', [OpsiDetailController::class, 'create'])->name('opsidetail.create');
    Route::put('/opsidetail/update', [OpsiDetailController::class, 'update'])->name('opsidetail.update');
    Route::post('/addopsidetail', [OpsiDetailController::class, 'store'])->name('opsidetail.store');

    //detail
    Route::put('/detail/{iddetail}', [DetailCetaksController::class, 'update'])->name('detail.update');
    Route::get('/createdetail/{idvendor}/{idlayanan}/{idjenisbahan}', [DetailCetaksController::class, 'create']);
    Route::put('/createdetail', [DetailCetaksController::class, 'store'])->name('detail.store');
    Route::delete('/deletedetail', [DetailCetaksController::class, 'destroy'])->name('detail.destroy');
    Route::post('/layanancetak/detail/{idjenisbahan}/create', [DetailCetaksController::class, 'create']);

    //Jenis bahan
    Route::get('/vendors/{idvendor}/layanan/{idlayanan}/edit/{idjenisbahan}', [JenisBahanController::class, 'edit'])->name('layanan.edit');
    Route::post('/jenisbahan/store', [JenisBahanController::class, 'store'])->name('jenisbahan.store');
    Route::put('/jenisbahan/update', [JenisBahanController::class, 'update'])->name('jenisbahan.update');
    Route::delete('/jenisbahan/delete', [JenisBahanController::class, 'destroy'])->name('jenisbahan.destroy');

    //harga
    Route::get('/layanancetak/editharga/{idjenisbahan}', [HargaCetakController::class, 'index'])->name('harga.index');
    Route::get('opsiharga/create/{idjenisbahan}', [HargaCetakController::class, 'create'])->name('harga.create');
    Route::post('opsiharga/store', [HargaCetakController::class, 'store'])->name('harga.store');
    Route::get('opsiharga/edit/{idharga}', [HargaCetakController::class, 'edit'])->name('harga.edit');
    Route::post('opsiharga/update/{idharga}', [HargaCetakController::class, 'update'])->name('harga.update');
    Route::delete('opsiharga/destroy', [HargaCetakController::class, 'destroy'])->name('harga.destroy');

    //pesanan
    Route::get('/pesanancetak', [PemesananController::class, 'index'])->name('pesanan.index');
    Route::get('/terimapesanan/{id}', [NotaController::class, 'terimaPesanan']);
    Route::get('/pesanancetak2/{id}', [PemesananController::class, 'show'])->name('pesanancetak.show');
    Route::get('/lihatcatatan/{idpemesanan}', [PemesananController::class, 'lihatcatatan']);
    Route::post('/kirimcontoh', [NotaController::class, 'kirimcontoh']);
    Route::post('/lihatperubahan', [NotaController::class, 'lihatperubahan']);

    //detailpesanan
    Route::get('/pesanancetak/{idvendor}/detail/{idnota}', [PemesananController::class, 'detailPesanan'])->name('pesanan.detailPesanan');

    //download
    Route::get('/pemesanan/{filename}', function ($filename) {
        $path = base_path('../pemesanan/' . $filename);

        if (file_exists($path)) {
            //ambil lokasi file pemesanan
            return response()->download($path);
        }

        abort(404);
    });

    //fotolayanan
    Route::get('/imagelayanan/{filename}', function ($filename) {
        $path = base_path('../imagelayanan/' . $filename);

        if (file_exists($path)) {
            return response()->file($path);
        }

        abort(404);
    });

    //pilih pengantar
    Route::post('/pilihpengantar', [PemesananController::class, 'pilihpengantar'])->name('pesanan.pilihpengantar');
    Route::post('/tugaskanpengantar', [PemesananController::class, 'tugaskanpengantar']);
    Route::get('/pengantar/{emailpengantar}/antar/{idnota}', [PemesananController::class, 'antarkan'])->name('pesanan.antarkan');

    //status ambil
    Route::post('/requestambil', [PemesananController::class, 'requestambil']);

    //status selesai
    Route::post('/selesaikanpesanan', [PemesananController::class, 'selesaikanpesanan']);


    //pegawai home
    Route::get('/pegawai', [VendorController::class, 'indexPegawai']);

    //list pegawai 
    Route::get('/pegawai/{idvendor}', [PenggunaController::class, 'indexPegawai'])->name('pegawai.index');;

    //tambah pegawai
    Route::get('/addpegawai/{idvendor}', [PenggunaController::class, 'createPegawai'])->name('pegawai.create');
    Route::post('/addpegawai', [PenggunaController::class, 'storePegawai'])->name('pegawai.store');

    //edit pegawai
    Route::get('/editpegawai/{idpegawai}', [PenggunaController::class, 'editPegawai'])->name('pegawai.edit');
    Route::put('/editpegawai/{idpegawai}', [PenggunaController::class, 'updatePegawai'])->name('pegawai.update');

    //hapus pegawai
    Route::delete('/deletepegawai/{id}', [PenggunaController::class, 'deletePegawai'])->name('pegawai.delete');

    //home pengantar
    Route::get('/pengantar', [VendorController::class, 'indexPengantar']);

    //list pengantar
    Route::get('/pengantar/{idvendor}', [PenggunaController::class, 'indexPengantar'])->name('pengantar.index');

    //tambah pengantar
    Route::get('/addpengantar/{idvendor}', [PenggunaController::class, 'createPengantar'])->name('pengantar.create');
    Route::post('/addpengantar', [PenggunaController::class, 'storePengantar'])->name('pengantar.store');

    //edit pengantar
    Route::get('/editpengantar/{idpengantar}', [PenggunaController::class, 'editPengantar'])->name('pengantar.edit');
    Route::put('/editpengantar/{idpengantar}', [PenggunaController::class, 'updatePengantar'])->name('pengantar.update');

    //hapus pengantar
    Route::delete('/deletepengantar/{id}', [PenggunaController::class, 'deletePengantar'])->name('pengantar.delete');

    //ulasan
    Route::get('/ulasan/{idvendor}/layanan/{idlayanan}', [RatingController::class, 'index'])->name('rating.index');
});
