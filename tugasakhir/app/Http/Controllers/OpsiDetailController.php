<?php

namespace App\Http\Controllers;

use App\Models\JenisBahanCetak;
use App\Models\OpsiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpsiDetailController extends Controller
{
    public function create($vendor_id, $idlayanan, $iddetail)
    {
        $layanan = [
            "idvendor" => $vendor_id,
            "idlayanan" => $idlayanan,
            "iddetail" => $iddetail,
        ];

        return view('layanan.createoption', compact('layanan')); // Pass it to the view
    }

    public function store(Request $request, OpsiDetail $opsiDetail)
    {

        $vendorid = $request->input('idvendor');
        $layananid = $request->input('idlayanan');

        $detailid = $request->input('iddetail');

        $detail = DB::table('detail_cetaks')
        ->where('id', $detailid)
        ->select('jenis_bahan_cetaks_id')
        ->first();
        
        $jenisbahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $jenisbahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenisbahan->save();

        $opsiDetail->opsi = $request->input('opsi'); // Ensure you have the correct input name
        if ($request->input('deskripsi')) {
            $opsiDetail->deskripsi = $request->input('deskripsi');
        }
        $opsiDetail->biaya_tambahan = $request->input('biaya_tambahan');
        $opsiDetail->tipe = $request->input('tipe');
        $opsiDetail->detail_cetaks_id = $detailid;

        $opsiDetail->save();

        return redirect()->route('layanan.detail_layanan', [$vendorid, $layananid]);
    }

    public function edit($vendor_id, $idlayanan, $id)
    {
        $layanan = [
            "idvendor" => $vendor_id,
            "idlayanan" => $idlayanan,
        ];

        $opsiDetail = OpsiDetail::findOrFail($id); // Fetch the OpsiDetail
        return view('layanan.editoption', compact('opsiDetail', 'layanan')); // Pass it to the view
    }

    public function update(Request $request, OpsiDetail $opsiDetail, $id)
    {
        $opsiDetail = OpsiDetail::findOrFail($id);

        $vendorid = $request->input('idvendor');
        $layananid = $request->input('idlayanan');

        $detailid = $request->input('iddetail');

        $detail = DB::table('detail_cetaks')
        ->where('id', $detailid)
        ->select('jenis_bahan_cetaks_id')
        ->first();
        
        $jenisbahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $jenisbahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenisbahan->save();

        // Update the OpsiDetail properties
        $opsiDetail->opsi = $request->input('opsi'); // Ensure you have the correct input name
        if ($request->input('deskripsi')) {
            $opsiDetail->deskripsi = $request->input('deskripsi');
        }
        $opsiDetail->biaya_tambahan = $request->input('biaya_tambahan');
        $opsiDetail->detail_cetaks_id = $detailid;
        $opsiDetail->save();
        // Redirect to the detail_layanan method
        return redirect()->route('layanan.detail_layanan', [$vendorid, $layananid]);
    }

    public function destroy($vendor_id, $idlayanan, $id)
    {
        $opsiDetail = OpsiDetail::findOrFail($id);

        $detail = DB::table('detail_cetaks')
        ->where('id', $opsiDetail->detail_cetaks_id)
        ->select('jenis_bahan_cetaks_id')
        ->first();
        
        $jenisbahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $jenisbahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenisbahan->save();
        $opsiDetail->delete();
        return redirect()->route('layanan.detail_layanan', [$vendor_id, $idlayanan]);
    }
}
