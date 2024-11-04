<?php

namespace App\Http\Controllers;

use App\Models\HargaCetak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HargaCetakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function cekHarga($jumlahCetak, $idjenisBahan){
        $hargacetak = DB::table('harga_cetaks')->where('id_bahan_cetaks','=',$idjenisBahan)->get();
        $maxJumlahMin = 0;
        $hargaMaxJumlahMin = 0;
        $harga = null;
        foreach($hargacetak as $h){
            
            if($h->jumlah_cetak_minimum <= $jumlahCetak && $h->jumlah_cetak_maksimum >= $jumlahCetak){
                $harga = $h->harga_satuan;
                if($h->jumlah_cetak_minimum > $maxJumlahMin){
                    $maxJumlahMin = $h->jumlah_cetak_minimum;
                    $hargaMaxJumlahMin = $h->harga_satuan;
                }
            }
            if(!$harga){
                $harga = $hargaMaxJumlahMin;
                //kalau sampai terakhir belum dapat, masukkan ke minimum yang terbesar
            }
        }
        return $harga;
        
    }

    public function getHarga($idvendor, $idlayanan){
        $jenisbahan = DB::table('vendors_has_jenis_bahan_cetaks')
        ->join('jenis_bahan_cetaks', 'jenis_bahan_cetaks.id','=','vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id')
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id','=',$idvendor)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id','=',$idlayanan)
        ->select()
        ->get();
        $arrAvgHarga = [];
        foreach($jenisbahan as $jb){
            //cek harga satuan tergeneralisasi dengan 10, 100, 200, 300, 400, 500, 1000
            $harga10 = $this->cekHarga(10, $jb);
            $harga100 = $this->cekHarga(100, $jb);
            $harga200 = $this->cekHarga(200, $jb);
            $harga300 = $this->cekHarga(300, $jb);
            $harga400 = $this->cekHarga(400, $jb);
            $harga500 = $this->cekHarga(500, $jb);
            $harga1000 = $this->cekHarga(1000, $jb);

            $avgHarga = ($harga10 + $harga100 + $harga200 + $harga300 + $harga400 + $harga500 + $harga1000)/7;
            array_push($arrAvgHarga, $avgHarga);
        }
        $totalHargaSatuan = 0;
        $totalJenisBahan = 0;
        //setiap jenis bahan masuk foreach
        foreach($arrAvgHarga as $avgHarga){
            $totalHargaSatuan += $avgHarga;
            $totalJenisBahan++;
        }
        //average harga di vendor (sesuai layanan)
        $avgHargaPerVendor = $totalHargaSatuan/$totalJenisBahan;
        return $avgHargaPerVendor;
    }

    public function index($id)
    {
        $hargas = DB::table('harga_cetaks')
        ->where('id_bahan_cetaks', $id)
        ->select('*')
        ->get();

        // dd($hargas);

        $detail = DB::table('layanan_cetaks')
        ->join('vendors_has_jenis_bahan_cetaks','vendors_has_jenis_bahan_cetaks.layanan_cetaks_id','=','layanan_cetaks.id')
        ->join('jenis_bahan_cetaks','vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id','=','jenis_bahan_cetaks.id')
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id','=',$id)
        ->select('layanan_cetaks.satuan as satuan','jenis_bahan_cetaks.nama as namajenisbahan', 'jenis_bahan_cetaks.id as idjenisbahan')
        ->first();
        return view('layanan.opsiharga', compact('hargas','detail'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $detail = DB::table('jenis_bahan_cetaks')
        ->where('id','=',$id)
        ->select('nama','id')
        ->first();
    
        return view('layanan.tambahharga', compact('detail'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $hargaCetak = new HargaCetak();

        $hargaCetak->id_bahan_cetaks = $request->input('idjenisbahan');
        $hargaCetak->jumlah_cetak_minimum = $request->input('min');
        $hargaCetak->jumlah_cetak_maksimum =$request->input('max');
        $hargaCetak->harga_satuan = $request->input('harga');

        $hargaCetak->save();    

        return redirect()->route('harga.index', $request->input('idjenisbahan'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HargaCetak  $hargaCetak
     * @return \Illuminate\Http\Response
     */
    public function show(HargaCetak $hargaCetak)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HargaCetak  $hargaCetak
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $harga = DB::table('harga_cetaks')
        ->where('id', '=', $id)
        ->select('*')
        ->first();

        $detail = DB::table('jenis_bahan_cetaks')
        ->where('id','=',$harga->id_bahan_cetaks)
        ->select('nama','id')
        ->first();
    
        return view('layanan.editharga', compact('harga','detail'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\HargaCetak  $hargaCetak
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $hargaCetak = HargaCetak::findOrFail($id);

        $hargaCetak->jumlah_cetak_minimum = $request->input('min');
        $hargaCetak->jumlah_cetak_maksimum =$request->input('max');
        $hargaCetak->harga_satuan = $request->input('harga');

        $hargaCetak->save();
        
        // dd($hargaCetak);

        return redirect()->route('harga.index', $request->input('idjenisbahan'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HargaCetak  $hargaCetak
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $hargaCetak = HargaCetak::findOrFail($request->input('idharga'));
        $hargaCetak->delete();
        return redirect()->route('harga.index', $request->input('idjenisbahan'));
    }
}
