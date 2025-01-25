<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Exception;
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
        

        if ($request->password != $request->confirmpassword) {
            return back()->withInput()->with('error', 'Password tidak sama');
        }

        $user = Pengguna::where('email', '=',$request->input('email'))->first();
        if($user){
            if($user->role == 'manajer'){
                return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar, silahkan login dengan email ini');
            }
            if($user->role == 'pegawai'){
                return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar sebagai pegawai, silahkan login dengan email ini');
            }
            else{
                return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar dengan hak akses lain, silahkan gunakan email lain atau login menggunakan aplikasi lainnya');
            }
        }

        $manajer = Pengguna::create([
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'manajer',
            'nomor_telepon' => $request->nomor_telepon,
            'saldo' => 0
        ]);

        Auth::login($manajer);

        return redirect()->route('home');
    }
}
