<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\VendorHasPengguna;
use Carbon\Carbon;
use Database\Seeders\VendorHasPenggunaSeeder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    
    public function ceklogin()
    {
        if(!Auth::user()){ 
            return redirect()->route('login');
        }
    }


    public function indexPegawai($id_vendor){
        $pegawais = DB::table('penggunas')
        ->where('vendors_id', '=', $id_vendor)
        ->where('deleted_at', '=', null)
        ->where('role', '=', 'pegawai')
        ->select()
        ->get();

        $data_vendor = DB::table('vendors')
        ->where('id','=',$id_vendor)
        ->select('nama' , 'id')
        ->first();
        
        return view('pegawai.pegawai', compact('pegawais','data_vendor'));
    }

    public function indexPengantar($id_vendor){
        $pengantars = DB::table('penggunas')
        ->where('vendors_id', '=', $id_vendor)
        ->where('deleted_at', '=', null)
        ->where('role', '=', 'pengantar')
        ->select()
        ->get();

        $data_vendor = DB::table('vendors')
        ->where('id','=',$id_vendor)
        ->select('nama' , 'id')
        ->first();
        
        return view('pengantar.pengantar', compact('pengantars','data_vendor'));
    }

    public function createPegawai($id_vendor){
        $vendor = [$id_vendor];
        return view('pegawai.tambahpegawai',compact('vendor'));
    }

    public function createPengantar($id_vendor){
        $vendor = [$id_vendor];
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
            $pegawai->password = Hash::make($request->input('password'));
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
            $pengantar->password = Hash::make($request->input('password'));
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

    public function editPegawai($id_pegawai){
        $pegawai = DB::table('penggunas')
        ->where('id','=', $id_pegawai)
        ->where('vendors_id','=', Auth::user()->vendors_id)
        ->where('role','=', 'pegawai')
        ->first();

        return view('pegawai.editpegawai', compact('pegawai') );
    }

    public function updatePegawai(Request $request, $id_pegawai){
        try {
            $request->validate([
                'nama' => 'required|string|max:255', //nanti disesuaikan dgn db
                'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $pegawai = Pengguna::findOrFail($id_pegawai);
        $pegawai->nama = $request->input('nama');
        $pegawai->nomor_telepon = $request->input('nomor_telepon');
        $pegawai->save();

        return redirect()->route('pegawai.index',[$request->idvendor]);
    }

    public function editPengantar($id_pengantar){
        $pengantar = DB::table('penggunas')
        ->where('id','=', $id_pengantar)
        ->where('vendors_id','=', Auth::user()->vendors_id)
        ->where('role','=', 'pengantar')
        ->first();

        return view('pengantar.editpengantar', compact('pengantar') );
    }

    public function updatePengantar(Request $request, $id_pengantar){
        try {
            $request->validate([
                'nama' => 'required|string|max:255', //nanti disesuaikan dgn db
                'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $pengantar = Pengguna::findOrFail($id_pengantar);
        $pengantar->nama = $request->input('nama');
        $pengantar->nomor_telepon = $request->input('nomor_telepon');
        $pengantar->save();

        return redirect()->route('pengantar.index',[$request->idvendor]);
    }

    public function tarikdana(){
        return view('tarikdana');
    }
    public function dotarikdana(Request $request){
        
        if(!$request->input('nominal')){
            return back()->with("error", "Nominal harus terisi");
        }
        if(!$request->input('norek')){
            return back()->with("error", "Nomor Rekening harus terisi");
        }
        $nominal = $request->input('nominal');
        if($nominal>Auth::user()->saldo){
            return back()->with("error", "Saldo tidak cukup untuk melakukan penarikan");
        }
        if($nominal<1000){
            return back()->with("error", "Saldo minimal untuk ditarik adalah Rp. 1.000");
        }
        $user = Pengguna::find(Auth::user()->id);
        $saldobaru = $user->saldo - $nominal;
        $user->saldo = $saldobaru;
        $user->save();
        return redirect()->to('/');
    }

    public function create()
    {
        return view('penggunas.create');
    }

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

    public function show(Pengguna $pengguna)
    {
        return view('penggunas.show', compact('pengguna'));
    }

    public function edit(Pengguna $pengguna)
    {
        return view('penggunas.edit', compact('pengguna'));
    }

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

    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();
        return redirect()->route('penggunas.index');
    }
}
