<?php

namespace App\Http\Controllers;

use App\Models\JenisBahanCetak;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisBahanController extends Controller
{
    public function store(Request $request){

        $jenisbahan_id = JenisBahanCetak::insertGetId([
            'nama' => $request->input('nama'),
            'gambar' => '',
            'deskripsi' => $request->input('deskripsi')
        ]);

        DB::table('vendors_has_jenis_bahan_cetaks')->insert([
            'layanan_cetaks_id' => $request->input('id_layanan'),
            'vendors_id' => $request->input('id_vendor'),
            'jenis_bahan_cetaks_id' => $jenisbahan_id,
        ]);

        return redirect()->route('layanan.detail_layanan', [$request->input('id_vendor'), $request->input('id_layanan')]);
    }
    public function edit($vendor_id, $idlayanan, $idjenisbahan){
        // $jenisbahan = DB::table('jenis_bahan_cetaks')
        // ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        // ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        // ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
        // ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)

        // ->select('jenis_bahan_cetaks.*')
        // ->first();

        // $layanan = [
        //     "idvendor" => $vendor_id,
        //     "idlayanan" => $idlayanan,
        // ];


        // return view('layanan.editoptionjenisbahan', compact('jenisbahan', 'layanan'));
    }

    public function update(Request $request)
    {
        try{
            $request->validate([
                'nama' =>'required|string|max:100',
                'deskripsi' =>'nullable|string|max:250'
            ]);
        }
        catch(Exception $e){
            return back()->withInput()->with('error', $e->getMessage());
        }

        $id = $request->input('id_jenis_bahan');
        $vendorlayanan = DB::table('vendors_has_jenis_bahan_cetaks')
        ->where('jenis_bahan_cetaks_id', '=', $id)
        ->first();

        $deskripsi = $request->input('deskripsi');
        if($deskripsi == null){
            $deskripsi = "";
        }
        
        $jenisbahan = JenisBahanCetak::findOrFail($id);
        $jenisbahan->nama = $request->input('nama');
        $jenisbahan->gambar = '';
        $jenisbahan->deskripsi = $deskripsi;
        $jenisbahan->save();
        return redirect()->route('layanan.detail_layanan', [$vendorlayanan->vendors_id, $vendorlayanan->layanan_cetaks_id]);

    }

    public function destroy(Request $request)
    {
        $id = $request->input('id_jenis_bahan');
        $vendorlayanan = DB::table('vendors_has_jenis_bahan_cetaks')
        ->where('jenis_bahan_cetaks_id', '=', $id)
        ->first();

        $jenisbahan = JenisBahanCetak::findOrFail($id);
        $jenisbahan->delete();

        DB::table('vendors_has_jenis_bahan_cetaks')
        ->where('jenis_bahan_cetaks_id', '=', $id)
        ->delete();

        

        

        
        return redirect()->route('layanan.detail_layanan', [$vendorlayanan->vendors_id, $vendorlayanan->layanan_cetaks_id]);
    }
}
