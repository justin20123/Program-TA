<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($vendors_id, $layanan_id)
    {
        $idjenisbahan = DB::table('vendors_has_jenis_bahan_cetaks')
            ->where('layanan_cetaks_id', '=', $layanan_id)
            ->where('vendors_id', '=', $vendors_id)
            ->get()
            ->toArray();

        $pemesanan = DB::table('pemesanans')
            ->whereIn('jenis_bahan_cetaks_id', array_column($idjenisbahan, 'jenis_bahan_cetaks'))
            ->distinct('notas_id')
            ->get();

        foreach ($pemesanan as $p) {
            $nota = DB::table('notas')
            ->where('id', '=', $p->notas_id)
            ->first();

            $p->ulasan = $nota->ulasan;

            $rating = DB::table('ratings')
            ->where('notas_id', '=', $p->notas_id)
            ->average('nilai');
            $p->rating = $rating;

            $pemesan = DB::table('penggunas')
            ->where('email', '=', $p->penggunas_email)
            ->first();
            $p->nama_pemesan = $pemesan->nama;

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
