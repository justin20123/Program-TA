<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\VendorHasPengguna;
use Database\Seeders\VendorHasPenggunaSeeder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        ->where('vendors_id', '=', $idvendor)
        ->where('deleted_at', '=', null)
        ->where('role', '=', 'pegawai')
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
        ->where('vendors_id', '=', $idvendor)
        ->where('deleted_at', '=', null)
        ->where('role', '=', 'pengantar')
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
        try {

            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:4',
                'confirmpassword' => 'required|string|min:4',
                'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $user = Pengguna::where('email', '=',$request->input('email'))->first();
        if($user){
                return redirect()->back()->withInput()->with('error', 'Email ini sudah terdaftar');

        }
        if($request->password == $request->confirmpassword){
            $pegawai = new Pengguna();
            $pegawai->nama = $request->input('nama');
            $pegawai->email = $request->input('email');
            $pegawai->password = $request->input('password');
            $pegawai->role = 'pegawai';
            $pegawai->saldo = 0;
            $pegawai->nomor_telepon = $request->input('nomor_telepon');
            $pegawai->vendors_id = Auth::user()->vendors_id;
            $pegawai->save();
            
            return redirect()->route('pegawai.index',[$request->idvendor]);
        }
        else{
            return redirect()->back()->with('error', 'Password dan Confirm Password harus sama');
        }
        

    }

    public function storePengantar(Request $request){
        try {

            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:4',
                'confirmpassword' => 'required|string|min:4',
                'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $user = Pengguna::where('email', '=',$request->input('email'))->first();
        if($user){
                return redirect()->back()->withInput()->with('error', 'Email ini sudah terdaftar');

        }

        if($request->password == $request->confirmpassword){
            $pengantar = new Pengguna();
            $pengantar->nama = $request->input('nama');
            $pengantar->email = $request->input('email');
            $pengantar->password = $request->input('password');
            $pengantar->role = 'pengantar';
            $pengantar->saldo = 0;
            $pengantar->nomor_telepon = $request->input('nomor_telepon');
            $pengantar->vendors_id = Auth::user()->vendors_id;
            $pengantar->save();
            
            return redirect()->route('pengantar.index',[$request->idvendor]);
        }
        else{
            return redirect()->back()->with('error', 'Password dan Confirm Password harus sama');
        }
        

    }

    public function editPegawai($idpegawai){
        $pegawai = DB::table('penggunas')
        ->where('id','=', $idpegawai)
        ->where('vendors_id','=', Auth::user()->vendors_id)
        ->where('role','=', 'pegawai')
        ->first();

        return view('pegawai.editpegawai', compact('pegawai') );
    }

    public function updatePegawai(Request $request, $idpegawai){
        try {

            $request->validate([
                'nama' => 'required|string|max:255', //nanti disesuaikan dgn db
                'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $pegawai = Pengguna::findOrFail($idpegawai);
        $pegawai->nama = $request->input('nama');
        $pegawai->nomor_telepon = $request->input('nomor_telepon');
        $pegawai->save();

        return redirect()->route('pegawai.index',[$request->idvendor]);

    }

    public function editPengantar($idpengantar){
        $pengantar = DB::table('penggunas')
        ->where('id','=', $idpengantar)
        ->where('vendors_id','=', Auth::user()->vendors_id)
        ->where('role','=', 'pengantar')
        ->first();

        return view('pengantar.editpengantar', compact('pengantar') );
    }

    public function updatePengantar(Request $request, $idpengantar){
        try {

            $request->validate([
                'nama' => 'required|string|max:255', //nanti disesuaikan dgn db
                'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $pengantar = Pengguna::findOrFail($idpengantar);
        $pengantar->nama = $request->input('nama');
        $pengantar->nomor_telepon = $request->input('nomor_telepon');
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
        $pegawai->deleted_at = now();
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
        $pengantar->deleted_at = now();
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
