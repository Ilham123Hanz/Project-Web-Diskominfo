<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class EnsureClockedIn
{
    /**
     * Memastikan pengguna telah melakukan absensi masuk sebelum diizinkan mengakses modul operasional.
     * Teroptimasi penuh untuk arsitektur multi-role (Anti-Redirect Loop).
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. FAIL-SAFE: Jika user belum login, biarkan middleware 'auth' global yang menanganinya
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // 2. Kueri status kehadiran hari ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('attendance_date', $today)
                                ->first();

        // 3. EVALUASI BLOCKING GAUNTLET (Jika belum melakukan Clock In)
        if (!$attendance || empty($attendance->clock_in)) {
            
            // Proteksi untuk request berbasis AJAX / API / Fetch data backend
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'lockout',
                    'message' => 'Akses ditolak. Token absensi masuk harian belum terdaftar.'
                ], 403);
            }

            Log::notice("Akses Modul Ditangguhkan: User ID {$user->id} ({$user->name}) mencoba mengakses area kerja sebelum melakukan presensi masuk.");

            // 🚨 INTELLIGENT ROUTING REDIRECTION (Pemisah Rute Berdasarkan Role Aktual)
            if (strcasecmp($user->role, 'Admin') === 0) {
                return redirect()->route('admin.attendance.form')
                                 ->with('error', 'Otorisasi Sistem: Administrator wajib mengisi log presensi masuk terlebih dahulu.');
            }

            // Default fallback untuk user Petugas Lapangan
            return redirect()->route('petugas.attendance.form')
                             ->with('error', 'Sistem Terkunci: Anda wajib melakukan registrasi presensi masuk (Clock In) hari ini.');
        }

        // Lolos validasi, lanjutkan ke controller
        return $next($request);
    }
}