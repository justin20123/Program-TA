<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
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

    public function submitpesanan(Request $request){
        $request->validate([
            'jumlah' => 'required',
            'jenis_bahan_cetaks_id' => 'required',
            'idopsidetail' => 'required',
            'vendors_id' => 'required',
        ]);

        $hargacetakcontroller = new HargaCetakController();

        $idhargacetak = $hargacetakcontroller->cekHarga($request->jumlah, $request->jenis_bahan_cetaks_id, true);
        
        $id = DB::table('pemesanans')->insertGetId([
            'penggunas_email' => 'email1@email.com', 
            'jumlah' => $request->input('jumlah'), 
            'url_file' => '', 
            'catatan' => $request->input('catatan'),
            'harga_cetaks_id' => $idhargacetak,
            'jenis_bahan_cetaks_id' => $request->input('jenis_bahan_cetaks_id'), 
            'vendors_id' => $request->input('vendors_id'), 
        ]);

        foreach($request->idopsidetail as $od){
            DB::table('pemesanans_has_opsi_details')->insert([
                'pemesanans_id'=>$id,
                'opsi_details_id'=>$od,
            ]);
        }

        return ["idpemesanan"=>$id, "idvendor"=>$request->input('vendors_id')];


    }

    public function uploadfile(Request $request){
        $file = $request->file('fileInput');
        $fileName = $request->idpemesanan . '.pdf'; 
        $directory = base_path('../pemesanans');
        $file->move($directory, $fileName);

        $fileUrl = '../pemesanans/' . $fileName;
        $pemesanan = Pemesanan::find($request->idpemesanan);
        $pemesanan->url_file = $fileUrl;
        $pemesanan->save();

        return response()->json([
            'message' => 'Pemesanan created successfully!',
        ], 201);

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
