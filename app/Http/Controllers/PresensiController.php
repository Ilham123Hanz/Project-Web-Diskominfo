<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class PresensiController extends Controller 
{
    /**
     * =========================================================================
     * 1. FITUR ADMINISTRATOR: REKAPITULASI PRESENSI SELURUH PETUGAS
     * =========================================================================
     */

    /**
     * Menampilkan rekap presensi seluruh petugas di halaman Panel Admin.
     * Dilengkapi Eager Loading (Anti N+1), Multi-Filter Dinamis, dan Statistik Ringkasan.
     */
    public function index(Request $request) 
    {
        try {
            // Eager Loading relasi user untuk optimasi query
            $query = Presensi::with('user')->orderBy('tanggal_presensi', 'desc');

            // 1. Filter Berdasarkan Pencarian Nama atau Username Petugas
            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%");
                });
            }

            // 2. Filter Rentang Tanggal Spesifik
            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_presensi', [$request->start_date, $request->end_date]);
            } elseif ($request->filled('start_date')) {
                $query->where('tanggal_presensi', '>=', $request->start_date);
            } elseif ($request->filled('end_date')) {
                $query->where('tanggal_presensi', '<=', $request->end_date);
            }

            // 3. Filter Status Kehadiran
            if ($request->filled('status_kehadiran')) {
                $query->where('status_kehadiran', $request->status_kehadiran);
            }

            // Hitung Statistik Ringkasan untuk Dashboard Admin (Single Query Optimization)
            $statsQuery = clone $query;
            $summaryStats = [
                'total_records'    => $statsQuery->count(),
                'total_hadir'      => (clone $statsQuery)->where('status_kehadiran', 'Hadir')->count(),
                'total_terlambat'  => (clone $statsQuery)->where('status_masuk', 'Terlambat')->count(),
                'total_izin_sakit' => (clone $statsQuery)->whereIn('status_kehadiran', ['Izin', 'Sakit'])->count(),
            ];

            // Eksekusi Paginasi dengan mempertahankan Query String Filter di URL
            $presensiList = $query->paginate(15)->withQueryString();

            return view('admin.check-attendance', compact('presensiList', 'summaryStats'));

        } catch (Exception $e) {
            Log::error('Gagal memuat rekap presensi di panel admin: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'Sistem gagal memuat data rekap presensi.');
        }
    }

    /**
     * =========================================================================
     * 2. FITUR OPERASIONAL PETUGAS/USER: FORMULIR & EKSEKUSI PRESENSI
     * =========================================================================
     */

    /**
     * Menampilkan Halaman Form Pengisian Presensi.
     * Dilengkapi Multi-Check View Fallback & Routing Dinamis Berdasarkan Role.
     */
    public function showCheckForm()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
            }

            $today = Carbon::today('Asia/Jakarta')->toDateString();

            // Mengambil record presensi user hari ini
            $presensiHariIni = Presensi::where('user_id', $user->id)
                ->where('tanggal_presensi', $today)
                ->first();

            $hasMasuk = false;
            $hasPulang = false;

            if ($presensiHariIni) {
                $hasMasuk = !empty($presensiHariIni->jam_masuk);
                $hasPulang = !empty($presensiHariIni->jam_pulang);
            }

            $userRole = strtolower($user->role ?? 'petugas');

            // View Redirection Bertingkat Fail-Safe
            if (in_array($userRole, ['admin', 'superadmin']) && view()->exists('admin.check-attendance')) {
                return view('admin.check-attendance', compact('hasMasuk', 'hasPulang', 'presensiHariIni'));
            }

            if (view()->exists('petugas.check-attendance')) {
                return view('petugas.check-attendance', compact('hasMasuk', 'hasPulang', 'presensiHariIni'));
            }

            if (view()->exists('petugas.attendance.form')) {
                return view('petugas.attendance.form', compact('hasMasuk', 'hasPulang', 'presensiHariIni'));
            }

            if (view()->exists('petugas.absensi-petugas')) {
                return view('petugas.absensi-petugas', compact('hasMasuk', 'hasPulang', 'presensiHariIni'));
            }

            // Fallback Redirection Berdasarkan Role User
            $fallbackRoute = in_array($userRole, ['admin', 'superadmin']) ? 'admin.dashboard' : 'petugas.dashboard';

            return redirect()->route($fallbackRoute)
                ->with('error', 'Formulir presensi belum tersedia (View Blade tidak ditemukan). Silakan hubungi admin.');

        } catch (Exception $e) {
            Log::error('Error saat memuat Form Presensi: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'line'    => $e->getLine()
            ]);

            $userRole = strtolower(Auth::user()->role ?? 'petugas');
            $fallbackRoute = in_array($userRole, ['admin', 'superadmin']) ? 'admin.dashboard' : 'petugas.dashboard';

            $debugMsg = config('app.debug') ? ' Error detail: ' . $e->getMessage() : '';

            return redirect()->route($fallbackRoute)->with('error', 'Terjadi kesalahan sistem saat memuat formulir presensi.' . $debugMsg);
        }
    }

    /**
     * Memproses Eksekusi Presensi Harian (Clock In / Jam Masuk ATAU Clock Out / Jam Pulang).
     * Terproteksi Database Transaction (Pessimistic Locking) & Dual Response Handler (HTML/JSON AJAX).
     */
    public function store(Request $request) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $todayDate = Carbon::today('Asia/Jakarta')->toDateString(); // YYYY-MM-DD Murni

        // ---------------------------------------------------------------------
        // MULTI-FALLBACK AUTO DETECTION UNTUK FIELD ATTENDANCE_INFO & NOTES
        // ---------------------------------------------------------------------
        $attendanceInfo = $request->input('attendance_info') 
            ?? $request->input('action_type') 
            ?? $request->input('status') 
            ?? $request->input('type') 
            ?? $request->input('mode');

        // Otomatisasi deteksi alur presensi jika parameter kosong
        if (empty($attendanceInfo)) {
            $existingToday = Presensi::where('user_id', $user->id)
                ->where('tanggal_presensi', $todayDate)
                ->first();

            if ($existingToday && $existingToday->jam_masuk && !$existingToday->jam_pulang) {
                $attendanceInfo = 'Pulang';
            } else {
                $attendanceInfo = 'Masuk';
            }

            $request->merge(['attendance_info' => $attendanceInfo]);
        }

        // Konsistensi Format Nilai ('Masuk' atau 'Pulang')
        $attendanceInfo = ucfirst(strtolower($attendanceInfo));
        if (in_array($attendanceInfo, ['Clockin', 'Clock_in', 'In', 'Masuk'])) {
            $attendanceInfo = 'Masuk';
        } elseif (in_array($attendanceInfo, ['Clockout', 'Clock_out', 'Out', 'Pulang'])) {
            $attendanceInfo = 'Pulang';
        }
        $request->merge(['attendance_info' => $attendanceInfo]);

        // Sanitasi Catatan / Notes
        $notes = $request->input('notes') 
            ?? $request->input('catatan') 
            ?? $request->input('keterangan') 
            ?? $request->input('catatan_masuk') 
            ?? $request->input('catatan_pulang') 
            ?? '';
        $cleanNotes = strip_tags(trim($notes));

        // 1. Validasi Input Form
        $validator = Validator::make($request->all(), [
            'attendance_info' => 'required|in:Masuk,Pulang',
            'manual_time'     => 'nullable|string', 
            'notes'           => 'nullable|string|max:500',
            'catatan'         => 'nullable|string|max:500'
        ], [
            'attendance_info.required' => 'Parameter tipe presensi wajib diisi (Masuk/Pulang).',
            'attendance_info.in'       => 'Parameter tipe presensi harus bernilai Masuk atau Pulang.'
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi Gagal: ' . implode(', ', $validator->errors()->all()),
                    'errors'  => $validator->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi Gagal: Format data catatan atau parameter presensi tidak valid.');
        }

        $userRole = strtolower($user->role ?? 'petugas');
        $targetDashboard = in_array($userRole, ['admin', 'superadmin']) ? 'admin.dashboard' : 'petugas.dashboard';

        // 2. PARSING & SANITASI WAKTU LOKAL (Pencegahan Bug Double Time Specification)
        try {
            $rawManualTime = $request->input('manual_time');

            if (!empty($rawManualTime)) {
                // Ekstrak bagian jam jika mengandung pemisah '|' atau 'WIB'
                if (strpos($rawManualTime, '|') !== false) {
                    $rawManualTime = explode('|', $rawManualTime)[1] ?? $rawManualTime;
                }
                $cleanTime = trim(str_replace(['WIB', 'wib', 'PM', 'AM'], '', $rawManualTime));

                // Normalisasi format HH:mm ke HH:mm:ss
                $timeParts = explode(':', $cleanTime);
                $hour   = str_pad($timeParts[0] ?? '00', 2, '0', STR_PAD_LEFT);
                $minute = str_pad($timeParts[1] ?? '00', 2, '0', STR_PAD_LEFT);
                $second = str_pad($timeParts[2] ?? '00', 2, '0', STR_PAD_LEFT);

                $timeString = "{$hour}:{$minute}:{$second}";
            } else {
                $timeString = Carbon::now('Asia/Jakarta')->format('H:i:s');
            }

            // Memastikan penentuan objek Carbon hanya menggunakan Tanggal Murni + Jam Murni
            $targetDateTime = Carbon::parse("{$todayDate} {$timeString}", 'Asia/Jakarta');

        } catch (Exception $e) {
            $targetDateTime = Carbon::now('Asia/Jakarta');
            $timeString = $targetDateTime->format('H:i:s');
        }

        // Ambil IP Address (Support Reverse Proxy / Cloudflare)
        $clientIp = $request->header('X-Forwarded-For') 
            ? trim(explode(',', $request->header('X-Forwarded-For'))[0]) 
            : $request->ip();

        // 3. DATABASE TRANSACTION (Pessimistic Locking / Lock For Update)
        DB::beginTransaction();
        try {
            $presensi = Presensi::where('user_id', $user->id)
                ->where('tanggal_presensi', $todayDate)
                ->lockForUpdate()
                ->first();

            // =========================================================================
            // ACTION A: PROSES PRESENSI MASUK (CLOCK IN)
            // =========================================================================
            if ($attendanceInfo === 'Masuk') {
                if ($presensi && $presensi->jam_masuk) {
                    DB::rollBack();
                    $msg = 'Sistem Menolak: Presensi masuk Anda hari ini sudah terdaftar.';
                    
                    return ($request->expectsJson() || $request->ajax())
                        ? response()->json(['success' => false, 'message' => $msg], 400)
                        : redirect()->route($targetDashboard)->with('error', $msg);
                }

                // Kalkulasi Keterlambatan Kedatangan
                $statusMasuk = method_exists(Presensi::class, 'checkIsLate') 
                    ? Presensi::checkIsLate($timeString) 
                    : ($timeString > '08:00:00' ? 'Terlambat' : 'Tepat Waktu');

                $newPresensi = Presensi::create([
                    'user_id'          => $user->id,
                    'tanggal_presensi' => $todayDate,
                    'jam_masuk'        => $timeString,
                    'status_masuk'     => $statusMasuk,
                    'status_kehadiran' => 'Hadir',
                    'verifikasi_admin' => 'Approved',
                    'metode_masuk'     => 'Web Portal',
                    'catatan_masuk'    => $cleanNotes,
                    'ip_address_masuk' => $clientIp,
                    'user_agent_masuk' => substr($request->userAgent() ?? '', 0, 500),
                ]);

                DB::commit();
                Log::info("User ID {$user->id} ({$user->name}) Berhasil Clock-In pada pukul {$timeString} WIB [Status: {$statusMasuk}]");

                $successMsg = "Berhasil mencatat presensi masuk. Status: {$statusMasuk}";
                return ($request->expectsJson() || $request->ajax())
                    ? response()->json(['success' => true, 'message' => $successMsg, 'data' => $newPresensi])
                    : redirect()->route($targetDashboard)->with('success', $successMsg);
            }

            // =========================================================================
            // ACTION B: PROSES PRESENSI PULANG (CLOCK OUT)
            // =========================================================================
            if ($attendanceInfo === 'Pulang') {
                if (!$presensi || !$presensi->jam_masuk) {
                    DB::rollBack();
                    $msg = 'Sistem Menolak: Modul Pulang terkunci sebelum presensi Masuk terverifikasi.';
                    
                    return ($request->expectsJson() || $request->ajax())
                        ? response()->json(['success' => false, 'message' => $msg], 400)
                        : redirect()->back()->with('error', $msg);
                }

                if ($presensi->jam_pulang) {
                    DB::rollBack();
                    $msg = 'Sistem Menolak: Anda sudah melakukan presensi pulang sebelumnya.';
                    
                    return ($request->expectsJson() || $request->ajax())
                        ? response()->json(['success' => false, 'message' => $msg], 400)
                        : redirect()->route($targetDashboard)->with('error', $msg);
                }

                // Kalkulasi Ketepatan Pulang
                $statusPulang = method_exists(Presensi::class, 'checkIsEarlyLeave') 
                    ? Presensi::checkIsEarlyLeave($timeString) 
                    : ($timeString < '17:00:00' ? 'Pulang Cepat' : 'Tepat Waktu');

                // PERBAIKAN BUG AMBIGU PARSING TANGGAL/JAM:
                // Ambil nilai tanggal_presensi sebagai string murni Y-m-d
                $cleanTanggal = Carbon::parse($presensi->tanggal_presensi)->toDateString();
                $cleanJamMasuk = trim($presensi->jam_masuk);
                
                // Jika jam_masuk menyertakan format datetime, bersihkan sehingga hanya H:i:s
                if (strlen($cleanJamMasuk) > 8) {
                    $cleanJamMasuk = Carbon::parse($cleanJamMasuk)->format('H:i:s');
                }

                // Buat objek Carbon jam masuk murni
                $jamMasukCarbon = Carbon::parse("{$cleanTanggal} {$cleanJamMasuk}", 'Asia/Jakarta');
                
                // Hitung Durasi Kerja (dalam Menit)
                $durasiKerjaMenit = $jamMasukCarbon->diffInMinutes($targetDateTime);

                $updateData = [
                    'jam_pulang'        => $timeString,
                    'status_pulang'     => $statusPulang,
                    'metode_pulang'     => 'Web Portal',
                    'catatan_pulang'    => $cleanNotes,
                    'ip_address_pulang' => $clientIp,
                    'user_agent_pulang' => substr($request->userAgent() ?? '', 0, 500),
                    'durasi_kerja'      => $durasiKerjaMenit,
                ];

                $presensi->update($updateData);

                DB::commit();
                Log::info("User ID {$user->id} ({$user->name}) Berhasil Clock-Out pada pukul {$timeString} WIB [Durasi: {$durasiKerjaMenit} mnt]");

                $successMsg = 'Berhasil mencatat presensi pulang. Terima kasih atas kerja keras Anda!';
                return ($request->expectsJson() || $request->ajax())
                    ? response()->json(['success' => true, 'message' => $successMsg, 'data' => $presensi])
                    : redirect()->route($targetDashboard)->with('success', $successMsg);
            }

            DB::rollBack();
            return redirect()->route($targetDashboard);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Kegagalan Kritikal Transaksi Database Presensi: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'request' => $request->all()
            ]);

            $errorMsg = 'Gagal memproses presensi. Terjadi kesalahan pada server database internal.';
            if (config('app.debug')) {
                $errorMsg .= ' [Debug: ' . $e->getMessage() . ']';
            }

            return ($request->expectsJson() || $request->ajax())
                ? response()->json(['success' => false, 'message' => $errorMsg], 500)
                : redirect()->back()->with('error', $errorMsg);
        }
    }

    /**
     * =========================================================================
     * 3. FITUR PETUGAS: RIWAYAT / LOG PRESENSI PRIBADI
     * =========================================================================
     */

    /**
     * Menampilkan riwayat/log presensi pribadi untuk Petugas Lapangan.
     */
    public function myAttendanceLog(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = Presensi::where('user_id', $user->id)
                ->orderBy('tanggal_presensi', 'desc');

            if ($request->filled('month')) {
                $query->whereMonth('tanggal_presensi', $request->month);
            }

            if ($request->filled('year')) {
                $query->whereYear('tanggal_presensi', $request->year);
            }

            $logs = $query->paginate(10)->withQueryString();

            // Cek file view yang tersedia
            if (view()->exists('petugas.attendance.log')) {
                return view('petugas.attendance.log', compact('logs'));
            }

            if (view()->exists('petugas.riwayat-absensi')) {
                return view('petugas.riwayat-absensi', compact('logs'));
            }

            return view('petugas.dashboard', compact('logs'));

        } catch (Exception $e) {
            Log::error('Gagal memuat log presensi petugas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat riwayat presensi pribadi.');
        }
    }
}