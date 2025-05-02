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
    public function index($id)
    {
        $hargas = DB::table('harga_cetaks')
        ->where('id_bahan_cetaks', $id)
        ->select('*')
        ->get();

        $detail = DB::table('layanan_cetaks')
        ->join('vendors_has_jenis_bahan_cetaks','vendors_has_jenis_bahan_cetaks.layanan_cetaks_id','=','layanan_cetaks.id')
        ->join('jenis_bahan_cetaks','vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id','=','jenis_bahan_cetaks.id')
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id','=',$id)
        ->select('layanan_cetaks.satuan as satuan','jenis_bahan_cetaks.nama as nama_jenis_bahan', 'jenis_bahan_cetaks.id as id_jenis_bahan', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id as id_layanan_cetak', 'vendors_has_jenis_bahan_cetaks.vendors_id as id_vendor')
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
        $harga_cetak = new HargaCetak();

        $harga_cetak->id_bahan_cetaks = $request->input('id_jenis_bahan');
        $harga_cetak->jumlah_cetak_minimum = $request->input('min');
        $harga_cetak->jumlah_cetak_maksimum = $request->input('max');
        $harga_cetak->harga_satuan = $request->input('harga');

        $harga_cetak->save();    

        return redirect()->route('harga.index', $request->input('id_jenis_bahan'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\HargaCetak  $harga_cetak
     * @return \Illuminate\Http\Response
     */
    public function show(HargaCetak $harga_cetak)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\HargaCetak  $harga_cetak
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
     * @param  \App\Models\HargaCetak  $harga_cetak
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $harga_cetak = HargaCetak::findOrFail($id);

        $harga_cetak->jumlah_cetak_minimum = $request->input('min');
        $harga_cetak->jumlah_cetak_maksimum = $request->input('max');
        $harga_cetak->harga_satuan = $request->input('harga');

        $harga_cetak->save();
        
        return redirect()->route('harga.index', $request->input('id_jenis_bahan'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\HargaCetak  $harga_cetak
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $harga_cetak = HargaCetak::findOrFail($request->input('id_harga'));
        $harga_cetak->delete();
        return redirect()->route('harga.index', $request->input('id_jenis_bahan'));
    }
}
