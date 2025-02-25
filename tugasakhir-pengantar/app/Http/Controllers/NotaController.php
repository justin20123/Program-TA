<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    // Existing methods...

    public function index()
    {
        // dd('a');
        $notas = DB::table('notas')
            ->where('idpengantar', '=', Auth::user()->id)
            ->orderBy('waktu_diantar')
            ->get();

        // dd($notas);

        $layanan_menunggu_diantar = array();
        $layanan_selesai = array();

        foreach ($notas as $n) {
            $pemesanan = DB::table('pemesanans')
                ->where('notas_id', '=', $n->id)
                ->get();

            $pengguna = DB::table('penggunas')
                ->where('email', '=', $pemesanan[0]->penggunas_email)
                ->first();

            $n->nama_pengguna = $pengguna->nama;

            $data_nota['nota'] = $n;

            $data_layanan = [];

            foreach ($pemesanan as $p) {
                $layananid = DB::table('vendors_has_jenis_bahan_cetaks')
                    ->where('jenis_bahan_cetaks_id', '=', $p->id)
                    ->select('layanan_cetaks_id')
                    ->first();

                $layanan = DB::table('layanan_cetaks')
                    ->where('id', '=', $layananid->layanan_cetaks_id)
                    ->select('satuan', 'nama')
                    ->first();

                $p->satuan = $layanan->satuan;
                $p->layanan = $layanan->nama;

                array_push($data_layanan, $p);
            }

            $data_nota['pesanan'] = $data_layanan;

            // dd($data_nota);

            if ($n->waktu_selesai) {
                array_push($layanan_selesai, $data_nota);
            } else {
                array_push($layanan_menunggu_diantar, $data_nota);
            }
        }
        return view('home', compact('layanan_menunggu_diantar', 'layanan_selesai'));
    }
    public function detail($id)
    {
        $nota = DB::table('notas')
            ->where('id', '=', $id)
            ->first();

        $pesanans = DB::table('pemesanans')
            ->where('notas_id', '=', $id)
            ->get();
            $pengguna = DB::table('penggunas')
                ->where('email', '=', $pesanans[0]->penggunas_email)
                ->first();

            $nota->nama_pengguna = $pengguna->nama;
            $nota->nomor_telepon = $pengguna->nomor_telepon;

        foreach ($pesanans as $p) {
            $layananid = DB::table('vendors_has_jenis_bahan_cetaks')
                ->where('jenis_bahan_cetaks_id', '=', $p->id)
                ->select('layanan_cetaks_id')
                ->first();

            $layanan = DB::table('layanan_cetaks')
                ->where('id', '=', $layananid->layanan_cetaks_id)
                ->select('satuan', 'nama')
                ->first();

            $p->satuan = $layanan->satuan;
            $p->layanan = $layanan->nama;
        }


        // dd($nota);

        return view('detailpengantaran', compact('nota', 'pesanans'));
    }

    public function selesaipesanan($id){
        $nota = Nota::find($id);
        $nota->waktu_selesai = Carbon::now('Asia/Jakarta');
        $nota->save();

        return redirect()->route('home');
    }
}
