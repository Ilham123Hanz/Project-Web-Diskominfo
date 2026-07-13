<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller 
{
    /**
     * Menampilkan rekap absensi seluruh petugas di halaman Admin.
     */
    public function index() 
    {
        $attendances = Attendance::with('user')->orderBy('date', 'desc')->get();
        return view('admin.attendances', compact('attendances'));
    }

    /**
     * Menampilkan Halaman Standalone Pengisian Absen (Gatekeeper Checkpoint)
     */
    public function showCheckForm()
    {
        $user = Auth::user();
        $userId = $user->id;
        $today = Carbon::today()->toDateString();

        // Cari tahu record absen pengguna hari ini
        $hasMasuk = Attendance::where('user_id', $userId)->where('date', $today)->where('shift', 'Masuk')->exists();
        $hasPulang = Attendance::where('user_id', $userId)->where('date', $today)->where('shift', 'Pulang')->exists();

        // SINKRONISASI GERBANG: Jika sudah melakukan KEDUA absen, kunci total ke dashboard masing-masing
        if ($hasMasuk && $hasPulang) {
            return $user->role === 'Admin' 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('petugas.dashboard');
        }

        // Jika sudah absen masuk dan mencoba mengakses form secara normal, arahkan langsung ke dashboard masing-masing
        if ($hasMasuk && !request()->has('action')) {
            return $user->role === 'Admin' 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('petugas.dashboard');
        }

        // PEMBAGIAN VIEW LAYOUT: Supaya Admin tidak melihat tampilan petugas magang
        if ($user->role === 'Admin') {
            return view('admin.check_attendance', compact('hasMasuk')); // Pastikan Anda membuat file blade ini di views/admin/
        }

        return view('petugas.check_attendance', compact('hasMasuk'));
    }

    /**
     * Memproses pengiriman Presensi Harian Pengguna secara MANUAL.
     */
    public function store(Request $request) 
    {
        // 1. VALIDASI: Wajib mengisi Jam Manual, Kategori Absen, dan Catatan Kegiatan Bebas
        $request->validate([
            'manual_time' => 'required', 
            'attendance_info' => 'required|in:Masuk,Pulang', 
            'notes' => 'required|string|max:500', 
        ], [
            'manual_time.required' => 'Jam presensi harus Anda isi secara manual.',
            'notes.required' => 'Kolom Rencana Kegiatan/Agenda Hari ini wajib diisi.',
        ]);

        $user = Auth::user();
        $userId = $user->id;
        $today = Carbon::today()->toDateString(); 
        
        // Tentukan target rute dashboard setelah aksi sukses
        $targetDashboard = $user->role === 'Admin' ? 'admin.dashboard' : 'petugas.dashboard';
        
        // Bersihkan data waktu untuk mencegah kesalahan format input
        $inputTime = Carbon::createFromFormat('H:i', $request->manual_time)->format('H:i:s'); 

        // 2. CEK DOUBLE ABSEN SINKRONISASI
        $alreadyAttended = Attendance::where('user_id', $userId)
                                     ->where('date', $today)
                                     ->where('shift', $request->attendance_info) 
                                     ->exists();

        if ($alreadyAttended) {
            return redirect()->route($targetDashboard)->with('error', 'Sistem Menolak: Anda sudah mengisi presensi kategori ' . $request->attendance_info . ' hari ini.');
        }

        // 3. LOGIKA EVALUASI JAM MANUAL SECARA STRIP & PRESISI
        $statusKehadiran = '';

        // --- ATURAN ABSEN MASUK ---
        if ($request->attendance_info === 'Masuk') {
            if ($inputTime >= '07:30:00' && $inputTime <= '08:00:00') {
                $statusKehadiran = 'Tepat Waktu';
            } 
            elseif ($inputTime > '08:00:00' && $inputTime <= '15:59:59') {
                $statusKehadiran = 'Terlambat';
            } 
            else {
                return redirect()->back()->with('error', 'Jam manual Kedatangan di luar rentang operasional masuk (07:30 - 15:59).');
            }
        }

        // --- ATURAN ABSEN PULANG ---
        if ($request->attendance_info === 'Pulang') {
            $hasMasuk = Attendance::where('user_id', $userId)->where('date', $today)->where('shift', 'Masuk')->exists();
            if (!$hasMasuk) {
                return redirect()->back()->with('error', 'Sistem Menolak: Anda tidak bisa mengisi jam Pulang sebelum melakukan Presensi Masuk.');
            }

            if ($inputTime >= '16:00:00' && $inputTime < '16:01:00') {
                $statusKehadiran = 'Pulang Tepat Waktu';
            } 
            elseif ($inputTime >= '16:01:00') {
                $statusKehadiran = 'Pulang Malam / Lembur';
            } 
            else {
                return redirect()->back()->with('error', 'Jam manual belum memasuki batas minimal jam pulang instansi (Minimal 16:00).');
            }
        }

        // 4. SIMPAN DATA MANUAL KE DATABASE
        Attendance::create([
            'user_id'   => $userId,
            'date'      => $today,
            'time_in'   => $inputTime, 
            'shift'     => $request->attendance_info, 
            'status'    => $statusKehadiran, // Menyimpan status hasil kalkulasi (Terlambat, dll)
            'notes'     => $request->notes, 
        ]);

        return redirect()->route($targetDashboard)->with('success', 'Presensi ' . $request->attendance_info . ' manual berhasil direkam dengan status: ' . $statusKehadiran);
    }
}