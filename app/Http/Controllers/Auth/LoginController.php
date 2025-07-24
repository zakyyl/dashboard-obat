<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session; // Untuk pesan flash

class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     * (Jika Anda ingin route GET /login menampilkan controller ini)
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani permintaan autentikasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        // 1. Validasi Input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba Autentikasi
        if (Auth::attempt($credentials)) {
            // Regenerate session untuk mencegah session fixation attacks
            $request->session()->regenerate();

            // Redirect ke halaman yang dituju setelah login sukses
            return redirect()->intended('/home'); // 'intended' akan mengarahkan kembali ke URL yang ingin diakses sebelum login
        }

        // 3. Jika Autentikasi Gagal
        // Redirect kembali ke form login dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email'); // Hanya simpan input email agar tidak perlu mengetik ulang password
    }

    /**
     * Menangani permintaan logout.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate(); // Hapus semua data sesi
        $request->session()->regenerateToken(); // Regenerate token CSRF

        // Redirect ke halaman login atau halaman utama
        return redirect('/');
    }
}