<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AkunGuru;
use Illuminate\Support\Facades\Hash;

class GuruAuthController extends Controller
{
    // Menampilkan halaman login
    public function showLoginForm()
    {
        return view('guru.login');
    }

    // Proses login guru
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('guru')->attempt(['username' => $request->username, 'password' => $request->password])) {
            return redirect()->route('guru.dashboard')->with('success', 'Login berhasil!');
        }

        return back()->with('error', 'Username atau password salah.');
    }

    // Logout guru
    public function logout()
    {
        Auth::guard('guru')->logout();
        return redirect()->route('guru.login')->with('success', 'Logout berhasil.');
    }
}
