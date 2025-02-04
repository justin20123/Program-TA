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
        $opsiDetails = OpsiDetail::where('detail_cetaks_id', '=', $id_detail)->get();

        return view('layanan.options', compact('detail', 'opsiDetails'));
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
        $detail_id = $request->input('id_detail');
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

        if ($request->input('khusus')) {
            if ($this->cekDuplikatOpsi($opsi, $detail->jenis_bahan_cetaks_id, $detail->value)) {
                return back()->with('error', 'Opsi sudah ada!');
            }

            $opsi_detail->opsi = $opsi;
            if ($request->input('deskripsi')) {
                $opsi_detail->deskripsi = $request->input('deskripsi');
            }
            $opsi_detail->biaya_tambahan = $request->input('biaya_tambahan');
            $opsi_detail->tipe = $request->input('tipe');
            $opsi_detail->detail_cetaks_id = $detail_id;

            $opsi_detail->save();

            $jenis_bahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
            $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
            $jenis_bahan->save();

            return redirect()->route('layanan.detail_layanan', [$vendor_id, $layanan_id])->with('message', "Sukses menambahkan opsi ke detail dengan nama $detail->value pada $jenis_bahan->nama");
        } else {
            $id_jenis_bahan = DB::table('vendors_has_jenis_bahan_cetaks')
                ->where('vendors_id', '=', $vendor_id)
                ->where('layanan_cetaks_id', '=', $layanan_id)
                ->select('jenis_bahan_cetaks_id')
                ->get();

            $count = 0;

            foreach ($id_jenis_bahan as $j) {
                if (!$this->cekDuplikatOpsi($opsi, $j->jenis_bahan_cetaks_id, $detail->value)) {
                    $detail = DB::table('detail_cetaks')
                        ->where('jenis_bahan_cetaks_id', '=', $j->jenis_bahan_cetaks_id)
                        ->first();

                    $opsi_detail = new OpsiDetail();
                    $opsi_detail->opsi = $opsi;
                    if ($request->input('deskripsi')) {
                        $opsi_detail->deskripsi = $request->input('deskripsi');
                    }
                    $opsi_detail->biaya_tambahan = $request->input('biaya_tambahan');
                    $opsi_detail->tipe = $request->input('tipe');
                    $opsi_detail->detail_cetaks_id = $detail->id;
                    $opsi_detail->save();
                    $count++;
                } else {
                    $detail = DB::table('detail_cetaks')
                        ->where('jenis_bahan_cetaks_id', '=', $j->jenis_bahan_cetaks_id)
                        ->where('value', '=', $detail->value)
                        ->first();

                    if (!$detail) {
                        continue;
                    } else {
                        $id_opsi_detail = DB::table('opsi_details')
                            ->where('detail_cetaks_id', '=', $detail->id)
                            ->select('id')
                            ->first();

                        $opsi_detail = OpsiDetail::find($id_opsi_detail->id);
                        if ($request->input('deskripsi')) {
                            $opsi_detail->deskripsi = $request->input('deskripsi');
                        }
                        $opsi_detail->biaya_tambahan = $request->input('biaya_tambahan');
                        $opsi_detail->tipe = $request->input('tipe');
                        $opsi_detail->detail_cetaks_id = $detail_id;
                        $opsi_detail->save();
                    }
                }

                if ($count == 0) {
                    return redirect()->back()->with('error', "Tidak ada opsi yang ditambahkan karena semua jenis bahan sudah memiliki opsi $opsi pada detail ini");
                }
                $jenis_bahan = JenisBahanCetak::find($j->jenis_bahan_cetaks_id);
                $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
                $jenis_bahan->save();
            }
            if ($count < count($id_jenis_bahan)) {
                $selisih = count($id_jenis_bahan) - $count;
                return redirect()->route('layanan.detail_layanan', [$vendor_id, $layanan_id])->with('message', "$count opsi detail berhasil ditambahkan, $selisih opsi detail berhasil diperbarui pada setiap jenis bahan");
            }

            return redirect()->route('layanan.detail_layanan', [$vendor_id, $layanan_id])->with('message', "Sukses menambahkan opsi ke setiap detail dengan nama $detail->value di semua jenis bahan");
        }
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
        $detail_id = $request->input('id_detail');

        $detail = DB::table('detail_cetaks')
            ->where('id', $detail_id)
            ->first();
        $layanan_vendor = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('jenis_bahan_cetaks_id', '=', $detail->jenis_bahan_cetaks_id)
            ->first();

        $vendor_id = $layanan_vendor->vendors_id;
        $layanan_id = $layanan_vendor->layanan_cetaks_id;

        if ($request->input('khusus')) {
            $opsi_detail = OpsiDetail::findOrFail($detail_id);
            $opsi_detail->opsi = $request->input('opsi'); 
            if ($request->input('deskripsi')) {
                $opsi_detail->deskripsi = $request->input('deskripsi');
            }
            $opsi_detail->biaya_tambahan = $request->input('biaya_tambahan');
            $opsi_detail->detail_cetaks_id = $detail_id;
            $opsi_detail->save();

            $jenis_bahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
            $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
            $jenis_bahan->save();
        } else {
            $vendor_layanan = DB::table('vendors_has_layanan_cetaks')
                ->where('jenis_bahan_cetaks_id', '=', $detail->jenis_bahan_cetaks_id)
                ->first();

            $id_jenis_bahan = DB::table('vendors_has_layanan_cetaks')
                ->where('layanan_cetaks_id', '=', $vendor_layanan->layanan_cetaks_id)
                ->where('vendors_id', '=', $vendor_layanan->vendors_id)
                ->select('jenis_bahan_cetaks_id')
                ->get();

            foreach ($id_jenis_bahan as $jb) {
                $detail_non_khusus = DB::table('detail_cetaks')
                    ->where('jenis_bahan_cetaks_id', '=', $jb->jenis_bahan_cetaks_id)
                    ->first();

                $opsi_details = DB::table('opsi_details')
                ->where('detail_cetaks_id', '=', $detail_non_khusus->id)
                ->get();

                foreach ($opsi_details as $od) {
                    if($opsi_details->opsi == $request->input('opsi')){
                        $opsi_detail = OpsiDetail::find($od->id);
                        $opsi_detail->opsi = $request->input('opsi');
                        if ($request->input('deskripsi')) {
                            $opsi_detail->deskripsi = $request->input('deskripsi');
                        }
                        $opsi_detail->biaya_tambahan = $request->input('biaya_tambahan');
                        $opsi_detail->detail_cetaks_id = $detail_id;
                        $opsi_detail->save();
                        break;
                    } 
                }

                $jenis_bahan = JenisBahanCetak::find($jb->jenis_bahan_cetaks_id);
                $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
            }
        }
        // Redirect to the detail_layanan method
        return redirect()->route('opsidetail.index', [$detail_id]);
    }

    public function destroy(Request $request)
    {
        $opsi_detail = OpsiDetail::findOrFail($request->input('id'));
        $opsi_detail->delete();
        $detail = DB::table('detail_cetaks')
            ->where('id', $opsi_detail->detail_cetaks_id)
            ->select('jenis_bahan_cetaks_id', 'id')
            ->first();

        $jenis_bahan = JenisBahanCetak::find($detail->jenis_bahan_cetaks_id);
        $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenis_bahan->save();
        
        return redirect()->route('opsidetail.index', [$detail->id]);
    }
}
