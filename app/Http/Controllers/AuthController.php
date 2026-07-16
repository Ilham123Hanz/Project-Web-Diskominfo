<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller 
{
    /**
     * TAMPILAN BARU: Menampilkan halaman Form Login Sistem SIP-O-SIBER.
     * Mencegah crash internal akibat ketidaktersediaan method GET handler.
     */
    public function showLoginForm()
    {
        // Memastikan sistem mengembalikan visual dari resources/views/auth/login.blade.php
        return view('auth.login');
    }

    /**
     * Menangani proses autentikasi masuk pengguna (Admin / Petugas).
     */
    public function login(Request $request) 
    {
        $credentials = $request->validate([
            'username' => 'required|string', 
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            
            $request->session()->regenerate();
            
            $user = Auth::user();

            if (strcasecmp($user->role, 'Admin') === 0) {
                return redirect()
                    ->route('admin.dashboard')
                    ->with('success', 'Otorisasi Berhasil. Selamat datang Admin pada repositori pusat SIP-O-SIBER.');
            }

            return redirect()
                ->route('petugas.dashboard')
                ->with('success', 'Koneksi Terhubung. Selamat bekerja, Operator ' . $user->name . '.');
        }

        return back()
            ->withInput($request->only('username'))
            ->withErrors([
                'login_error' => 'Akses ditolak. NPM atau kata sandi tidak cocok dengan repositori pusat.'
            ]);
    }

    /**
     * Menampilkan halaman Form Registrasi Mandiri untuk Petugas Baru.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Memproses pendaftaran akun petugas baru secara dinamis berdasarkan input user.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|numeric|unique:users,username', 
            'email'    => 'required|email|unique:users,email',      
            'password' => 'required|string|min:6|confirmed',        
        ], [
            'username.required'  => 'NPM wajib diisi.',
            'username.numeric'   => 'NPM hanya boleh berisi angka.',
            'username.unique'    => 'NPM ini sudah terdaftar di sistem. Gunakan NPM lain.',
            'email.required'     => 'Email kontak wajib diisi.',
            'email.email'        => 'Format penulisan email tidak valid.',
            'email.unique'       => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal harus 6 karakter.'
        ]);
        
        User::create([
            'name'     => $request->name,
            'username' => $request->username, 
            'email'    => $request->email,    
            'password' => $request->password, 
            'role'     => 'Petugas', 
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Data NPM dan Akun Anda telah tersimpan. Silakan masuk.');
    }

    /**
     * Menangani proses keluar dari sistem dan pembersihan enkripsi session.
     */
    public function logout(Request $request) 
    {
        Auth::logout();
        
        $request->session()->invalidate();
        
        $request->session()->regenerateToken();
        
        return redirect('/login')->with('success', 'Sesi login instansi telah ditutup dengan aman.');
    }
}