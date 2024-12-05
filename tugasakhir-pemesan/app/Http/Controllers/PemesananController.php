<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    public function indexOrder($idvendors)
    {
        $pemesanans = DB::table('pemesanans')
        ->where('penggunas_email', '=', Auth::user()->email)
        ->where('pemesanans.notas_id','=',null)
        ->where('pemesanans.vendors_id','=',$idvendors)
        ->get();

        $subtotal = 0;
        foreach($pemesanans as $p){

            $subtotal_pemesanan = 0;
            $biaya_tambahan = 0;

            $tambahan_opsi = DB::table('pemesanans_has_opsi_details')
            ->join('opsi_details', 'opsi_details.id','=','pemesanans_has_opsi_details.opsi_details_id')
            ->where('pemesanans_has_opsi_details.pemesanans_id','=',$p->id)
            ->select('opsi_details.biaya_tambahan','opsi_details.opsi')
            ->get();

            $biayaOpsiDetails = [];

            

            $harga_cetaks = DB::table('harga_cetaks')
            ->where('id','=',$p->harga_cetaks_id)
            ->first();
            $p->harga_satuan = $harga_cetaks->harga_satuan;

            $subtotal_pemesanan += $p->harga_satuan * $p->jumlah;

            $biaya_tambahan = 0;
            foreach($tambahan_opsi as $t){
                $array = [
                    'biaya_tambahan' => $t->biaya_tambahan,
                    'opsi'=> $t->opsi
                ];
                array_push($biayaOpsiDetails,$array);
                $subtotal_pemesanan += $t->biaya_tambahan;
                $biaya_tambahan += $t->biaya_tambahan;
            }
            $p->biaya_tambahan = $biaya_tambahan;
            $p->opsi_detail = $biayaOpsiDetails;
            $p->subtotal = $subtotal_pemesanan;

            $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('layanan_cetaks','vendors_has_jenis_bahan_cetaks.layanan_cetaks_id','=', 'layanan_cetaks.id')
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $p->jenis_bahan_cetaks_id)
            ->select('layanan_cetaks.nama as namalayanan', 'layanan_cetaks.satuan as satuanlayanan')
            ->first();
            $p->layanan = $layanan->namalayanan;
            $p->satuan = $layanan->satuanlayanan;

            $subtotal += $subtotal_pemesanan;
        }
        // dd($pemesanans);
        

        return view('cart.pesanan', compact('pemesanans', 'subtotal'));
    }

    public function submitpesanan(Request $request){
        $request->validate([
            'jumlah' => 'required',
            'jenis_bahan_cetaks_id' => 'required',
            'idopsidetail' => 'required',
            'vendors_id' => 'required',
            'file' => 'required|file|mimes:pdf|max:20480' //max 20mb
        ]);

        $idopsidetail = explode(",", $request->idopsidetail);

        $hargacetakcontroller = new HargaCetakController();
        $idhargacetak = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id, true);
        $hargasatuan = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id);
        $harga = $hargasatuan * $request->input('jumlah');
        
        foreach ($idopsidetail as $id){
            $p = DB::table('opsi_details')
            ->where('id', '=', $id)
            ->first();
            $harga += $p->biaya_tambahan;
        }

        $perlu_verifikasi = 0;
        if($harga > 200000){
            $perlu_verifikasi  = 1;
        }
        
        $id = DB::table('pemesanans')->insertGetId([
            'penggunas_email' => Auth::user()->email, 
            'jumlah' => $request->input('jumlah'), 
            'url_file' => '', 
            'catatan' => $request->input('catatan'),
            'perlu_verifikasi' => $perlu_verifikasi,
            'harga_cetaks_id' => $idhargacetak,
            'jenis_bahan_cetaks_id' => $request->input('jenis_bahan_cetaks_id'), 
            'vendors_id' => $request->input('vendors_id'), 
            
            'created_at' => now(),
        ]);

        foreach($idopsidetail as $od){
            DB::table('pemesanans_has_opsi_details')->insert([
                'pemesanans_id'=>$id,
                'opsi_details_id'=>$od,
            ]);
        }

        $file = $request->file('file');
        $fileName = $id . '.pdf';

        $directory = base_path('../pemesanan');

        $file->move($directory, $fileName);

        $relativePath = 'pemesanan/' . $fileName;

        $pemesanan = Pemesanan::find($id);
        $pemesanan->url_file = $relativePath; 
        $pemesanan->save();

        return ["idpemesanan"=>$id, "idvendor"=>$request->input('vendors_id'), 'message'=>"Pesanan dimasukkan ke dalam cart"];


    }
    public function bukacheckout(Request $request){
        $request->validate([
            'idpemesanans' => 'required|array',
            'subtotal' => 'required|gt:0',
        ]);

        // dd($request);
        $idpemesanans = $request->input('idpemesanans');
        $biaya_tambahan = $request->input('biaya_tambahan');
        $pemesanans = [];
        $subtotal = $request->subtotal;
        foreach($idpemesanans as $key=>$id){
            $p = DB::table('pemesanans')
            ->where('id','=',$id) 
            ->first();

            $harga_cetaks = DB::table('harga_cetaks')
            ->where('id','=',$p->harga_cetaks_id)
            ->first();
            $p->harga_satuan = $harga_cetaks->harga_satuan;

            $p->biaya_tambahan = $biaya_tambahan[$key];

            

            $layanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->join('layanan_cetaks','vendors_has_jenis_bahan_cetaks.layanan_cetaks_id','=', 'layanan_cetaks.id')
            ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $p->jenis_bahan_cetaks_id)
            ->select('layanan_cetaks.nama as namalayanan', 'layanan_cetaks.satuan as satuanlayanan')
            ->first();
            $p->layanan = $layanan->namalayanan;
            $p->satuan = $layanan->satuanlayanan;
            array_push($pemesanans, $p);
        }

        // dd($pemesanans);
        // return ['subtotal'=>$subtotal, 'pemesanans'=>$pemesanans];
        return view('cart.checkout', compact('pemesanans','subtotal'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $notas = Nota::all();
        return view('pemesanans.create', compact('notas'));
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
            'penggunas_email' => 'required',
            'jumlah' => 'required',
            'url_file' => 'required',
            'harga_cetaks_id' => 'required',
            'harga_cetaks_id_jenis_bahan_cetaks' => 'required',
            'vendors_id' => 'required',
            'notas_id' => 'required',
        ]);

        Pemesanan::create($request->all());

        return redirect()->route('pemesanans.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Debugging to see if the method is hit
        $url = '/pesanancetak/'.$id;
        return redirect($url);// This will show the ID passed in the URL
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $notas = Nota::all();
        return view('pemesanans.edit', compact('pemesanan', 'notas'));
    }
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pemesanan $pemesanan)
    {
        $request->validate([
            'penggunas_email' => 'required',
                       'jumlah' => 'required',
                       'url_file' => 'required',
                       'harga_cetaks_id' => 'required',
                       'harga_cetaks_id_jenis_bahan_cetaks' => 'required',
                       'vendors_id' => 'required',
                       'notas_id' => 'required',
                   ]);
           
                   $pemesanan->update($request->all());
           
                   return redirect()->route('pemesanans.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pemesanan $pemesanan)
    {
        $pemesanan->delete();
        return redirect()->route('pemesanans.index');
    }
}