<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\View;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!Auth::user()) {
            return redirect()->route('login');
        }
        if (Auth::user()->role == "manajer" && Auth::user()->vendors_id) {
            $layananController = new LayananController();
            return $layananController->index();
        } elseif (Auth::user()->role != "manajer" && Auth::user()->role != "pegawai") {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Anda bukan manajer maupun pegawai, silahkan login menggunakan akun yang bersangkutan.');
        } else {
            return view('vendor.tambah');
        }


        return view('home', compact('vendors'));
    }
    public function indexOrders()
    {
        $vendors = DB::table('vendors')
            ->join('penggunas', 'penggunas.vendors_id', '=', 'vendors.id')
            ->where('penggunas.id', '=', Auth::user()->id)
            ->get();


        $pemesanans = DB::table('pemesanans')->get();

        $vendorpemesanan = [];

        foreach ($vendors as $v) {
            foreach ($pemesanans as $p) {
                if ($p->vendors_id == $v->id) {
                    array_push($vendorpemesanan, $v);
                    break;
                }
            }
        }

        

        foreach ($vendorpemesanan as $key => $v) {
            $ratings = DB::table('notas')
            ->join('pemesanans','pemesanans.notas_id','=','notas.id')
            
            ->join('ratings','ratings.notas_id','=','notas.id')
            ->where('pemesanans.vendors_id','=', $v->id)
            ->whereNotNull('ratings.nilai')
            ->select('notas.id',
                DB::raw('avg(ratings.nilai) as average_rating'),
            )
            ->groupBy('notas.id')
            ->get();
            if($ratings->isNotEmpty()){
                $totalRating = 0;
                $totalNota = DB::table('notas')
                ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                ->where('pemesanans.vendors_id', '=', $v->id)
                ->distinct('notas.id') // Ensure distinct count
                ->count('notas.id');
        
                foreach ($ratings as $r) {
                    $totalRating += $r->average_rating;

                }        
                $vendor_rating = $totalRating / $totalNota;
                $v->jumlah_pesanan = $totalNota;
                $v->rating = $vendor_rating;            
            }
            else{
                $v->jumlah_pesanan = 0;
                $v->rating = 0; 
            }
        }

        // dd($vendorpemesanan);

        return view('pesanan.home', compact('vendorpemesanan'));
    }

    public function indexPegawai()
    {


        $id = Auth::user()->vendors_id;
        return redirect()->to("/pegawai/$id");

    }

    public function indexPengantar()
    {
        $id = Auth::user()->vendors_id;
        return redirect()->to("/pengantar/$id");
    }


    public function tambahvendor(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required',
                'fotopercetakan' => 'required|file|mimes:jpeg,jpg,png,gif,|max:20480',
                'latitude' => 'required|between:-90,90',
                'longitude' => 'required|between:-180,180'
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal menambah vendor. ' . $e->getMessage());
        }

        $idvendor = DB::table('vendors')->insertGetId([
            'nama' => $request->nama,
            'status' => 'menunggu verifikasi',
            'foto_lokasi' => '',
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);

        $file = $request->file('fotopercetakan');
        $fileExtension = $file->getClientOriginalExtension();
        $fileName = "$idvendor.$fileExtension";

        $directory = base_path('../vendors');

        $file->move($directory, $fileName);

        $relativePath = 'vendors/' . $fileName;

        $vendor = Vendor::findOrFail($idvendor);
        $vendor->foto_lokasi = $relativePath;
        $vendor->save();

        $pengguna = Pengguna::findOrFail(Auth::user()->id);
        $pengguna->vendors_id = $idvendor;
        $pengguna->save();

        return redirect()->route('opensetup', ['vendorid' => $idvendor]);;
    }

    public function opensetup($idvendor)
    {
        $layananvendor = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('vendors_id', '=', $idvendor)
            ->select('layanan_cetaks_id')
            ->get();

            // dd($layananvendor);
        $layananvendorid = [];
        foreach ($layananvendor as $lv) {
            array_push($layananvendorid, $lv->layanan_cetaks_id);
        }
        $layanans = DB::table('layanan_cetaks')
            ->get();
        $setup_layanans = [];
        foreach ($layanans as $l) {
            if (!in_array($l->id, $layananvendorid)) {
                array_push($setup_layanans, $l);
            }
        }

        return view('vendor.setup', compact('setup_layanans', 'idvendor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('vendor.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max: 50',
                'fotopercetakan' => 'file|mimes:jpeg,jpg,png,gif,|max:20480',
                'longitude' => 'required',
                'latitude' => 'required',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal menambah vendor. ' . $e->getMessage());
        }

        $idvendor = $request->input('vendorid');

        $vendor = Vendor::findOrFail($idvendor);
        $vendor->nama = $request->input('nama');
        if ($request->file('fotopercetakan')) {
            $file = $request->file('fotopercetakan');
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = "$idvendor.$fileExtension";

            $directory = base_path('../vendors');
            $file->move($directory, $fileName);
            Cache::flush(); 
            Artisan::call('view:clear');
        }
        $vendor->longitude = $request->input('longitude');
        $vendor->latitude = $request->input('latitude');
        $vendor->save();

        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index');
    }
}
