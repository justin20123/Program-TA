<?php

namespace App\Http\Controllers;

use App\Models\Nota;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    // Existing methods...

    public function tinjau($vendors_id)
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();

        $ulasan = DB::table('pemesanans')
            ->where('vendors_id', '=', $vendors_id)
            ->whereNotNull('notas_id')
            ->select('notas_id', 'penggunas_email')
            ->groupBy('notas_id', 'penggunas_email') 
            ->get();
        

        $ulasan_filtered = [];

        foreach($ulasan as $u){
            $data_nota = [];
            $nota = DB::table('notas')
            ->where('id', '=', $u->notas_id)
            // ->whereBetween('waktu_selesai', [$startOfWeek, $endOfWeek])
            ->first();

            

            // $dateTime = new DateTime($nota->waktu_selesai);
            // $waktu_selesai = $dateTime->format('d F, Y H:i');

            $u->waktu_selesai = $nota->waktu_selesai;

            if($nota->ulasan != ""){
                $rating = DB::table('ratings')
                ->where('notas_id', '=', $u->notas_id)
                ->average('nilai');
    
                $u->rating = $rating;
    
                $pengguna = DB::table('penggunas')
                ->where('email', '=', $u->penggunas_email)
                ->first();
    
                $u->nama_pengguna = $pengguna->nama;
                $u->ulasan = $nota->ulasan;
                
                $data_nota['ulasan'] = $u;

                $pesanans = DB::table('pemesanans')
                ->where('notas_id', '=', $u->notas_id)
                ->distinct()
                ->get();

                $data_layanan = [];

                foreach($pesanans as $p){
                    $layananid = DB::table('vendors_has_jenis_bahan_cetaks')
                    ->where('jenis_bahan_cetaks_id', '=', $p->id)
                    ->select('layanan_cetaks_id')
                    ->first();

                    $namalayanan = DB::table('layanan_cetaks')
                    ->where('id', '=', $layananid->layanan_cetaks_id)
                    ->select('nama')
                    ->first();

                    array_push($data_layanan, $namalayanan->nama);
                }

                $data_nota['pesanan'] = $data_layanan;

                // dd($data_nota);

                array_push($ulasan_filtered, $data_nota);
            }
        }
        $ulasan = $ulasan_filtered;

        // dd($ulasan);

        $vendor = DB::table('vendors')
        ->where('id', '=', $vendors_id)
        ->select('nama')
        ->first();

        return view('tinjau', compact('ulasan', 'vendor'));
    }
}
