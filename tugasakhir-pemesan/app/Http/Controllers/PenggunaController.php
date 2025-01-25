<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\VendorHasPengguna;
use Carbon\Carbon;
use Database\Seeders\VendorHasPenggunaSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenggunaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $penggunas = Pengguna::all();
        return view('penggunas.index', compact('penggunas'));
    }

    public function indexPegawai($idvendor){
        $pegawais = DB::table('penggunas')
        ->join('vendors_has_penggunas', 'penggunas.email', '=', 'vendors_has_penggunas.penggunas_email')
        ->where('vendors_has_penggunas.vendors_id', '=', $idvendor)
        ->where('penggunas.deleted_at', '=', null)
        ->whereRaw('(penggunas.role = "manajer" OR penggunas.role = "pegawai")')
        ->select()
        ->get();

        $datavendor = DB::table('vendors')
        ->where('id','=',$idvendor)
        ->select('nama' , 'id')
        ->first();

        // dd($pegawais);
        
        return view('pegawai.pegawai', compact('pegawais','datavendor'));
    }

    public function indexPengantar($idvendor){
        $pengantars = DB::table('penggunas')
        ->join('vendors_has_penggunas', 'penggunas.email', '=', 'vendors_has_penggunas.penggunas_email')
        ->where('vendors_has_penggunas.vendors_id', '=', $idvendor)
        ->where('penggunas.deleted_at', '=', null)
        ->where('penggunas.role', '=', 'pengantar')
        ->select()
        ->get();

        $datavendor = DB::table('vendors')
        ->where('id','=',$idvendor)
        ->select('nama' , 'id')
        ->first();

        // dd($pengantars);
        
        return view('pengantar.pengantar', compact('pengantars','datavendor'));
    }

    public function createPegawai($idvendor){
        $vendor = [$idvendor];
        return view('pegawai.tambahpegawai',compact('vendor'));
    }

    public function createPengantar($idvendor){
        $vendor = [$idvendor];
        return view('pengantar.tambahpengantar',compact('vendor'));
    }

    public function storePegawai(Request $request){

        if($request->password == $request->confirmpassword){
            $pegawai = new Pengguna();
            $pegawai->nama = $request->nama;
            $pegawai->email = $request->email;
            $pegawai->password = $request->password;
            $pegawai->role = 'pegawai';
            $pegawai->saldo = 0;
            $pegawai->save();

            $vendors_has_penggunas = new VendorHasPengguna();
            $vendors_has_penggunas->vendors_id = $request->idvendor;
            $vendors_has_penggunas->penggunas_email = $pegawai->email;
            $vendors_has_penggunas->penggunas_id = $pegawai->id;
            $vendors_has_penggunas->save();
            
            return redirect()->route('pegawai.index',[$request->idvendor]);
        }
        else{
            return redirect()->back()->with('error', 'Password dan Confirm Password harus sama');
        }
        

    }

    public function storePengantar(Request $request){

        if($request->password == $request->confirmpassword){
            $pengantar = new Pengguna();
            $pengantar->nama = $request->nama;
            $pengantar->email = $request->email;
            $pengantar->password = $request->password;
            $pengantar->role = 'pengantar';
            $pengantar->saldo = 0;
            $pengantar->save();

            $vendors_has_penggunas = new VendorHasPengguna();
            $vendors_has_penggunas->vendors_id = $request->idvendor;
            $vendors_has_penggunas->penggunas_email = $pengantar->email;
            $vendors_has_penggunas->penggunas_id = $pengantar->id;
            $vendors_has_penggunas->save();
            
            return redirect()->route('pengantar.index',[$request->idvendor]);
        }
        else{
            return redirect()->back()->with('error', 'Password dan Confirm Password harus sama');
        }
        

    }

    public function editPegawai($idpegawai){
        $pegawai = DB::table('penggunas')
        ->where('id','=', $idpegawai)
        ->select()
        ->first();

        $idvendor = DB::table('vendors_has_penggunas')
        ->where('penggunas_id','=', $idpegawai)
        ->select('vendors_id')
        ->first();

        $vendor = [$idvendor->vendors_id];

        return view('pegawai.editpegawai', compact('pegawai', 'vendor') );
    }

    public function updatePegawai(Request $request, $idpegawai){
        $pegawai = Pengguna::findOrFail($idpegawai);
        $pegawai->nama = $request->nama;
        $pegawai->save();

        return redirect()->route('pegawai.index',[$request->idvendor]);
    }

    public function editPengantar($idpengantar){
        $pengantar = DB::table('penggunas')
        ->where('id','=', $idpengantar)
        ->select()
        ->first();

        $idvendor = DB::table('vendors_has_penggunas')
        ->where('penggunas_id','=', $idpengantar)
        ->select('vendors_id')
        ->first();

        $vendor = [$idvendor->vendors_id];
        return view('pengantar.editpengantar', compact('pengantar', 'vendor') );
    }

    public function updatePengantar(Request $request, $idpengantar){
        $pengantar = Pengguna::findOrFail($idpengantar);
        $pengantar->nama = $request->nama;
        $pengantar->save();

        return redirect()->route('pengantar.index',[$request->idvendor]);
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
