<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Attendance;
use Carbon\Carbon;

class CheckRole 
{
    /**
     * Menangani pemfilteran hak akses masuk rute (Role & Checkpoint Absensi).
     */
    public function handle(Request $request, Closure $next, $role): Response 
    {
        // 1. KONTROL OTORISASI DASAR: Pastikan pengguna sudah masuk sistem
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. PROTEKSI SILANG (CROSS-ROLE PROTECTION)
        // Jika role user tidak sesuai dengan kriteria rute yang diminta
        if (strcasecmp($user->role, $role) !== 0) {
            // Jika user adalah Admin tapi nyasar ke rute Petugas, arahkan paksa ke halaman Admin yang tepat
            if (strcasecmp($user->role, 'Admin') === 0) {
                return redirect()->route('admin.dashboard');
            }
            // Jika user adalah Petugas tapi mencoba membobol rute Admin, buang ke dashboard petugas
            return redirect()->route('petugas.dashboard')->with('error', 'Akses ditolak: Anda tidak memiliki otoritas Administrator Pusat.');
        }

        // 3. GATEKEEPER ABSENSI HARIAN GLOBAL
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // Cek log presensi masuk hari ini
        $hasMasuk = Attendance::where('user_id', $userId)
                              ->where('date', $today)
                              ->where('shift', 'Masuk')
                              ->exists();

        // Cek log presensi pulang sore hari ini
        $hasPulang = Attendance::where('user_id', $userId)
                               ->where('date', $today)
                               ->where('shift', 'Pulang')
                               ->exists();

        // Deteksi URL rute absensi secara presisi agar tidak terjadi infinite loop redirect
        $isAttendanceRoute = $request->is('*check-attendance*') || $request->is('*attendance*');

        // KONDISI A: Jika BELUM absen masuk hari ini dan mencoba mengakses halaman operasional/dashboard
        if (!$hasMasuk && !$isAttendanceRoute) {
            if (strcasecmp($user->role, 'Admin') === 0) {
                return redirect()->route('admin.attendance.form')->with('error', 'Otorisasi Kerja: Admin wajib mengisi log presensi masuk sistem.');
            }
            return redirect()->route('petugas.attendance.form')->with('error', 'Sistem Terkunci: Petugas lapangan wajib mengisi presensi harian terlebih dahulu.');
        }

        // KONDISI B: Jika SUDAH lengkap absen (Masuk & Pulang), dilarang kembali ke form absensi
        if ($hasMasuk && $hasPulang && $isAttendanceRoute) {
            if (strcasecmp($user->role, 'Admin') === 0) {
                return redirect()->route('admin.dashboard')->with('info', 'Sesi registrasi presensi Admin hari ini telah selesai.');
            }
            return redirect()->route('petugas.dashboard')->with('info', 'Sesi registrasi presensi harian Anda telah terpenuhi.');
        }

        return $next($request);
    }
}