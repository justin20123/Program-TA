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
    public function tambahappkey(Request $request){
        if($request->input('appkey') == "5add2ba41013d02cdd63acf1d7e9c8865ca9607403d7844184d127dd46f0a7e8"){
            session(['appkey' => $request->input('appkey')]);
            return redirect()->route('register');
        }
        else{
            return back()->with('error', 'Kunci aplikasi salah');
        }
    }
    public function bukaregister()
    {

        if(session()->has('appkey') && session('appkey') == "5add2ba41013d02cdd63acf1d7e9c8865ca9607403d7844184d127dd46f0a7e8"){
            return view('auth.register');
        }
        else{
            return redirect()->route('login')->with('error', 'Kunci aplikasi belum ada');
        }
        
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
            ], [
                'nama.required' => 'Nama harus diisi.',
                'nama.string' => 'Nama harus berupa string.',
                'nama.max' => 'Nama tidak boleh lebih dari 255 karakter.',
                'email.required' => 'Email harus diisi.',
                'email.string' => 'Email harus berupa string.',
                'email.email' => 'Email tidak valid.',
                'email.max' => 'Email tidak boleh lebih dari 255 karakter.',
                'email.unique' => 'Email sudah terdaftar, silahkan gunakan email lain.',
                'password.required' => 'Password harus diisi.',
                'password.string' => 'Password harus berupa string.',
                'password.min' => 'Password harus memiliki minimal 4 karakter.',
                'confirmpassword.required' => 'Konfirmasi password harus diisi.',
                'confirmpassword.string' => 'Konfirmasi password harus berupa string.',
                'confirmpassword.min' => 'Konfirmasi password harus memiliki minimal 4 karakter.',
                'nomor_telepon.required' => 'Nomor telepon harus diisi.',
                'nomor_telepon.string' => 'Nomor telepon harus berupa string.',
                'nomor_telepon.regex' => 'Nomor telepon tidak valid.',
            ],);

        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        
        

        if ($request->password != $request->confirmpassword) {
            return back()->withInput()->with('error', 'Password tidak sama');
        }

        $user = Pengguna::where('email', '=',$request->input('email'))->first();
        if($user){
            return redirect()->back()->withInput()->with('error', 'Email sudah terdaftar, silahkan gunakan email lain');
            
        }

        $owner = Pengguna::create([
            'nama' => $request->input('nama'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => 'owner',
            'nomor_telepon' => $request->input('nomor_telepon'),
            'saldo' => 0
        ]);

        Auth::login($owner);

        return redirect()->route('home');
    }
}
