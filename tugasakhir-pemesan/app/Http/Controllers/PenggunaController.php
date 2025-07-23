<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\VendorHasPengguna;
use Carbon\Carbon;
use Database\Seeders\VendorHasPenggunaSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{


    public function openmasukdana(){
        return view('masukdana');
    }

    public function masukdana(Request $request){
        $user = Pengguna::findOrFail(Auth::user()->id);
        $saldo = $user->saldo;
        $saldo += $request->input('nominal');
        $user->saldo += $saldo;
        $user->save();
        return redirect()->route('home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        return view('penggunas.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required',
            'email_pengguna' => 'required',
            'password_pengguna' => 'required',
        ]);

        Pengguna::create($request->all());

        return redirect()->route('penggunas.index');
    }

    public function deletePegawai(Request $request, $id){
        $pegawai = Pengguna::findOrFail($id);
        $pegawai->deleted_at = Carbon::now('Asia/Jakarta');
        $pegawai->save();

        return redirect()->route('pegawai.index',[$request->idvendor]);
    }

    public function undoDeletePegawai(Request $request){
        $pegawai = Pengguna::findOrFail($request->id);
        $pegawai->deleted_at = null;
        $pegawai->save();

        return redirect()->route('pegawai.index',[$request->idvendor]);
    }

    public function deletePengantar(Request $request){
        $pengantar = Pengguna::findOrFail($request->id);
        $pengantar->deleted_at = Carbon::now('Asia/Jakarta');
        $pengantar->save();

        return redirect()->route('pengantar.index',[$request->idvendor]);
    }

    public function undoDeletePengantar(Request $request){
        $pengantar = Pengguna::findOrFail($request->id);
        $pengantar->deleted_at = null;
        $pengantar->save();

        return redirect()->route('pengantar.index',[$request->idvendor]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pengguna $pengguna)
    {
        return view('penggunas.show', compact('pengguna'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pengguna $pengguna)
    {
        return view('penggunas.edit', compact('pengguna'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pengguna $pengguna)
    {
        $request->validate([
            'nama_pengguna' => 'required',
            'email_pengguna' => 'required',
            'password_pengguna' => 'required',
        ]);

        $pengguna->update($request->all());

        return redirect()->route('penggunas.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();
        return redirect()->route('penggunas.index');
    }
}
