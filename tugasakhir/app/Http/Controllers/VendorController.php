<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendors = DB::table('vendors')
            ->join('vendors_has_penggunas', 'vendors_has_penggunas.vendors_id', '=', 'vendors.id')
            ->where('vendors_has_penggunas.penggunas_id', '=', Auth::user()->id)
            ->get();

        if (count($vendors)>0) {
            foreach ($vendors as $key => $v) {

                $ratings = DB::table('notas')
                    ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                    ->join('ratings', 'ratings.notas_id', '=', 'notas.id')
                    ->where('pemesanans.vendors_id', '=', $v->id)
                    ->whereNotNull('ratings.nilai')
                    ->select(
                        'notas.id',
                        DB::raw('avg(ratings.nilai) as average_rating'),
                    )
                    ->groupBy('notas.id')
                    ->get();
                if ($ratings->isNotEmpty()) {
                    $totalRating = 0;
                    $totalNota = DB::table('notas')
                        ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
                        ->where('pemesanans.vendors_id', $v->id)
                        ->count();

                    foreach ($ratings as $r) {
                        $totalRating += $r->average_rating;
                    }
                    $vendor_rating = $totalRating / $totalNota;
                    $v->vendor_rating = $vendor_rating;
                    $v->total_nota = $totalNota;
                } else {
                    $v->vendor_rating = null;
                    $v->total_nota = 0;
                }
            }
        } else {
            return view('tambahvendor.tambah');
        }


        return view('home', compact('vendors'));
    }
    public function indexOrders()
    {
        $vendors = DB::table('vendors')
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

        // dd($vendorpemesanan);

        $pesanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->leftJoin('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id')
            ->leftJoin('pemesanans', 'jenis_bahan_cetaks.id', '=', 'pemesanans.jenis_bahan_cetaks_id')
            ->whereNotNull('pemesanans.notas_id')
            ->select('vendors_has_jenis_bahan_cetaks.vendors_id as idvendor', 'pemesanans.notas_id as idnota')
            ->groupBy('vendors_has_jenis_bahan_cetaks.vendors_id', 'pemesanans.notas_id')
            ->orderBy('vendors_has_jenis_bahan_cetaks.vendors_id')
            ->get();

        // dd($pesanan);

        $pesanan_count = [];
        $vendor_id = null;
        $i = 0;
        $count = 0;
        foreach ($pesanan as $p) {
            if (!$vendor_id) {
                $vendor_id = $p->idvendor;
            }
            if ($p->idvendor == $vendor_id) {
                $count++;
                $pesanan_count[$i] = [
                    'idvdendor' => $vendor_id,
                    'count' => $count,
                ];
            } else {
                $i++;
                $count = 1;
                $vendor_id = $p->idvendor;
                $pesanan_count[$i] = [
                    'idvdendor' => $vendor_id,
                    'count' => $count,
                ];
            }
        }
        $idvendor = null;

        foreach ($vendorpemesanan as $key => $v) {
            $v->jumlah_pesanan =  $pesanan_count[$key]['count'];
        }

        // dd($vendorpemesanan);

        return view('pesanan.home', compact('vendorpemesanan'));
    }

    public function indexPegawai()
    {
        $vendors = DB::table('vendors')
            ->select('*')
            ->get();

        // dd($vendors);

        $jumlah_pegawai = DB::table('vendors_has_penggunas')
            ->leftJoin('penggunas', 'penggunas.email', '=', 'vendors_has_penggunas.penggunas_email')
            ->select(DB::raw('count(vendors_has_penggunas.penggunas_email) as jumlah_pegawai'))
            ->where('penggunas.role', '=', 'pegawai')
            ->groupBy('vendors_has_penggunas.vendors_id')
            ->get();

        foreach ($vendors as $key => $v) {
            $v->jumlah_pegawai = $jumlah_pegawai[$key]->jumlah_pegawai;
        }

        return view('pegawai.home', compact('vendors'));
    }

    public function indexPengantar()
    {
        $vendors = DB::table('vendors')
            ->select('*')
            ->get();

        // dd($vendors);

        $jumlah_pengantar = DB::table('vendors_has_penggunas')
            ->leftJoin('penggunas', 'penggunas.email', '=', 'vendors_has_penggunas.penggunas_email')
            ->select(DB::raw('count(vendors_has_penggunas.penggunas_email) as jumlah_pengantar'))
            ->where('penggunas.role', '=', 'pengantar')
            ->groupBy('vendors_has_penggunas.vendors_id')
            ->get();

        // dd($jumlah_pengantar);

        foreach ($vendors as $key => $v) {
            $v->jumlah_pengantar = $jumlah_pengantar[$key]->jumlah_pengantar;
        }

        return view('pengantar.home', compact('vendors'));
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
            'status' => 'active',
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

        DB::table('vendors_has_penggunas')->insert([
            'vendors_id' => $idvendor,
            'penggunas_email' => Auth::user()->email,
            'penggunas_id' => Auth::user()->id
        ]);

        return redirect()->route('opensetup', ['vendorid' => $idvendor]); 
        ;
    }

    public function opensetup($idvendor){
        $layananvendor = DB::table('vendors_has_jenis_bahan_cetaks')
        ->where('vendors_id', '=', $idvendor)
        ->select('layanan_cetaks_id')
        ->get();
        $layananvendorid = [];
        foreach($layananvendor as $lv){
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

        return view('tambahvendor.setup', compact('setup_layanans','idvendor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_vendor' => 'required',
            'alamat_vendor' => 'required',
            'kontak_vendor' => 'required',
        ]);

        Vendor::create($request->all());

        return redirect()->route('vendors.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Vendor $vendor)
    {
        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'nama_vendor' => 'required',
            'alamat_vendor' => 'required',
            'kontak_vendor' => 'required',
        ]);

        $vendor->update($request->all());

        return redirect()->route('vendors.index');
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
