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
        if (!Auth::user()) {
            return redirect()->route('login');
        }
    }

    public function index()
    {
        $admins = DB::table('penggunas')
            ->where('role', '=', 'admin')
            ->where('deleted_at', '=', null)
            ->get();
        return view('admin.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tambah');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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

        $user = Pengguna::where('email', '=', $request->input('email'))->first();
        if ($user) {
            return redirect()->back()->withInput()->with('error', 'Email ini sudah terdaftar');
        }

        if ($request->input('password') == $request->input('confirmpassword')) {
            $admin = new Pengguna();
            $admin->nama = $request->input('nama');
            $admin->email = $request->input('email');
            $admin->password = Hash::make($request->input('password'));
            $admin->role = 'admin';
            $admin->saldo = 0;
            $admin->nomor_telepon = $request->input('nomor_telepon');
            $admin->vendors_id = Auth::user()->vendors_id;
            $admin->save();

            return redirect()->route('home');
        } else {
            return redirect()->back()->with('error', 'Password dan Confirm Password harus sama');
        }
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
    public function edit($idadmin)
    {
        $admin = DB::table('penggunas')
            ->where('id', '=', $idadmin)
            ->first();
        return view('admin.edit', compact('admin'));
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
        try {

            $request->validate([
                'nama' => 'required|string|max:255',
                'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
            ]);
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $idadmin = DB::table('penggunas')
            ->where('id', '=', $id)
            ->first()->id;

        $admin = Pengguna::findOrFail($idadmin);
        $admin->nama = $request->input('nama');
        $admin->nomor_telepon = $request->input('nomor_telepon');
        $admin->save();

        return redirect()->route('home');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->input('id');

        $admin = Pengguna::findOrFail($id);
        $admin->deleted_at = Carbon::now('Asia/Jakarta');
        $admin->save();

        return redirect()->route('home');
    }
}
