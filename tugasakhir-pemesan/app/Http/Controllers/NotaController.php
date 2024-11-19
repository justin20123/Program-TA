<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function placeorder(Request $request)
    {
        $idnota = 0;
        if($request->opsiantar == "diambil"){
            $request->validate([
                'idpemesanans' => 'required',  
            ]);

            $idpemesanans = explode(",", $request->idpemesanans);

            $idnota = DB::table('notas')->insertGetid([
                'waktu_transaksi' => now(),
                'status' => "proses",
                'opsi_pengambilan' => "diambil",
                'tanggal_selesai' => null,
                'ulasan' => "",
                'catatan_antar' => $request->catatan_antar,
            ]); 
            
            
        }
        else{
            $request->validate([
                'idpemesanans' => 'required',
                'latitude' => 'required|',
                'longitude' => 'required|'
            ]);

            $idnota = DB::table('notas')->insertGetid([
                'waktu_transaksi' => now(),
                'status' => "proses",
                'opsi_pengambilan' => "diambil",
                'tanggal_selesai' => null,
                'longitude_pengambilan'=>$request->longitude,
                'latitude_pengambilan'=>$request->latitude,
                'ulasan' => $request->catatan_antar,
            ]); 
        }
        foreach($idpemesanans as $id){
            $pemesanan = Pemesanan::findOrFail($id);
            $pemesanan->notas_id = $idnota;
            $pemesanan->save();
        }

        return ["idnota"=>$idnota ,"message"=>"Pesanan berhasil dibuat, silahkan menunggu diproses"];
    }

    public function index()
    {
        $notas = Nota::all();
        return view('notas.index', compact('notas'));
    }

    public function indexPesanan(){

        $notas = DB::table('notas')
        ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
        ->where('notas.status', 'proses')
        ->select('notas.id as idnota', 'notas.waktu_transaksi as waktu_transaksi', 'notas.status', 'pemesanans.vendors_id as idvendor' , DB::raw('COUNT(pemesanans.id) as jumlah_pesanan'))
        ->groupBy('notas.id', 'notas.waktu_transaksi', 'notas.status', 'pemesanans.vendors_id')
        ->get();

        // dd($notas);

        return view('pesanan.vendors', compact('notas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'waktu_transaksi' => 'required',
            'status' => 'required',
            'opsi_pengambilan' => 'required',
            'alamat_pengambilan' => 'required',
            'tanggal_selesai' => 'required',
            'ulasan' => 'required',
        ]);

        Nota::create($request->all());

        return redirect()->route('notas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('notas.show', compact('nota'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view('notas.edit', compact('nota'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Nota $nota)
    {
        $request->validate([
            'waktu_transaksi' => 'required',
            'status' => 'required',
            'opsi_pengambilan' => 'required',
            'alamat_pengambilan' => 'required',
            'tanggal_selesai' => 'required',
            'ulasan' => 'required',
        ]);

        $nota->update($request->all());

        return redirect()->route('notas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Nota $nota)
    {
        $nota->delete();
        return redirect()->route('notas.index');
    }
}
