<?php

namespace App\Http\Controllers;

use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRating($vendors_id, $layanans_id)
    {
        $ratings = DB::table('ratings')
            ->join('notas', 'ratings.notas_id', '=', 'notas.id')
            ->join('pemesanans', 'notas.id', '=', 'pemesanans.notas_id')
            ->join('jenis_bahan_cetaks', 'pemesanans.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('pemesanans.vendors_id', '=', $vendors_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $layanans_id)
            ->whereNotNull('ratings.nilai')
            ->avg('ratings.nilai');
        return $ratings;
    }
    //ambil avg rating setiap layanan dalam 1 vendor
    public function getTotalNota($vendors_id, $layanans_id)
    {
        $total_nota = DB::table('notas')
            ->join('pemesanans', 'pemesanans.notas_id', '=', 'notas.id')
            ->join('jenis_bahan_cetaks', 'pemesanans.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
            ->where('pemesanans.vendors_id', '=', $vendors_id)
            ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $layanans_id)
            ->count();
        return $total_nota;
    }
    //ambil totap nota setiap layanan dalam 1 vendor

    public function index($vendor_id)
    {
        $layanans = DB::table('layanan_cetaks')
            ->join('vendors_has_jenis_bahan_cetaks', 'layanan_cetaks.id', '=', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id')
            ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
            ->select('layanan_cetaks.*')->distinct()
            ->get();
        foreach ($layanans as $l) {
            $ratings = $this->getRating($vendor_id, $l->id);
            $l->layanan_rating = $ratings;
            $l->total_nota =  $this->getTotalNota($vendor_id, $l->id);
        }
        $vendor = DB::table('vendors')
            ->where('vendors.id', '=', $vendor_id)
            ->first();

        return view('layanan.layanancetak', compact('layanans', 'vendor'));
    }

    public function detail_layanan($vendor_id, $idlayanan)
    {         
        $jenisbahan = DB::table('jenis_bahan_cetaks')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
        ->select('jenis_bahan_cetaks.id as idjenisbahan', 'jenis_bahan_cetaks.nama as namajenisbahan', 'vendors_has_jenis_bahan_cetaks.vendors_id as idvendor', 'vendors_has_jenis_bahan_cetaks.layanan_cetaks_id as idlayanan')
        ->get();

        $detailcetaks = DB::table('detail_cetaks')
        ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id') 
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $jenisbahan[0]->idjenisbahan   )
        ->select('detail_cetaks.*', 'opsi_details.id as idopsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan')
        ->get();

        

        $opsiDetail = [];
        foreach ($detailcetaks as $detail) {
            if (!isset($opsiDetail[$detail->id])) {
                $opsiDetail[$detail->id] = [
                    'detail' => $detail,
                    'opsi' => [],
                ];
            }
            if ($detail->idopsi) { 
                $opsiDetail[$detail->id]['opsi'][] = [
                    'id' => $detail->idopsi,
                    'opsi' => $detail->opsi, 
                    'biaya_tambahan' => $detail->biaya_tambahan, 
                ];
            }
        } 

        $opsiDetail = array_values($opsiDetail);

        $namaLayanan = DB::table('layanan_cetaks')
        ->where('id','=',$idlayanan)
        ->select('nama')
        ->first();
        $rating = $this->getRating($vendor_id, $idlayanan);
        $totalNota = $this->getTotalNota($vendor_id, $idlayanan);
        if ($namaLayanan) {
            $layanan = [
                'namaLayanan' => $namaLayanan->nama, 
                'rating' => $this->getRating($vendor_id, $idlayanan),
                'totalNota' => $this->getTotalNota($vendor_id, $idlayanan)
            ];
        } else {

            $layanan = [
                'namaLayanan' => 'Unknown', 
                'rating' => 0,
                'totalNota' => 0 
            ];
        }
        
        // dd($jenisbahan);
        // dd($opsiDetail);

        return view('layanan.detail', compact('opsiDetail', 'layanan', 'jenisbahan'));
    }

    public function detail_layanan_load($vendor_id, $idlayanan, $idjenisbahan){
        $detailcetaks = DB::table('detail_cetaks')
        ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id') 
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)
        ->select('detail_cetaks.*', 'opsi_details.id as idopsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan')
        ->get();

        $opsiDetail = [];
        foreach ($detailcetaks as $detail) {
            if (!isset($opsiDetail[$detail->id])) {
                $opsiDetail[$detail->id] = [
                    'detail' => $detail,
                    'opsi' => [],
                ];
            }
            if ($detail->idopsi) { 
                $opsiDetail[$detail->id]['opsi'][] = [
                    'id' => $detail->idopsi,
                    'opsi' => $detail->opsi, 
                    'biaya_tambahan' => $detail->biaya_tambahan, 
                ];
            }
        } 

        $opsiDetail = array_values($opsiDetail);

        // dd($opsiDetail);

            return json_encode(['result'=>'success', 'data'=>$opsiDetail]);
    }

    public function edit_opsi($vendor_id, $idlayanan, $idjenisbahan, $iddetail, $idopsi){
        $detailcetaks = DB::table('detail_cetaks')
        ->join('jenis_bahan_cetaks', 'detail_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->leftJoin('opsi_details', 'detail_cetaks.id', '=', 'opsi_details.detail_cetaks_id') 
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)
        ->where('detail_cetaks.id', '=', $iddetail)
        ->where('opsi_details.id', '=', $idopsi)

        ->select('detail_cetaks.*', 'opsi_details.id as idopsi', 'opsi_details.opsi as opsi', 'opsi_details.biaya_tambahan as biaya_tambahan', 'detail_cetaks.value as namadetail')
        ->first();

        $layanan = [
            "idvendor" => $vendor_id,
            "idlayanan" => $idlayanan,
            "idjenisbahan" => $idjenisbahan,
            "iddetail" => $iddetail,
        ];

        return view('layanan.editoption', compact('detailcetaks','layanan'));
    }

    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('layanans.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $vendorid = $request->input('idvendor');
        $layananid = $request->input('idlayanan');
        
        $layanan = new Layanan();
        $layanan->nama = $request->input('nama');
        if($request->input('deskripsi')){
            $layanan->deskripsi = $request->input('deskripsi');
        }
        $layanan->biaya_tambahan = $request->input('biaya_tambahan');
        $layanan->save();
        return redirect()->route('layanan.detail_layanan', [$vendorid, $layananid]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function show(Layanan $layanan)
    {
        return view('layanans.show', compact('layanan'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function edit(Layanan $layanan)
    {
        return view('layanans.edit', compact('layanan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Layanan $layanan)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Layanan  $layanan
     * @return \Illuminate\Http\Response
     */
    public function destroy(Layanan $layanan)
    {
        $layanan->delete();
        return redirect()->route('layanans.index');
    }
}
