<?php

namespace App\Http\Controllers;

use App\Models\DetailCetaks;
use App\Models\JenisBahanCetak;
use App\Models\OpsiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OpsiDetailController extends Controller
{
    public function cekDuplikatOpsi($opsi, $id_jenis_bahan, $value_detail)
    {
        $detail = DB::table('detail_cetaks')
            ->where('jenis_bahan_cetaks_id', '=', $id_jenis_bahan)
            ->where('value', '=', $value_detail)
            ->first();

        $opsi_detail = DB::table('opsi_details')
            ->where('opsi', '=', $opsi)
            ->where('detail_cetaks_id', '=', $detail->id)
            ->first();

        if ($opsi_detail) {
            return true;
        }
        return false;
    }

    public function index($id_detail)
    {
        $detail = DetailCetaks::find($id_detail);
        $opsiDetails = DB::table('opsi_details')
            ->where('detail_cetaks_id', '=', $id_detail)
            ->where('deleted_at', '=', null)
            ->get();
        $jenisbahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $vendorlayanan = DB::table("vendors_has_jenis_bahan_cetaks")
        ->where("jenis_bahan_cetaks_id", "=", $jenisbahan->id)
        ->first();

        return view('layanan.options', compact('detail', 'opsiDetails',"vendorlayanan"));
    }

    public function create($id_detail)
    {
        $detail = DB::table('detail_cetaks')
            ->where('id', '=', $id_detail)
            ->first();
        $layanan_vendor = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('jenis_bahan_cetaks_id', '=', $detail->jenis_bahan_cetaks_id)
            ->first();
        $layanan = [
            "id_vendor" => $layanan_vendor->vendors_id,
            "id_layanan" => $layanan_vendor->layanan_cetaks_id,
            "id_detail" => $id_detail,
        ];

        return view('layanan.createoption', compact('layanan')); // Pass it to the view
    }

    public function store(Request $request, OpsiDetail $opsi_detail)
    {
        if(!$request->input('opsi')){
            return back()->with("error", "Opsi harus diisi");
        }
        $detail_id = $request->input('id_detail');
        $biaya_tambahan = 0;
        if($request->input('biaya_tambahan')){
            $biaya_tambahan = $request->input('biaya_tambahan');
        }
        $detail = DB::table('detail_cetaks')
            ->where('id', '=', $detail_id)
            ->first();
        $layanan_vendor = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('jenis_bahan_cetaks_id', '=', $detail->jenis_bahan_cetaks_id)
            ->first();

        $vendor_id = $layanan_vendor->vendors_id;
        $layanan_id = $layanan_vendor->layanan_cetaks_id;

        $opsi = $request->input('opsi');

        $detail = DB::table('detail_cetaks')
            ->where('id', $detail_id)
            ->select('jenis_bahan_cetaks_id', 'value')
            ->first();


        if ($this->cekDuplikatOpsi($opsi, $detail->jenis_bahan_cetaks_id, $detail->value)) {
            return back()->with('error', 'Opsi sudah ada!');
        }

        $opsi_detail->opsi = $opsi;
        if ($request->input('deskripsi')) {
            $opsi_detail->deskripsi = $request->input('deskripsi');
        }
        $opsi_detail->biaya_tambahan = $biaya_tambahan;
        $opsi_detail->tipe = $request->input('tipe');
        $opsi_detail->detail_cetaks_id = $detail_id;

        $opsi_detail->save();

        $jenis_bahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenis_bahan->save();

        return redirect()->route('layanan.detail_layanan', [$vendor_id, $layanan_id])->with('message', "Sukses menambahkan opsi ke detail dengan nama $detail->value pada $jenis_bahan->nama");
    }

    public function edit($id)
    {
        $opsi_detail = OpsiDetail::findOrFail($id);
        $detail = DB::table('detail_cetaks')
            ->where('id', '=', $opsi_detail->detail_cetaks_id)
            ->first();
        $layanan_vendor = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('jenis_bahan_cetaks_id', '=', $detail->jenis_bahan_cetaks_id)
            ->first();
        $layanan = [
            "id_vendor" => $layanan_vendor->vendors_id,
            "id_layanan" => $layanan_vendor->layanan_cetaks_id,
        ];
        return view('layanan.editoption', compact('detail', 'opsi_detail', 'layanan')); // Pass it to the view
    }

    public function update(Request $request)
    {
        if(!$request->input('opsi')){
            return back()->with("error", "Opsi harus diisi");
        }
        $detail_id = $request->input('id_detail');

        $detail = DB::table('detail_cetaks')
            ->where('id', $detail_id)
            ->first();
        $biaya_tambahan = 0;
        if ($request->input('biaya_tambahan')){
            $biaya_tambahan = $request->input('biaya_tambahan');
        }

        $opsi_detail = OpsiDetail::findOrFail($request->input('id_opsi_detail'));
        $opsi_detail->opsi = $request->input('opsi');
        if ($request->input('deskripsi')) {
            $opsi_detail->deskripsi = $request->input('deskripsi');
        }
        $opsi_detail->biaya_tambahan = $biaya_tambahan;
        $opsi_detail->tipe = $request->input('tipe');
        $opsi_detail->detail_cetaks_id = $detail_id;
        $opsi_detail->save();

        $jenis_bahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenis_bahan->save();

        $jenisbahan = new JenisBahanController;
        $jenisbahan->updateJenisBahan($detail->jenis_bahan_cetaks_id);

        return redirect()->route('opsidetail.index', [$detail_id]);
    }

    public function destroy(Request $request)
    {
        $opsi_detail = OpsiDetail::findOrFail($request->input('id'));
        $opsi_detail->deleted_at = Carbon::now('Asia/Jakarta');
        $opsi_detail->save();
        $detail = DB::table('detail_cetaks')
            ->where('id', $opsi_detail->detail_cetaks_id)
            ->select('jenis_bahan_cetaks_id', 'id')
            ->first();

        $jenis_bahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenis_bahan->save();

        $jenisbahancontroller  = new JenisBahanController;
        $jenisbahancontroller ->updateJenisBahan($detail->jenis_bahan_cetaks_id);

        return redirect()->route('opsidetail.index', [$detail->id]);
    }
}
