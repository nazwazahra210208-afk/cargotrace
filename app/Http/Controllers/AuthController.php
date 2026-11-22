<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan form login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses data login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Arahkan berdasarkan role setelah berhasil login
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                return redirect('/admin/dashboard')->with('success', 'Berhasil login sebagai Administrator');
            } elseif ($user->role === 'operator') {
                return redirect('/dashboard')->with('success', 'Berhasil login sebagai Operator');
            }

            return redirect('/track')->with('success', 'Berhasil login.');
        }

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // Memproses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}