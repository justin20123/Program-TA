<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
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
        $vendors = Vendor::all();
        foreach($vendors as $key=>$v){

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
                ->where('pemesanans.vendors_id', $v->id)
                ->count();
        
                foreach ($ratings as $r) {
                    $totalRating += $r->average_rating;

                }        
                $vendor_rating = $totalRating / $totalNota;
                $v->vendor_rating = $vendor_rating;
                $v->total_nota = $totalNota;               
            }
            else{
                $v->vendor_rating = null;
                $v->total_nota = 0;
            }
            
        }
        
        return view('home', compact('vendors'));
    }
    public function indexOrders()
    {
        $vendors = DB::table('vendors')
        // ->join('vendors_has_layanan_cetaks','vendors.id','=','vendors_has_layanan_cetaks.vendors_id')
        // ->join('jenis_bahan_cetaks','jenis_bahan_cetaks.id','=','vendors_has_layanan_cetaks.jenis_bahan_cetaks_id')
        // ->join('pemesanans','jenis_bahan_cetaks.id','=','pemesanans.jenis_bahan_cetaks_id')
        ->select('*')
        ->get();
        
        // dd($vendors);

        $jumlah_pesanan = DB::table('vendors_has_jenis_bahan_cetaks')
        ->leftJoin('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id')
        ->leftJoin('pemesanans', 'jenis_bahan_cetaks.id', '=', 'pemesanans.jenis_bahan_cetaks_id')
        ->select('vendors_has_jenis_bahan_cetaks.vendors_id', DB::raw('count(pemesanans.id) as jumlahpesanan'))
        ->groupBy('vendors_has_jenis_bahan_cetaks.vendors_id')
        ->get();
        
        // dd($jumlah_pesanan);

        foreach ($vendors as $key=>$v) {
            $v->jumlah_pesanan = $jumlah_pesanan[$key]->jumlahpesanan; 
        }   

        return view('pesanan.home', compact('vendors'));
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
        ->where('penggunas.role','=','pegawai')
        ->groupBy('vendors_has_penggunas.vendors_id')
        ->get();

        foreach ($vendors as $key=>$v) {
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
        ->where('penggunas.role','=','pengantar')
        ->groupBy('vendors_has_penggunas.vendors_id')
        ->get();

        // dd($jumlah_pengantar);

        foreach ($vendors as $key=>$v) {
            $v->jumlah_pengantar = $jumlah_pengantar[$key]->jumlah_pengantar; 
        }   

        return view('pengantar.home', compact('vendors'));
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
