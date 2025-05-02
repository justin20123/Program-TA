<?php

namespace App\Http\Controllers;

use App\Models\DetailCetaks;
use App\Models\JenisBahanCetak;
use App\Models\OpsiDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetailCetaksController extends Controller
{
    public function updateJenisBahan($id){
        $jenis_bahan = JenisBahanCetak::find($id);
        $jenis_bahan->updated_at = Carbon::now('Asia/Jakarta');
        $jenis_bahan->save();
    }

    public function cekDupikatNama($value, $id_jenis_bahan){
        $detail_cetaks = DB::table('detail_cetaks')
        ->where('jenis_bahan_cetaks_id', '=', $id_jenis_bahan)
        ->where('value', '=', $value)
        ->first();

        if($detail_cetaks){
            return true;
        }
        return false;
    }

    public function create($id_jenis_bahan){
        $vendorlayanan = DB::table('vendors_has_jenis_bahan_cetaks')
        ->where('jenis_bahan_cetaks_id','=',$id_jenis_bahan)
        ->first();
        $jenis_bahan = JenisBahanCetak::find($id_jenis_bahan);
        $layanan = [
            "idvendor" => $vendorlayanan->vendors_id,
            "idlayanan" => $vendorlayanan->layanan_cetaks_id,
            "idjenisbahan" => $id_jenis_bahan,
        ];

        return view('layanan.createdetail', compact('layanan', 'jenis_bahan'));    
    }

    public function store(Request $request){
        $id_vendor = $request->input('id_vendor');
        $id_layanan = $request->input('id_layanan');

        if(!$request->input('ubah_semua')){
            if($this->cekDupikatNama($request->input('value'),$request->input('id_jenis_bahan'))){
                return redirect()->back()->with('error', 'Nama sudah ada');
            }


            $detail_cetaks = new DetailCetaks();

            $detail_cetaks->value = $request->input('value');
            $detail_cetaks->jenis_bahan_cetaks_id = $request->input('id_jenis_bahan');
            $detail_cetaks->save();

            $this->updateJenisBahan($request->input('id_jenis_bahan'));
        }
        else{
            $id_jenis_bahan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('vendors_id', '=', $id_vendor)
            ->where('layanan_cetaks_id', '=', $id_layanan)
            ->select('jenis_bahan_cetaks_id')
            ->get();

            $count = 0;
    
            foreach($id_jenis_bahan as $j){
                if(!$this->cekDupikatNama($request->input('value'),$j->jenis_bahan_cetaks_id)){
                    $detail_cetaks = new DetailCetaks();
                    $detail_cetaks->value = $request->input('value');
                    $detail_cetaks->jenis_bahan_cetaks_id = $j->jenis_bahan_cetaks_id;
                    $detail_cetaks->save();
    
                    $this->updateJenisBahan($j->jenis_bahan_cetaks_id);
                    $count++;
                }
            }

            if($count == 0){
                return redirect()->back()->with('error', 'Semua jenis bahan yang memiliki nama ini sudah ada');
            }
        }

        return redirect()->route('layanan.detail_layanan', [$id_vendor, $id_layanan]);    
    }
    
    public function update(Request $request, $id){
        $details = DetailCetaks::findOrFail($id);

        $details->value = $request->input('value');
        $details->save();

        $this->updateJenisBahan($details->jenis_bahan_cetaks_id);

        return response()->json(['success' => 'Detail with id ' . $id. ' updated successfully.']);
    }

    public function destroy(Request $request){
        $opsi_detail = DB::table('opsi_details')
        ->where('detail_cetaks_id','=',$request->input('id_detail'))
        ->get();

        $details = DetailCetaks::findOrFail($request->input('id_detail'));

        $vendorlayanan = DB::table('vendors_has_jenis_bahan_cetaks')
        ->where('jenis_bahan_cetaks_id','=',$details->jenis_bahan_cetaks_id)
        ->first();

        $id_vendor = $vendorlayanan->vendors_id;
        $id_layanan = $vendorlayanan->layanan_cetaks_id;

        foreach($opsi_detail as $od){
            DB::table('opsi_details')->where('id', '=', $od->id)->update(['deleted_at' => null]);
        }
        $details->deleted_at = Carbon::now('Asia/Jakarta');
        $details->save();

        return redirect()->route('layanan.detail_layanan', [$id_vendor, $id_layanan]);
    }
}
