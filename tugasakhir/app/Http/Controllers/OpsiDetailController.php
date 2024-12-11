<?php

namespace App\Http\Controllers;

use App\Models\OpsiDetail;
use Illuminate\Http\Request;

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
        
        $opsiDetail->delete();
        return redirect()->route('layanan.detail_layanan', [$vendor_id, $idlayanan]);
    }
}
