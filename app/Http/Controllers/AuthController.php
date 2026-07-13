<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Mengimpor Model User untuk keperluan registrasi dinamis

class AuthController extends Controller 
{
    /**
     * Menangani proses autentikasi masuk pengguna (Admin / Petugas).
     */
    public function login(Request $request) 
    {
        // 1. Validasi format input dari form login
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Percobaan autentikasi kredensial ke database
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk mencegah serangan Session Fixation
            $request->session()->regenerate();
            
            $user = Auth::user();

            // 3. Pengalihan halaman awal menuju Gerbang Absensi sesuai Hak Akses Instansi
            // Menggunakan strcasecmp agar pengecekan string kebal terhadap perbedaan huruf besar/kecil (Admin / admin)
            if (strcasecmp($user->role, 'Admin') === 0) {
                return redirect()
                    ->route('admin.dashboard') // 🚀 DIUBAH: Langsung ke dashboard admin, biarkan middleware internal yang mengecek status absen harian jika diperlukan
                    ->with('success', 'Otorisasi Berhasil. Selamat datang Admin pada repositori pusat SIP-O-SIBER.');
            }

            // Jika role bukan Admin (default ke Petugas/Operator)
            return redirect()
                ->route('petugas.attendance.form') // Diarahkan ke form absen Petugas lapangan
                ->with('success', 'Koneksi Terhubung. Selamat bekerja, Operator ' . $user->name . '. Harap lakukan presensi harian terlebih dahulu.');
        }

        // 4. Jika autentikasi gagal, kembalikan dengan pesan error kedinasan
        return back()
            ->withInput($request->only('username'))
            ->withErrors(['username' => 'Akses ditolak. Username atau password tidak terdaftar pada repositori pusat.']);
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
        // 1. Validasi input form registrasi mandiri
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|min:4|unique:users,username|alpha_dash',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' mencocokkan dengan input password_confirmation
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh petugas lain.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, strip, dan underscore tanpa spasi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal harus 6 karakter.'
        ]);

        // 2. Simpan data petugas baru ke database (Role otomatis dikunci sebagai 'Petugas')
        User::create([
            'name' => $request->name,
            'username' => strtolower($request->username), // Standarisasi username ke huruf kecil semua
            'password' => $request->password, // Otomatis di-hash oleh mutator setPasswordAttribute di model User.php
            'role' => 'Petugas', 
        ]);

        // 3. Alihkan ke gerbang login utama dengan pesan instruksi sukses
        return redirect()
            ->route('login')
            ->with('success', 'Registrasi berhasil! Akun Anda telah terdaftar dalam sistem SIP-O-SIBER. Silakan masuk.');
    }

    /**
     * Menangani proses keluar dari sistem dan pembersihan enkripsi session.
     */
    public function logout(Request $request) 
    {
        // Proses logout dari facade Auth Laravel
        Auth::logout();
        
        // Menghancurkan session aktif saat ini
        $request->session()->invalidate();
        
        // Regenerasi token CSRF baru demi keamanan jaringan pasca-logout
        $request->session()->regenerateToken();
        
        // Pengalihan kembali ke gerbang login utama
        return redirect('/login')->with('success', 'Sesi login instansi telah ditutup dengan aman.');
    }
}