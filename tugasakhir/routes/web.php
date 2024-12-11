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
Route::get('register', [RegisterController::class, 'bukaregister'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

//buka app
Route::get('/', function () {
    $user = Auth::user();
    if ($user && ($user->hasRole('manajer') || $user->hasRole('pegawai'))) {
        return redirect()->route('home');
    } else if ($user && ($user->hasRole('pemesan') || $user->hasRole('pengantar'))) {
        Auth::logout();
        return redirect()->route('login')->with('error', 'User  ini tidak tercatat sebagai pemesan, silahkan login menggunakan aplikasi yang sesuai.');
    } else if (!$user) {
        return redirect()->route('login');
    }
});
Route::middleware(['web'])->group(function () {
    //tambah vendor
    Route::post('/tambahvendor', [VendorController::class, 'tambahvendor'])->name('tambahvendor');
    Route::get('/setup/{vendorid}', [VendorController::class, 'opensetup'])->name('opensetup');
    Route::post('/dosetup', [LayananController::class, 'dosetup']);


    //home
    Route::get('/home', [VendorController::class, 'index'])->name('home');
    Route::get('/layanans/{vendor_id}', [LayananController::class, 'index'])->name('layananindex');
    Route::get('/layanans/{vendor_id}/details/{idlayanan}', [LayananController::class, 'detail_layanan'])->name('layanan.detail_layanan');
    //opsi detail
    Route::get('/layanans/{vendor_id}/details/{idlayanan}/{idjenisbahan}', [LayananController::class, 'detail_layanan_load'])->name('layanan.detail_layanan_load');
    Route::get('/layanans/{idvendor}/details/{idlayanan}/edit/{idjenisbahan}/{iddetail}/{idopsi}', [LayananController::class, 'edit_opsi'])->name('layanan.edit_opsi');
    Route::get('/opsidetail/{idvendor}/{idlayanan}/{idopsi}', [OpsiDetailController::class, 'edit'])->name('opsidetail.edit');
    Route::get('/deleteopsidetail/{idvendor}/{idlayanan}/{idopsi}', [OpsiDetailController::class, 'destroy'])->name('opsidetail.destroy');
    Route::get('/addopsidetail/{idvendor?}/{idlayanan?}/{idopsi?}', [OpsiDetailController::class, 'create'])->name('opsidetail.create');
    Route::put('/opsidetail/{idopsi}', [OpsiDetailController::class, 'update'])->name('opsidetail.update');
    Route::post('/addopsidetail', [OpsiDetailController::class, 'store'])->name('opsidetail.store');

    //detail
    Route::put('/detail/{iddetail}', [DetailCetaksController::class, 'update'])->name('detail.update');
    Route::get('/createdetail/{idvendor}/{idlayanan}/{idjenisbahan}', [DetailCetaksController::class, 'create'])->name('detail.create');
    Route::put('/createdetail', [DetailCetaksController::class, 'store'])->name('detail.store');
    Route::delete('/deletedetail', [DetailCetaksController::class, 'destroy'])->name('detail.destroy');
    Route::post('/layanancetak/detail/{idjenisbahan}/create', [DetailCetaksController::class, 'create'])->name('detail.create');

    //Jenis bahan
    Route::get('/vendors/{idvendor}/layanan/{idlayanan}/edit/{idjenisbahan}', [JenisBahanController::class, 'edit'])->name('layanan.edit');
    Route::put('/jenisbahan/{idopsi}', [JenisBahanController::class, 'update'])->name('jenisbahan.update');

    //harga
    Route::get('/layanancetak/editharga/{idjenisbahan}', [HargaCetakController::class, 'index'])->name('harga.index');
    Route::get('opsiharga/create/{idjenisbahan}', [HargaCetakController::class, 'create'])->name('harga.create');
    Route::post('opsiharga/store', [HargaCetakController::class, 'store'])->name('harga.store');
    Route::get('opsiharga/edit/{idharga}', [HargaCetakController::class, 'edit'])->name('harga.edit');
    Route::post('opsiharga/update/{idharga}', [HargaCetakController::class, 'update'])->name('harga.update');
    Route::delete('opsiharga/destroy', [HargaCetakController::class, 'destroy'])->name('harga.destroy');

    //pesanan
    Route::get('/pesanancetak', [VendorController::class, 'indexOrders'])->name('pesanan');
    Route::get('/pesanancetak/{id}', [PemesananController::class, 'index'])->name('pesanan.index');
    Route::get('/terimapesanan/{id}', [NotaController::class, 'terimaPesanan']);
    Route::get('/pesanancetak2/{id}', [PemesananController::class, 'show'])->name('pesanancetak.show');

    Route::post('/kirimcontoh', [NotaController::class, 'kirimcontoh']);
    Route::post('/lihatperubahan', [NotaController::class, 'lihatperubahan']);

    //detailpesanan
    Route::get('/pesanancetak/{idvendor}/detail/{idnota}', [PemesananController::class, 'detailPesanan'])->name('pesanan.detailPesanan');

    //download
    Route::get('downloadfile/{url_file}', function ($url_file) {
        $filePath = base_path('../pemesanans/' . $url_file);
        
        if (file_exists($filePath)) {
            return response()->download($filePath);
        }
        return abort(404);
    })->name('downloadfile');

    //pilih pengantar
    Route::post('/pilihpengantar', [PemesananController::class, 'pilihpengantar'])->name('pesanan.pilihpengantar');
    Route::post('/tugaskanpengantar', [PemesananController::class, 'tugaskanpengantar']);
    Route::get('/pengantar/{emailpengantar}/antar/{idnota}', [PemesananController::class, 'antarkan'])->name('pesanan.antarkan');

    //status ambil
    Route::post('/requestambil', [PemesananController::class, 'requestambil']);

    //status selesai
    Route::post('/selesaikanpesanan', [PemesananController::class, 'selesaikanpesanan']);


    //pegawai home
    Route::get('/pegawai', [VendorController::class, 'indexPegawai'])->name('pegawai.index');

    //list pegawai 
    Route::get('/pegawai/listpegawai/{idvendor}', [PenggunaController::class, 'indexPegawai'])->name('pegawai.index');

    //tambah pegawai
    Route::get('/addpegawai/{idvendor}', [PenggunaController::class, 'createPegawai'])->name('pegawai.create');
    Route::post('/addpegawai', [PenggunaController::class, 'storePegawai'])->name('pegawai.store');

    //edit pegawai
    Route::get('/editpegawai/{idpegawai}', [PenggunaController::class, 'editPegawai'])->name('pegawai.edit');
    Route::put('/editpegawai/{idpegawai}', [PenggunaController::class, 'updatePegawai'])->name('pegawai.update');

    //hapus pegawai
    Route::delete('/deletepegawai/{id}', [PenggunaController::class, 'deletePegawai'])->name('pegawai.delete');

    //home pengantar
    Route::get('/pengantar', [VendorController::class, 'indexPengantar'])->name('pengantar.index');

    //list pengantar
    Route::get('/pengantar/listpengantar/{idvendor}', [PenggunaController::class, 'indexPengantar'])->name('pengantar.index');

    //tambah pengantar
    Route::get('/addpengantar/{idvendor}', [PenggunaController::class, 'createPengantar'])->name('pengantar.create');
    Route::post('/addpengantar', [PenggunaController::class, 'storePengantar'])->name('pengantar.store');

    //edit pengantar
    Route::get('/editpengantar/{idpengantar}', [PenggunaController::class, 'editPengantar'])->name('pengantar.edit');
    Route::put('/editpengantar/{idpengantar}', [PenggunaController::class, 'updatePengantar'])->name('pengantar.update');

    //hapus pengantar
    Route::delete('/deletepengantar/{id}', [PenggunaController::class, 'deletePengantar'])->name('pengantar.delete');


    Route::get('/layanancetak', function () {
        return view('layanancetak');
    });

    Route::get('/layanancetak/fotokopi', function () {
        return view('layanan.detail');
    });
    Route::get('/layanancetak/fotokopi/createdetail', function () {
        return view('layanan.createdetail');
    });
    Route::get('/layanancetak/fotokopi/editdetail', function () {
        return view('layanan.editdetail');
    });
    Route::get('/layanancetak/fotokopi/editdetail/edit/option', function () {
        return view('layanan.editoption');
    });
});
