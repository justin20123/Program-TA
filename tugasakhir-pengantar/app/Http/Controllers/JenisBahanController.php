<?php

namespace App\Http\Controllers;

use App\Models\JenisBahanCetak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisBahanController extends Controller
{
    public function edit($vendor_id, $idlayanan, $idjenisbahan){
        $jenisbahan = DB::table('jenis_bahan_cetaks')
        ->join('vendors_has_jenis_bahan_cetaks', 'vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', 'jenis_bahan_cetaks.id')
        ->where('vendors_has_jenis_bahan_cetaks.vendors_id', '=', $vendor_id)
        ->where('vendors_has_jenis_bahan_cetaks.layanan_cetaks_id', '=', $idlayanan)
        ->where('vendors_has_jenis_bahan_cetaks.jenis_bahan_cetaks_id', '=', $idjenisbahan)

        ->select('jenis_bahan_cetaks.*')
        ->first();

        $layanan = [
            "idvendor" => $vendor_id,
            "idlayanan" => $idlayanan,
        ];


        return view('layanan.editoptionjenisbahan', compact('jenisbahan', 'layanan'));
    }

    public function update(Request $request, $id)
    {

        $vendorid = $request->input('idvendor');
        $layananid = $request->input('idlayanan');
        
        $jenisbahan = JenisBahanCetak::findOrFail($id);
        $jenisbahan->nama = $request->input('nama');
        $jenisbahan->ukuran = 'example';
        $jenisbahan->gambar = 'example';
        $jenisbahan->deskripsi = $request->input('deskripsi');
        $jenisbahan->save();
        return redirect()->route('layanan.detail_layanan', [$vendorid, $layananid]);
    }
}
