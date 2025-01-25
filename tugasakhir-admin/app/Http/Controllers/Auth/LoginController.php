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


        if($user->role == 'admin'){
            if (Hash::check($password, $user->password)) {

                Auth::login($user);
                
                $request->session()->regenerate();
                
                return redirect()->route('home'); 
                
            }
            
        }
        else{
            return back()->with('error', 'Anda bukan admin, silahkan login menggunakan akun yang bersangkutan')->withInput($request->all());
        }


        return back()->with('error', 'Email atau password salah')->withInput($request->all());
    }

    // Handle logout request
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
