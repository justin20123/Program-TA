<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\Pemesanan;
use DateTime;
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
                'waktu_menerima_pesanan' => now(),
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
                'opsi_pengambilan' => "diantar",
                'waktu_menerima_pesanan' => now(),
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
                $a->prioritas = 1;
            }
            else if($a->progress == 'proses'){
                $a->prioritas = 0;
            }     

        }
        return $a;
    }

    public function convertPrioritasKeProgress($key){
        $prioritas = [  
            'proses', 
            'menunggu verifikasi'
        ];

        return $prioritas[$key];
    }

    public function formatDateTime($datetime){
        $dateTime = new DateTime($datetime);
        $formattedDate = $dateTime->format('d F, Y H:i');

        return $formattedDate; 
    }

    public function showDetailPesanan($idnota){
        

        $jumlah_pesanan = DB::table('pemesanans')
        ->where('notas_id', '=', $idnota)
        ->count();

        $arrSummary = [];
        $arrProgress = [];
        $maxPrioritas = 0;

        $notas = DB::table('notas')
        ->where('id', '=', $idnota)
        ->first();

        $status_antar = $notas->opsi_pengambilan;

        $transaksi = [
            'waktu_progress_format' => $this->formatDateTime($notas->waktu_transaksi),
            'progress' => 'transaksi'
        ];

        array_push($arrSummary, $transaksi);

        $terima = [
            'waktu_progress_format' => $this->formatDateTime($notas->waktu_menerima_pesanan),
            'progress' => 'Pesanan diterima'
        ];

        array_push($arrSummary, $terima);

        $proses = [
            'waktu_progress_format' => $this->formatDateTime($notas->waktu_menerima_pesanan),
            'progress' => 'Pesanan diproses'
        ];

        array_push($arrSummary, $proses);

        $notas_progress = DB::table('notas_progress')
        ->where('notas_id', '=', $idnota)
        ->orderBy('waktu_progress')
        ->get();

        $jumlah_selesai = 0;
        $npSelesai = [];
        foreach($notas_progress as $key => $np){
             if($key == 0){
                 array_push($arrProgress, $np);
             }
             else if($np->progress == 'menunggu verifikasi' || $np->progress == 'memperbaiki'){
                 array_push($arrProgress, $np);
             }
             else if($np->progress == 'selesai'){
                array_push($arrProgress, $np);
                $jumlah_selesai++;
                $npSelesai = $np;
             }
        }
        if($jumlah_selesai == $jumlah_pesanan){
            $proses_seleseai = [
                'waktu_progress_format' => $this->formatDateTime($np->waktu_progress),
                'progress' => 'Pesanan selesai diproses'
            ];
            array_push($arrSummary, $proses_seleseai);
            $arrProgress = null;
            $array = [];
            if($status_antar == 'diambil'){
                $array = [
                    'waktu_progress_format' => $this->formatDateTime($notas->waktu_tunggu_diambil),
                    'progress' => 'Menunggu diambil'
                ];
                
            }
            else if($status_antar == 'diantar'){
                $array = [
                    'waktu_progress_format' => $this->formatDateTime($notas->waktu_diantar),
                    'progress' => 'Pesanan sedang diantar'
                ];
            }
            array_push($arrSummary, $array);

            if($notas->waktu_selesai){
                $selesai = [
                    'waktu_progress_format' => $this->formatDateTime($notas->waktu_selesai),
                    'progress' => 'Pesanan sudah selesai'
                ];
                array_push($arrSummary, $selesai);
            }
            
            $arrSummaryReverse = [];
            
            for($i = count($arrSummary) -1; $i>=0; $i--){
                array_push($arrSummaryReverse, $arrSummary[$i]);
            }

        }

        return view('pesanan.orderinfo', compact('arrProgress', 'arrSummaryReverse'));
        
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
