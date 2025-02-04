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
            $layanan_controller = new LayananController();
            return $layanan_controller->index();
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

        $vendor_pemesanan = [];

        foreach ($vendors as $v) {
            foreach ($pemesanans as $p) {
                if ($p->vendors_id == $v->id) {
                    array_push($vendor_pemesanan, $v);
                    break;
                }
            }
        }

        foreach ($vendor_pemesanan as $key => $vendor) {
            $ratings = DB::table('notas')
            ->join('pemesanans','pemesanans.notas_id','=','notas.id')
            ->join('ratings','ratings.notas_id','=','notas.id')
            ->where('pemesanans.vendors_id','=', $vendor->id)
            ->whereNotNull('ratings.nilai')
            ->select('notas.id',
                DB::raw('avg(ratings.nilai) as average_rating'),
            )
            ->groupBy('notas.id')
            ->get();
            if($ratings->isNotEmpty()){
                $total_rating = 0;
                $total_nota = DB::table('notas')
                ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                ->where('pemesanans.vendors_id', '=', $vendor->id)
                ->distinct('notas.id') // Ensure distinct count
                ->count('notas.id');

                foreach ($ratings as $r) {
                    $total_rating += $r->average_rating;
                }        
                $vendor_rating = $total_rating / $total_nota;
                $vendor->jumlah_pesanan = $total_nota;
                $vendor->rating = $vendor_rating;            
            }
            else{
                $vendor->jumlah_pesanan = 0;
                $vendor->rating = 0; 
            }
        }

        return view('pesanan.home', compact('vendor_pemesanan'));
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

        $id_vendor = DB::table('vendors')->insertGetId([
            'nama' => $request->nama,
            'status' => 'menunggu verifikasi',
            'foto_lokasi' => '',
            'longitude' => $request->longitude,
            'latitude' => $request->latitude,
        ]);

        $file = $request->file('fotopercetakan');
        $file_extension = $file->getClientOriginalExtension();
        $file_name = "$id_vendor.$file_extension";

        $directory = base_path('../vendors');

        $file->move($directory, $file_name);

        $relative_path = 'vendors/' . $file_name;

        $vendor = Vendor::findOrFail($id_vendor);
        $vendor->foto_lokasi = $relative_path;
        $vendor->save();

        $pengguna = Pengguna::findOrFail(Auth::user()->id);
        $pengguna->vendors_id = $id_vendor;
        $pengguna->save();

        return redirect()->route('opensetup', ['vendorid' => $id_vendor]);
    }

    public function opensetup($id_vendor)
    {
        $layanan_vendor = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('vendors_id', '=', $id_vendor)
            ->select('layanan_cetaks_id')
            ->get();

        $layanan_vendor_id = [];
        foreach ($layanan_vendor as $lv) {
            array_push($layanan_vendor_id, $lv->layanan_cetaks_id);
        }
        $layanans = DB::table('layanan_cetaks')
            ->get();
        $setup_layanans = [];
        foreach ($layanans as $l) {
            if (!in_array($l->id, $layanan_vendor_id)) {
                array_push($setup_layanans, $l);
            }
        }

        return view('vendor.setup', compact('setup_layanans', 'id_vendor'));
    }

    public function edit($id)
    {
        $vendor = Vendor::findOrFail($id);
        return view('vendor.edit', compact('vendor'));
    }

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

        $id_vendor = $request->input('vendorid');

        $vendor = Vendor::findOrFail($id_vendor);
        $vendor->nama = $request->input('nama');
        if ($request->file('fotopercetakan')) {
            $file = $request->file('fotopercetakan');
            $file_extension = $file->getClientOriginalExtension();
            $file_name = "$id_vendor.$file_extension";

            $directory = base_path('../vendors');
            $file->move($directory, $file_name);
            Cache::flush(); 
            Artisan::call('view:clear');
        }
        $vendor->longitude = $request->input('longitude');
        $vendor->latitude = $request->input('latitude');
        $vendor->save();

        return redirect()->route('home');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index');
    }
}
