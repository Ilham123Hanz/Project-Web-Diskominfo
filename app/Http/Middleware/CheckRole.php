<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

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
        $userRole = trim($user->role ?? '');

        // 2. PROTEKSI SILANG & STRATIFIKASI ROLE (CROSS-ROLE PROTECTION)
        // Menggunakan perbandingan string case-insensitive yang aman dari manipulasi input character
        if (strcasecmp($userRole, trim($role)) !== 0) {
            Log::warning("Akses Ilegal Terblokir: User ID {$user->id} dengan Role '{$userRole}' mencoba mengakses area khusus '{$role}' pada URL: " . $request->fullUrl());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Forbidden Access.'], 403);
            }

            // --- FASE PENCEGAHAN INFINITE REDIRECT LOOP ---
            // Dapatkan nama rute saat ini untuk mencegah pengalihan ke rute yang sama
            $currentRoute = $request->route() ? $request->route()->getName() : '';

            // Pengalihan cerdas langsung ke Dashboard Utama operasional role masing-masing
            if (strcasecmp($userRole, 'Admin') === 0) {
                if ($currentRoute === 'admin.dashboard') {
                    abort(403, 'Akses Ditolak: Batas wewenang area terlampaui.');
                }
                return redirect()->route('admin.dashboard')
                    ->with('error', 'Proteksi Akses: Anda dialihkan ke gerbang kontrol Admin.');
            }
            
            if (strcasecmp($userRole, 'Petugas') === 0) {
                if ($currentRoute === 'petugas.dashboard') {
                    abort(403, 'Akses Ditolak: Batas wewenang area terlampaui.');
                }
                return redirect()->route('petugas.dashboard')
                    ->with('error', 'Akses Ditolak: Hak akses dibatasi hanya untuk Administrator Pusat.');
            }

            // Jika role tidak dikenal di sistem siber, paksa hancurkan sesi login demi keamanan
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('error', 'Kredensial Rusak: Token otoritas role tidak valid.');
        }

        // Jalankan request ke lapisan proses berikutnya jika seluruh otentikasi identitas valid
        return $next($request);
    }
}