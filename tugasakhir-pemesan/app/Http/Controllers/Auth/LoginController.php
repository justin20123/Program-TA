<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function bukalogin()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:4',
            ]);
        } catch (Exception) {
            return back()->with('error', 'Password minimal 4 karakter')->withInput($request->all());
        }

        $email = $request->email;
        $password = $request->password;

        $user = Pengguna::where('email', '=',$email)->first();

        if(!$user){
            return back()->with('error', 'Akun anda belum terdaftar')->withInput();
        }

        if($user->role != 'pemesan'){
            return back()->with('error', 'Anda bukan pemesan, silahkan login menggunakan akun pemesan')->withInput($request->all());
        }


        if ($user) {
            if (Hash::check($password, $user->password)) {
                Auth::login($user);
                $request->session()->regenerate();
                return redirect()->route('home');
            }
        }

        return back()->with('error', 'Email atau password salah')->withInput($request->all());
    }

    // Handle logout request
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
