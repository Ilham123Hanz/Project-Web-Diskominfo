<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class AttendanceController extends Controller 
{
    /**
     * Menampilkan rekap absensi seluruh petugas di halaman Admin.
     * Dilengkapi eager loading, multi-filter dinamis, dan pencarian global.
     */
    public function index(Request $request) 
    {
        try {
            $query = Attendance::with('user')->orderBy('attendance_date', 'desc');

            // Filter Berdasarkan Pencarian Nama atau Username User
            if ($request->filled('search')) {
                $search = $request->search;
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }

            // Filter Rentang Tanggal Secara Spesifik
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('attendance_date', [$request->start_date, $request->end_date]);
            }

            // Filter Status Masuk (Tepat Waktu / Terlambat)
            if ($request->filled('status_in')) {
                $query->where('status_in', $request->status_in);
            }

            // Filter Status Pulang (Sesuai Jam / Pulang Awal)
            if ($request->filled('status_out')) {
                $query->where('status_out', $request->status_out);
            }

            // Eksekusi Paginasi dengan mempertahankan Query String Filter
            $attendances = $query->paginate(15)->withQueryString();

            // SINKRONISASI: Diarahkan ke view index absensi admin yang tepat
            return view('admin.check-attendance', compact('attendances'));
        } catch (Exception $e) {
            Log::error('Gagal memuat rekap absensi di panel admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Sistem Gagal memuat data log presensi.');
        }
    }

    /**
     * Menampilkan Halaman Form Pengisian Absen (Gatekeeper Checkpoint).
     * Mengunci akses jika alur registrasi presensi harian sudah lengkap.
     */
    public function showCheckForm()
    {
        try {
            $user = Auth::user();
            $today = Carbon::today()->toDateString();

            // Mengambil log absensi user hari ini dengan kunci pencarian optimal
            $attendance = Attendance::where('user_id', $user->id)
                                    ->where('attendance_date', $today)
                                    ->first();

            $hasMasuk = false;
            $hasPulang = false;

            if ($attendance) {
                $hasMasuk = !empty($attendance->clock_in);
                $hasPulang = !empty($attendance->clock_out);
            }

            // GERBANG PROTEKSI UTAMA: Jika sudah lengkap Clock In DAN Clock Out, lempar kembali ke dashboard
            if ($hasMasuk && $hasPulang) {
                $targetDashboard = (strcasecmp($user->role, 'Admin') === 0) ? 'admin.dashboard' : 'petugas.dashboard';
                return redirect()->route($targetDashboard)->with('info', 'Otorisasi Lengkap: Anda telah menyelesaikan siklus absensi hari ini.');
            }

            // 🚨 SAFE VIEW REDIRECTION: Jika file blade khusus admin belum siap/hilang, 
            // otomatis gunakan template milik petugas agar sistem tidak crash/looping.
            if (strcasecmp($user->role, 'Admin') === 0) {
                return view()->exists('admin.check-attendance') 
                    ? view('admin.check-attendance', compact('hasMasuk', 'hasPulang', 'attendance'))
                    : view('petugas.check-attendance', compact('hasMasuk', 'hasPulang', 'attendance'));
            }

            return view('petugas.check-attendance', compact('hasMasuk', 'hasPulang', 'attendance'));

        } catch (Exception $e) {
            Log::error('Error pada Checkpoint Absensi Form: ' . $e->getMessage());
            
            // 🚨 ANTI-LOOP BREAK: Hancurkan alur redirect ke /home jika terjadi error struktural
            abort(500, 'Kritikal Interseptor Absensi: ' . $e->getMessage());
        }
    }

    /**
     * Memproses Pengisian Absensi Harian (Clock In / Masuk atau Clock Out / Pulang).
     * Terproteksi Database Transaction & Fallback Fail-Safe.
     */
    public function store(Request $request) 
    {
        // 1. Validasi Input Form Tingkat Tinggi
        $validator = Validator::make($request->all(), [
            'attendance_info' => 'required|in:Masuk,Pulang',
            'manual_time'     => 'nullable|string', 
            'notes'           => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput()
                             ->with('error', 'Validasi Gagal: Format data catatan atau parameter tidak sesuai.');
        }

        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $targetDashboard = (strcasecmp($user->role, 'Admin') === 0) ? 'admin.dashboard' : 'petugas.dashboard';

        // 2. PARSING & SANITASI WAKTU: Ekstraksi waktu server aman
        try {
            if ($request->filled('manual_time') && strpos($request->manual_time, '|') !== false) {
                $timePart = explode('|', $request->manual_time)[1] ?? '';
                $cleanTime = trim(str_replace('WIB', '', $timePart));
                $targetTime = Carbon::createFromFormat('H:i', $cleanTime);
            } else {
                $targetTime = Carbon::now()->timezone('Asia/Jakarta');
            }
            $timeString = $targetTime->toTimeString(); 
        } catch (Exception $e) {
            $timeString = Carbon::now()->timezone('Asia/Jakarta')->toTimeString();
        }

        // Jalankan Database Transaction untuk mencegah Race Condition
        DB::beginTransaction();
        try {
            $attendance = Attendance::where('user_id', $user->id)
                                    ->where('attendance_date', $today)
                                    ->lockForUpdate()
                                    ->first();

            // =========================================================================
            // ACTION A: PROSES DATA ABSEN MASUK (CLOCK IN)
            // =========================================================================
            if ($request->attendance_info === 'Masuk') {
                if ($attendance && $attendance->clock_in) {
                    DB::rollBack();
                    return redirect()->route($targetDashboard)->with('error', 'Sistem Menolak: Token riwayat masuk Anda hari ini sudah terdaftar.');
                }

                $statusIn = method_exists(Attendance::class, 'checkIsLate') 
                            ? Attendance::checkIsLate($timeString) 
                            : ($timeString > '07:30:00' ? 'Terlambat' : 'Tepat Waktu');

                Attendance::create([
                    'user_id'         => $user->id,
                    'attendance_date' => $today,
                    'clock_in'        => $timeString,
                    'status_in'       => $statusIn,
                    'notes_in'        => strip_tags($request->notes),
                    'ip_address_in'   => $request->ip(),
                    'device_agent'    => substr($request->userAgent(), 0, 250),
                ]);

                DB::commit();
                Log::info("User ID {$user->id} ({$user->name}) Berhasil Clock-In pada pukul {$timeString}");
                return redirect()->route($targetDashboard)->with('success', 'Berhasil mencatat kehadiran masuk. Status: ' . $statusIn);
            }

            // =========================================================================
            // ACTION B: PROSES DATA ABSEN PULANG (CLOCK OUT)
            // =========================================================================
            if ($request->attendance_info === 'Pulang') {
                if (!$attendance || !$attendance->clock_in) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Sistem Menolak: Modul Pulang terkunci sebelum data Masuk terverifikasi.');
                }

                if ($attendance->clock_out) {
                    DB::rollBack();
                    return redirect()->route($targetDashboard)->with('error', 'Sistem Menolak: Anda sudah melakukan absen pulang sebelumnya.');
                }

                $statusOut = method_exists(Attendance::class, 'checkIsEarlyLeave') 
                             ? Attendance::checkIsEarlyLeave($timeString) 
                             : ($timeString < '16:00:00' ? 'Pulang Awal' : 'Selesai Kerja');

                $attendance->update([
                    'clock_out'      => $timeString,
                    'status_out'     => $statusOut,
                    'notes_out'      => strip_tags($request->notes),
                    'ip_address_out' => $request->ip(),
                ]);

                DB::commit();
                Log::info("User ID {$user->id} ({$user->name}) Berhasil Clock-Out pada pukul {$timeString}");
                return redirect()->route($targetDashboard)->with('success', 'Berhasil mencatat log kepulangan. Status: ' . $statusOut);
            }

            DB::rollBack();
            return redirect()->route($targetDashboard);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Kegagalan Kritikal Transaksi Database Absensi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses absensi. Terjadi kesalahan pada server basis data internal.');
        }
    }
}