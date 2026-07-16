<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Log;

class CheckRole 
{
    /**
     * Menangani pemfilteran hak akses masuk rute berdasarkan Tipe Otoritas (Role-Based Access Control).
     * Teroptimasi penuh: Menghilangkan pengecekan absensi ganda untuk mencegah putaran redirect (Looping Protection).
     */
    public function handle(Request $request, Closure $next, string $role): Response 
    {
        // 1. KONTROL OTORISASI DASAR: Pastikan pengguna sudah terautentikasi (Logged In)
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login')->with('error', 'Sesi Berakhir: Silakan masuk kembali ke sistem.');
        }

        $user = Auth::user();

        // 2. PROTEKSI SILANG & STRATIFIKASI ROLE (CROSS-ROLE PROTECTION)
        // Menggunakan perbandingan string case-insensitive yang aman dari manipulasi input character
        if (strcasecmp($user->role, $role) !== 0) {
            Log::warning("Akses Ilegal Terblokir: User ID {$user->id} dengan Role '{$user->role}' mencoba mengakses area khusus '{$role}' pada URL: " . $request->fullUrl());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden Access.'], 403);
            }

            // Pengalihan cerdas sesuai hak operasional aktual user
            if (strcasecmp($user->role, 'Admin') === 0) {
                return redirect()->route('admin.attendance.form')->with('error', 'Proteksi Akses: Anda dialihkan ke gerbang kontrol Admin.');
            }
            
            if (strcasecmp($user->role, 'Petugas') === 0) {
                return redirect()->route('petugas.attendance.form')->with('error', 'Akses Ditolak: Hak akses dibatasi hanya untuk Administrator Pusat.');
            }

            // Jika role tidak dikenal di sistem siber, paksa hancurkan sesi login
            Auth::logout();
            return redirect()->route('login')->with('error', 'Kredensial Rusak: Token otoritas tidak valid.');
        }

        // Jalankan request ke lapisan proses berikutnya jika seluruh otentikasi identitas valid
        return $next($request);
    }
}