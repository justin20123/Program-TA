<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function bukaregister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validasi = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'nomor_telepon' => 'required|string|regex:/^08[0-9]{6,}$/',
        ]);

        if($request->password != $request->confirm_password){
            return back()->with('error', 'Password tidak sama');
        }

        $pemesan = Pengguna::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pemesan',
            'nomor_telepon' => $request->nomor_telepon,
            'saldo' => 0
        ]);

        Auth::login($pemesan);

        return redirect()->route('home'); 
    }
}
