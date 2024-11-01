<?php

namespace App\Http\Controllers;

use App\Models\DetailCetaks;
use App\Models\OpsiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailCetaksController extends Controller
{
    public function create($idvendor, $idlayanan, $idjenisbahan){

        $layanan = [
            "idvendor" => $idvendor,
            "idlayanan" => $idlayanan,
            "idjenisbahan" => $idjenisbahan,
        ];

        return view('layanan.createdetail', compact('layanan'));    
    }

    public function store(Request $request, DetailCetaks $detailcetaks){
        $idvendor = $request->input('idvendor');
        $idlayanan = $request->input('idlayanan');

        $detailcetaks->value = $request->input('value');
        $detailcetaks->jenis_bahan_cetaks_id = $request->input('idjenisbahan');
        $detailcetaks->save();

        return redirect()->route('layanan.detail_layanan', [$idvendor, $idlayanan]);    }
    
    public function update(Request $request, $id){
        $details = DetailCetaks::findOrFail($id);

        $details->value = $request->input('value');
        $details->save();

        return response()->json(['success' => 'Detail with id ' . $id. ' updated successfully.']);
    }

    public function destroy(Request $request){
        $opsidetail = DB::table('opsi_details')
        ->where('detail_cetaks_id','=',$request->iddetail)
        ->get();
        $gambar = DB::table('gambars')
        ->where('detail_cetaks_id','=',$request->iddetail)
        ->get();
        
        $idvendor = $request->idvendor;
        $idlayanan = $request->idlayanan;

        $details = DetailCetaks::findOrFail($request->iddetail);
        // dd($details);
        foreach($opsidetail as $od){
            DB::table('opsi_details')->where('id', '=', $od->id)->delete();
        }
        foreach($gambar as $g){
            DB::table('gambars')->where('id', '=', $g->id)->delete();
        }
        $details->delete();

        return redirect()->route('layanan.detail_layanan', [$idvendor, $idlayanan]);
    }
}
