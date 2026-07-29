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

            // 2. Filter Tanggal Spesifik (Mendukung parameter 'date' dari form/picker UI Admin)
            if ($request->filled('date')) {
                $query->whereDate('tanggal_presensi', $request->date);
            } 
            // Filter Rentang Tanggal Spesifik
            elseif ($request->filled('start_date') && $request->filled('end_date')) {
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
            $attendances = $query->paginate(15)->withQueryString();

            return view('admin.absensi', compact('attendances', 'summaryStats'));

        } catch (Exception $e) {
            Log::error('Gagal memuat rekap presensi di panel admin: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'Sistem gagal memuat data rekap presensi.');
        }
    }
    
   public function exportCsv(Request $request)
    {
        $query = Presensi::with('user')->orderBy('tanggal_presensi', 'desc');

        // Menangkap parameter filter tanggal atau bulan
        $filterTanggal = $request->input('date') ?? $request->input('tanggal') ?? $request->input('start_date');
        $filterBulan   = $request->input('month') ?? $request->input('bulan');
        $filterTahun   = $request->input('year') ?? $request->input('tahun') ?? date('Y');

        if (!empty($filterTanggal)) {
            $query->whereDate('tanggal_presensi', $filterTanggal);
            $fileName = 'rekap-absensi-tanggal-' . $filterTanggal . '.csv';
        } elseif (!empty($filterBulan)) {
            $query->whereMonth('tanggal_presensi', $filterBulan)
                  ->whereYear('tanggal_presensi', $filterTahun);
            $fileName = 'rekap-absensi-bulan-' . $filterBulan . '-' . $filterTahun . '.csv';
        } else {
            $fileName = 'rekap-absensi-semua.csv';
        }

        if ($request->filled('status_kehadiran')) {
            $query->where('status_kehadiran', $request->status_kehadiran);
        }

        $attendances = $query->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // Menggunakan titik koma (;) agar otomatis terpisah ke kolom-kolom Excel
            fputcsv($file, ['NO', 'NAMA PETUGAS PATROLI', 'SHIFT PENUGASAN', 'TANGGAL PRESENSI', 'WAKTU KEHADIRAN', 'STATUS KEHADIRAN'], ';');

            foreach ($attendances as $index => $item) {
                $tanggalFormat = $item->tanggal_presensi ? Carbon::parse($item->tanggal_presensi)->format('d-m-Y') : '-';

                fputcsv($file, [
                    $index + 1,
                    $item->user->name ?? '-',
                    $item->shift ?? 'Shift Pagi',
                    $tanggalFormat,
                    $item->jam_masuk ? $item->jam_masuk . ' WIB' : '-',
                    $item->status_kehadiran ?? 'Hadir'
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * =========================================================================
     * 2. FITUR OPERASIONAL PETUGAS/USER: FORMULIR & EKSEKUSI PRESENSI
     * =========================================================================
     */

    /**
     * Menampilkan Halaman Form Pengisian Presensi.
     */
    public function showCheckForm()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Sesi Anda telah berakhir, silakan login kembali.');
            }

            $today = Carbon::today('Asia/Jakarta')->toDateString();

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
     * Memproses Eksekusi Presensi Harian (Clock In / Clock Out).
     */
    public function store(Request $request) 
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $todayDate = Carbon::today('Asia/Jakarta')->toDateString();

        $attendanceInfo = $request->input('attendance_info') 
            ?? $request->input('action_type') 
            ?? $request->input('status') 
            ?? $request->input('type') 
            ?? $request->input('mode');

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

        $attendanceInfo = ucfirst(strtolower($attendanceInfo));
        if (in_array($attendanceInfo, ['Clockin', 'Clock_in', 'In', 'Masuk'])) {
            $attendanceInfo = 'Masuk';
        } elseif (in_array($attendanceInfo, ['Clockout', 'Clock_out', 'Out', 'Pulang'])) {
            $attendanceInfo = 'Pulang';
        }
        $request->merge(['attendance_info' => $attendanceInfo]);

        $notes = $request->input('notes') 
            ?? $request->input('catatan') 
            ?? $request->input('keterangan') 
            ?? $request->input('catatan_masuk') 
            ?? $request->input('catatan_pulang') 
            ?? '';
        $cleanNotes = strip_tags(trim($notes));

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

        try {
            $rawManualTime = $request->input('manual_time');

            if (!empty($rawManualTime)) {
                if (strpos($rawManualTime, '|') !== false) {
                    $rawManualTime = explode('|', $rawManualTime)[1] ?? $rawManualTime;
                }
                $cleanTime = trim(str_replace(['WIB', 'wib', 'PM', 'AM'], '', $rawManualTime));

                $timeParts = explode(':', $cleanTime);
                $hour   = str_pad($timeParts[0] ?? '00', 2, '0', STR_PAD_LEFT);
                $minute = str_pad($timeParts[1] ?? '00', 2, '0', STR_PAD_LEFT);
                $second = str_pad($timeParts[2] ?? '00', 2, '0', STR_PAD_LEFT);

                $timeString = "{$hour}:{$minute}:{$second}";
            } else {
                $timeString = Carbon::now('Asia/Jakarta')->format('H:i:s');
            }

            $targetDateTime = Carbon::parse("{$todayDate} {$timeString}", 'Asia/Jakarta');

        } catch (Exception $e) {
            $targetDateTime = Carbon::now('Asia/Jakarta');
            $timeString = $targetDateTime->format('H:i:s');
        }

        $clientIp = $request->header('X-Forwarded-For') 
            ? trim(explode(',', $request->header('X-Forwarded-For'))[0]) 
            : $request->ip();

        DB::beginTransaction();
        try {
            $presensi = Presensi::where('user_id', $user->id)
                ->where('tanggal_presensi', $todayDate)
                ->lockForUpdate()
                ->first();

            // ACTION A: PROSES PRESENSI MASUK
            if ($attendanceInfo === 'Masuk') {
                if ($presensi && $presensi->jam_masuk) {
                    DB::rollBack();
                    $msg = 'Sistem Menolak: Presensi masuk Anda hari ini sudah terdaftar.';
                    
                    return ($request->expectsJson() || $request->ajax())
                        ? response()->json(['success' => false, 'message' => $msg], 400)
                        : redirect()->route($targetDashboard)->with('error', $msg);
                }

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

            // ACTION B: PROSES PRESENSI PULANG
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

                $statusPulang = method_exists(Presensi::class, 'checkIsEarlyLeave') 
                    ? Presensi::checkIsEarlyLeave($timeString) 
                    : ($timeString < '17:00:00' ? 'Pulang Cepat' : 'Tepat Waktu');

                $cleanTanggal = Carbon::parse($presensi->tanggal_presensi)->toDateString();
                $cleanJamMasuk = trim($presensi->jam_masuk);
                
                if (strlen($cleanJamMasuk) > 8) {
                    $cleanJamMasuk = Carbon::parse($cleanJamMasuk)->format('H:i:s');
                }

                $jamMasukCarbon = Carbon::parse("{$cleanTanggal} {$cleanJamMasuk}", 'Asia/Jakarta');
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

            if (view()->exists('petugas.attendance.log')) {
                return view('petugas.attendance.log', compact('logs'));
            }

            if (view()->exists('petugas.riwayat-absensi')) {
                return view('petugas.riwayat-absensi', compact('logs'));
            }

            return view('petugas.dashboard-petugas', compact('logs'));

        } catch (Exception $e) {
            Log::error('Gagal memuat log presensi petugas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat riwayat presensi pribadi.');
        }
    }
}