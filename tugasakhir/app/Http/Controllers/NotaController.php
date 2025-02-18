<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use App\Models\NotaProgress;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function terimaPesanan($id_nota){
        $nota = Nota::findOrFail($id_nota);
        $nota->waktu_menerima_pesanan = Carbon::now('Asia/Jakarta');
        $nota->save();

        return back()->with('message', 'Pesanan berhasil diterima');
    }

    public function kirimcontoh(Request $request){
        $request->validate([
            'fileperubahan' => 'required|file|mimes:pdf,jpg,jpeg,png,gif|max:20480',   
            'idpemesanan' => 'required',
        ]);
        $id_nota = DB::table('pemesanans')
        ->where('id','=', $request->idpemesanan)
        ->select('notas_id')
        ->first();

        $id_nota = $id_nota->notas_id;

        $notas_progress_latest = DB::table('notas_progress')
        ->where('pemesanans_id','=', $request->idpemesanan)
        ->where('notas_id','=', $id_nota)
        ->where('progress', '!=', 'menunggu verifikasi')
        ->orderBy('urutan_progress', 'desc')
        ->select('urutan_progress')
        ->first();

        $latest_progress = $notas_progress_latest->urutan_progress + 1;

        $existing_progress = DB::table('notas_progress')
            ->where('pemesanans_id', '=', $request->idpemesanan)
            ->where('notas_id', '=', $id_nota)
            ->where('urutan_progress', '=', $latest_progress)
            ->exists();

        if ($existing_progress) {
            return response()->json(['message' => 'Progress sudah ditangani'], 409); // Conflict status
        }

        $file = $request->file('fileperubahan');
        $file_name = $latest_progress . '.pdf';

        $directory = base_path('../verifikasi_file/'. $request->idpemesanan);

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true); //0755: pemilik = rwx; grup, lainnya = rx (tidak bisa write)
        }
        $file->move($directory, $file_name);

        $relative_path = 'verifikasi_file/' . $request->idpemesanan . '/' . $file_name;

        $nota_progress = new NotaProgress();
        $nota_progress->pemesanans_id = $request->idpemesanan;
        $nota_progress->notas_id = $id_nota;
        $nota_progress->urutan_progress = $latest_progress;
        $nota_progress->waktu_progress = Carbon::now('Asia/Jakarta');
        $nota_progress->progress = 'menunggu verifikasi';
        $nota_progress->url_ubah_file = $relative_path;
        
        $nota_progress->save();

        return back()->with('message', 'Contoh file berhasil dikirim');
    }

    public function lihatperubahan(Request $request){

        $id_nota = DB::table('pemesanans')
        ->where('id','=', $request->idpemesanan)
        ->select('notas_id')
        ->first();

        $id_nota = $id_nota->notas_id;
        

        $notas_progress_latest = DB::table('notas_progress')
        ->where('pemesanans_id','=', $request->idpemesanan)
        ->where('notas_id','=', $id_nota)
        ->where('progress', '=', 'memperbaiki')
        ->orderBy('urutan_progress', 'desc')
        ->select('urutan_progress')
        ->first();

        $perubahan_db = DB::table('notas_progress')
        ->where('pemesanans_id','=', $request->idpemesanan)
        ->where('notas_id','=', $id_nota)
        ->where('urutan_progress', '=', $notas_progress_latest->urutan_progress)
        ->select('perubahan')
        ->first();

        if (!$perubahan_db) {
            return response()->json(['perubahan' => 'Belum ada perubahan ditemukan']);
        }

        $perubahan = $perubahan_db->perubahan;

        return response()->json(['perubahan' => $perubahan]);
    }

    public function index()
    {
        $notas = Nota::all();
        return view('notas.index', compact('notas'));
    }

}
