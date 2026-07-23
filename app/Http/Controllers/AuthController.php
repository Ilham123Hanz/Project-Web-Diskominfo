<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller 
{
    /**
     * Menampilkan halaman Form Login Sistem SIP-O-SIBER.
     */
    public function showLoginForm()
    {
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
        ], [
            'username.required' => 'Nama pengguna / NPM / NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.'
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
                'login_error' => 'Akses ditolak. Nama Pengguna/NPM/NIP atau kata sandi tidak cocok.'
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
     * Memproses pendaftaran akun petugas baru secara dinamis.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username', 
            'email'    => 'required|email|unique:users,email',      
            'password' => 'required|string|min:8|confirmed',        
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'username.required'  => 'Nama pengguna (NPM/NIP) wajib diisi.',
            'username.unique'    => 'Nama pengguna (NPM/NIP) ini sudah terdaftar di sistem.',
            'email.required'     => 'Email kontak wajib diisi.',
            'email.email'        => 'Format penulisan email tidak valid.',
            'email.unique'       => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min'       => 'Kata sandi minimal harus 8 karakter.'
        ]);

        DB::transaction(function () use ($request) {
            User::create([
                'name'     => $request->name,
                'username' => $request->username, 
                'email'    => $request->email,    
                'password' => Hash::make($request->password), 
                'role'     => 'Petugas', 
            ]);
        });

        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Akun Anda telah terdaftar. Silakan masuk ke sistem.');
    }

    /**
     * Menampilkan halaman Form Lupa / Reset Password.
     * Dipanggil oleh route 'password.request'
     */
    public function showForgotPasswordForm()
    {
        return view('auth.lupa-password'); 
    }

    /**
     * Memproses reset password langsung di tempat (Direct Update).
     * Menerima input 'username' (bisa berupa Username/NPM/NIP atau Email) dan 'password' baru.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validasi Input Form
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'username.required'  => 'Nama Pengguna (Username/NPM/NIP) wajib diisi.',
            'password.required'  => 'Kata sandi baru wajib diisi.',
            'password.min'       => 'Kata sandi baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        try {
            // 2. Cari User berdasarkan Username atau Email
            $user = User::where('username', $request->username)
                        ->orWhere('email', $request->username)
                        ->first();

            if (!$user) {
                return back()
                    ->withInput($request->only('username'))
                    ->withErrors([
                        'username' => 'Data petugas tidak ditemukan di pangkalan data CSIRT.'
                    ]);
            }

            // 3. Update Password menggunakan Transaksi Database
            DB::transaction(function () use ($user, $request) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            });

            // 4. Redirect ke Login dengan Pesan Sukses
            return redirect()
                ->route('login')
                ->with('success', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');

        } catch (\Exception $e) {
            Log::error('Reset Password Error: ' . $e->getMessage());

            return back()
                ->withInput($request->only('username'))
                ->withErrors([
                    'system_error' => 'Gagal memperbarui kata sandi. Terjadi kesalahan pada sistem CSIRT.'
                ]);
        }
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