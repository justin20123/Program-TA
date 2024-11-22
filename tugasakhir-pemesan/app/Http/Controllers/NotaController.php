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

    public function getStatus($nota){
        if(!$nota->waktu_menerima_pesanan){
            $status_nota = "Menunggu diterima vendor";
        }
        elseif(!$nota->waktu_diantar && !$nota->waktu_tunggu_diambil){
            $status_nota = "Pesanan diterima";
            
        }
        else if($nota->waktu_diantar || $nota->waktu_tunggu_diambil){
            if($nota->waktu_diantar){
                $status_nota = "Diantar";
            }
            else{
                $status_nota = "Menunggu diambil";
            }
            
        }
        else if($nota->waktu_selesai){
            $status_nota = "Selesai";
        }
        return $status_nota;
    }


    public function placeorder(Request $request)
    {
        $idnota = 0;
        if($request->opsiantar == "diambil"){
            $request->validate([
                'idpemesanans' => 'required',  
            ]);

            $idpemesanans = explode(",", $request->idpemesanans);

            $idnota = DB::table('notas')->insertGetid([
                'harga_total' => $request->harga_total,
                'waktu_transaksi' => now(),
                'opsi_pengambilan' => "diambil",
                'waktu_menerima_pesanan' => null,
                'waktu_diantar' => null,
                'waktu_tunggu_diambil' => null,
                'waktu_selesai' => null,
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
                'harga_total' => $request->harga_total,
                'waktu_transaksi' => now(),
                'waktu_menerima_pesanan' => now(),
                'opsi_pengambilan' => "diantar",
                'waktu_menerima_pesanan' => null,
                'waktu_diantar' => null,
                'waktu_tunggu_diambil' => null,
                'waktu_selesai' => null,
                'longitude_pengambilan'=>$request->longitude,
                'latitude_pengambilan'=>$request->latitude,
                'ulasan' => $request->catatan_antar,
            ]); 
        }
        foreach($idpemesanans as $id){
            $pemesanan = Pemesanan::findOrFail($id);
            $pemesanan->notas_id = $idnota;
            $pemesanan->save();

            DB::table('notas_progress')->insert([
                'pemesanans_id' => $id,
                'notas_id' => $idnota, 
                'tanggal_progress' => now(),
                'progress' => "proses",
            ]);
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
        ->select('notas.id as idnota', 'notas.waktu_transaksi as waktu_transaksi', 'pemesanans.vendors_id as idvendor' ,'notas.waktu_menerima_pesanan','notas.waktu_diantar','notas.waktu_tunggu_diambil','notas.waktu_selesai' , DB::raw('COUNT(pemesanans.id) as jumlah_pesanan'))
        ->groupBy('notas.id', 'notas.waktu_transaksi', 'pemesanans.vendors_id','notas.waktu_menerima_pesanan','notas.waktu_diantar','notas.waktu_tunggu_diambil','notas.waktu_selesai')
        ->orderBy('notas.waktu_transaksi')
        ->get();

        foreach ($notas as $n){ 
            
            $n->status = $this->getStatus($n);
            

            $vendor = DB::table('vendors')
            ->where('id', $n->idvendor)
            ->first();
            $n->nama_vendor = $vendor->nama;
            $n->foto_lokasi = $vendor->foto_lokasi;

            $liststatus = DB::table('notas_progress')
            ->where('notas_id','=', $n->idnota)
            ->select('progress')
            ->get();

        }

        return view('pesanan.vendors', compact('notas'));
    }

    public function convertProgressKePrioritas($array){
        foreach($array as $a){
            if($a->progress == 'menunggu verifikasi'){
                $a->prioritas = 4;
            }
            else if($a->progress == 'proses'){
                $a->prioritas = 3;
            }     
            else if($a->progress == 'sedang diantar'){
                $a->prioritas = 2;
            }
            else if($a->progress == 'menunggu diambil'){
                $a->prioritas = 1;
            }
            else if($a->progress == 'selesai'){
                $a->prioritas = 0;
            }

        }
        return $a;
    }

    public function convertPrioritasKeProgress($key){
        $prioritas = [
            'selesai', 
            'sedang diantar', 
            'menunggu diambil',  
            'proses', 
            'menunggu verifikasi'
        ];

        return $prioritas[$key];
    }

    public function showDetailPesanan($idnota){
        // $info_nota = DB::table('notas_progress')
        // ->where('notas_id', '=', $idnota)
        // ->get();

        // dd($info_nota);

        $pemesanans = DB::table('pemesanans')
        ->where('notas_id', '=', $idnota)
        ->get();

        
        $maxPrioritas = 0;

        foreach($pemesanans as $p){
            $minPrioritasPemesanan = 4;

            $info_nota_pemesanan = DB::table('notas_progress')
            ->where('notas_id', '=', $idnota)
            ->where('pemesanans_id', '=', $p->id)
            ->get();

            // dd($info_nota_pemesanan);

            $this->convertProgressKePrioritas($info_nota_pemesanan);

            foreach ($info_nota_pemesanan as $inp){
                if($inp->prioritas < $minPrioritasPemesanan){
                    //cari progress terjauh (prioritas terendah)
                    $minPrioritasPemesanan = $inp->prioritas;
                }
            }
            if($minPrioritasPemesanan < $maxPrioritas){
                //cari prioritas tertinggi
                $maxPrioritas = $minPrioritasPemesanan;
            }
        }



        $progress = $this->convertPrioritasKeProgress($maxPrioritas);

        dd($progress);


        $pemesanans = DB::table('pemesanans')
        ->where('notas_id', '=', $idnota)
        ->get();

        $arrPerbandingan = [];

        foreach($pemesanans as $key=>$p) {
            $progress = DB::table('notas_progress')
            ->where('notas_id', '=', $idnota)
            ->where('pemesanans_id', '=', $p->id)
            ->orderBy('tanggal_progress', 'asc')
            ->get();

            $arrPerbandingan[$key] = $progress;
        }

        $result = [];

        $key = [];
        foreach($arrPerbandingan as $ap){
            if($ap['progress'] == "menunggu verifikasi"){
                array_push($result, $ap);
            }
            else{
                
            }
        }
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
