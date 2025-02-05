<?php

namespace App\Http\Controllers;

use App\Models\Nota;
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
            ->distinct() 
            ->get();
        
        $ulasan = [];

        foreach($ulasan as $u){
            $nota = Nota::find($u->notas_id);

            $rating = DB::table('ratings')
            ->where('notas_id', '=', $u->notas_id)
            ->average('nilai');

            $u->rating = $rating;

            $pengguna = DB::table('pengguna')
            ->where('email', '=', $u->penggunas_email)
            ->first();

            $u->nama_pengguna = $pengguna->nama;
            $u->ulasan = $nota->ulasan;

        
        }

        $vendor = DB::table('vendors')
        ->where('id', '=', $vendors_id)
        ->select('nama')
        ->first();

        return view('notas.tinjau', compact('ulasan', 'vendor'));
    }
}
