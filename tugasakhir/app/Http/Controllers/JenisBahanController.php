<?php

namespace App\Http\Controllers;

use App\Models\JenisBahanCetak;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JenisBahanController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
                'deskripsi' => 'nullable|string|max:250'
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
        $deskripsi = $request->input('deskripsi');
        if ($deskripsi == null) {
            $deskripsi = "";
        }

        $jenisbahan_id = JenisBahanCetak::insertGetId([
            'nama' => $request->input('nama'),
            'gambar' => '',
            'deskripsi' => $deskripsi
        ]);

        DB::table('vendors_has_jenis_bahan_cetaks')->insert([
            'layanan_cetaks_id' => $request->input('id_layanan'),
            'vendors_id' => $request->input('id_vendor'),
            'jenis_bahan_cetaks_id' => $jenisbahan_id,
        ]);

        return redirect()->route('layanan.detail_layanan', [$request->input('id_vendor'), $request->input('id_layanan')]);
    }


    public function update(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:100',
                'deskripsi' => 'nullable|string|max:250'
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $id = $request->input('id_jenis_bahan');
        $vendorlayanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('jenis_bahan_cetaks_id', '=', $id)
            ->first();

        $deskripsi = $request->input('deskripsi');
        if ($deskripsi == null) {
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
            ->update(['deleted_at' => now()]);

        return redirect()->route('layanan.detail_layanan', [$vendorlayanan->vendors_id, $vendorlayanan->layanan_cetaks_id]);
    }

    public function uploadfotojenisbahan(Request $request)
    {
        $request->validate([
            'file_foto' => 'required|file|mimes:jpg,jpeg,png,gif|max:20480',
            'id_jenis_bahan' => 'required',
        ]);
        $id = $request->input('id_jenis_bahan');
        $file = $request->file('file_foto');

        $vendorlayanan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('jenis_bahan_cetaks_id', '=', $id)
            ->first();





            $file_name = $id . '.jpg';

            $directory = base_path('../jenisbahan');

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true); //0755: pemilik = rwx; grup, lainnya = rx (tidak bisa write)
            }
            $file->move($directory, $file_name);

            $relative_path = 'jenisbahan/' . $file_name;

            $jenisbahan = JenisBahanCetak::find($id);
            $jenisbahan->gambar = $relative_path;
            $jenisbahan->save();
        




        return redirect()->route('layanan.detail_layanan', [$vendorlayanan->vendors_id, $vendorlayanan->layanan_cetaks_id]);
    }
}
