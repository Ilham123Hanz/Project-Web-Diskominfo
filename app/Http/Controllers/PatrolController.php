<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patrol;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PatrolController extends Controller 
{
    /**
     * Menampilkan Halaman Dashboard Utama Petugas Lapangan + Kontrol Wizard + Metriks Dinamis
     */
    public function index(Request $request) 
     {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // 1. PENGOPTIMALAN QUERY ABSENSI HARI INI
        $todayAttendance = Attendance::where('user_id', $user->id)
                                    ->where('attendance_date', $today)
                                    ->first();

        $hasPulang = ($todayAttendance && $todayAttendance->clock_out) ? true : false;

        // 2. KALKULASI QUICK METRICS SECARA AGREGAT DENGAN HANDSHAKE STATUS TERBARU
        $metrics = [
            'total_hari_ini' => Patrol::where('user_id', $user->id)->whereDate('created_at', $today)->count(),
            'pending'        => Patrol::where('user_id', $user->id)->whereIn('status', ['Pending', 'Menunggu Validasi'])->count(),
            'verified'       => Patrol::where('user_id', $user->id)->whereIn('status', ['Verified', 'Approved', 'Disetujui Admin'])->count(),
        ];

        // 3. FITUR STATUS ALERT TERKINI: Deteksi Kasus Perlu Tindakan Koreksi Segera
        $latestCorrection = Patrol::where('user_id', $user->id)
                                  ->whereIn('status', ['Perlu Perbaikan', 'Rejection'])
                                  ->orderBy('updated_at', 'desc')
                                  ->first();

        // 4. FILTERING, FOLDERISASI, & PENCARIAN ADVANCED
        $search = $request->input('search');
        $folder = $request->input('folder'); 
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // Proteksi SQL Injection untuk Sorting Column
        $allowedSort = ['created_at', 'opd_sasaran', 'kategori_insiden', 'status'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $query = Patrol::where('user_id', $user->id);
        
        if ($folder) { 
            $query->whereYear('created_at', $folder); 
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('opd_sasaran', 'like', "%{$search}%")
                  ->orWhere('kategori_insiden', 'like', "%{$search}%")
                  ->orWhere('id_log', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Tampilan Terbatas untuk Widget Dashboard Utama (5 Log Terbaru)
        $patrols = $query->orderBy($sortBy, $sortOrder)->paginate(5)->withQueryString();

        // 5. MASTER DATA KLASTER (Mendukung Dropdown Wizard Form)
        $listOPD = [
            'Dinas Komunikasi, Informatika dan Statistik', 
            'Badan Kepegawaian Daerah', 
            'Badan Pengelolaan Keuangan dan Aset Daerah', 
            'Dinas Kesehatan', 
            'Dinas Pendidikan dan Kebudayaan',
            'BAPPEDA Lampung',
            'Bapenda'
        ];
        
        $listKategori = [
            'Web Defacement / Peretasan Situs', 
            'Judi Online (Judol)', 
            'Malware / Ransomware Infection', 
            'Phishing Page / Social Engineering', 
            'DDoS Attack / Kelumpuhan Jaringan'
        ];

        return view('petugas.dashboard', compact(
            'todayAttendance', 'hasPulang', 'patrols', 
            'listOPD', 'listKategori', 'metrics', 'latestCorrection'
        ));
    }

    /**
     * Menampilkan Form Entri Log Laporan Patroli Siber Standalone
     */
    public function create()
    {
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
                                ->where('attendance_date', $today)
                                ->first();

        if (!$attendance) {
            return redirect()->route('petugas.attendance.form')
                             ->with('error', 'Akses Ditolak: Anda wajib melakukan Presensi Masuk (Clock In) terlebih dahulu.');
        }

        $listOPD = ['Dinas Komunikasi, Informatika dan Statistik', 'Badan Kepegawaian Daerah', 'Dinas Kesehatan', 'BAPPEDA Lampung', 'Bapenda'];
        $listKategori = ['Web Defacement / Peretasan Situs', 'Judi Online (Judol)', 'Malware / Ransomware Infection', 'Phishing Page / Social Engineering', 'DDoS Attack / Kelumpuhan Jaringan'];

        return view('petugas.input-patroli', compact('listOPD', 'listKategori'));
    }

    /**
     * Transmisi Pengiriman Berkas Laporan Kerja Petugas ke Database + Proteksi Magic Bytes File
     */
    public function store(Request $request) 
    {
        $today = Carbon::today()->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
                                ->where('attendance_date', $today)
                                ->first();

        if (!$attendance) {
            return redirect()->route('petugas.attendance.form')->with('error', 'Akses Ditolak: Logika validasi mendeteksi Anda belum melakukan Clock In.');
        }

        $request->validate([
            'opd_target'        => 'required|string|max:255',
            'incident_category' => 'required|string|max:255',
            'url_target'        => 'required|url|max:255',
            'chronology'        => 'required|string',
            'evidence'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048'
        ]);

        $fileName = null;
        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');

            // VALIDASI MAGIC BYTES (MIME TYPE ASLI BERKAS)
            $realMimeType = $file->getMimeType();
            $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
            if (!in_array($realMimeType, $allowedMimes)) {
                return redirect()->back()->withInput()->withErrors(['evidence' => 'Injeksi Malware Terdeteksi! Ekstensi berkas tidak cocok dengan struktur internal Magic Bytes asli berkas.']);
            }

            // STRUKTUR FOLDER ARSIP BERDASARKAN TAHUN & KATEGORI 
            $cleanCategory = str_replace([' ', '/', '\\', '(', ')'], '_', $request->incident_category);
            $subFolder = 'public/bukti_files/' . Carbon::now()->year . '/' . $cleanCategory;

            $fileName = 'EVIDENCE_' . time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($subFolder, $fileName);
        }

        // Otomatisasi pembuatan ID Log Unik berurutan (Contoh: LOG-045)
        $nextId = (Patrol::max('id') ?? 0) + 1;
        $idLog = 'LOG-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        Patrol::create([
            'id_log'            => $idLog,
            'user_id'           => Auth::id(),
            'attendance_id'     => $attendance->id,
            'opd_sasaran'       => $request->opd_target,
            'kategori_insiden'  => $request->incident_category,
            'target_url'        => $request->url_target,
            'description'       => $request->chronology,
            'file_evidence'     => $fileName,
            'status'            => 'Pending'
        ]);

        return redirect()->route('petugas.dashboard')->with('success', 'Laporan insiden siber berhasil diamankan ke dalam antrean approval Pusat Admin.');
    }

    /**
     * Halaman Riwayat Lengkap Log Patroli Personal Milik Petugas Lapangan (Sinkronisasi Form Filter & UI)
     */
    public function history(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Patrol::where('user_id', Auth::id());

        // Normalisasi status toleran untuk menangkap modifikasi data input
        if ($status) { 
            if ($status === 'Approved') {
                $query->whereIn('status', ['Approved', 'Verified', 'Disetujui Admin']);
            } elseif ($status === 'Rejection') {
                $query->whereIn('status', ['Rejection', 'Perlu Perbaikan']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('opd_sasaran', 'like', "%{$search}%")
                  ->orWhere('kategori_insiden', 'like', "%{$search}%")
                  ->orWhere('id_log', 'like', "%{$search}%");
            });
        }

        $patrols = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        return view('petugas.history', compact('patrols'));
    }

    /**
     * Menampilkan Form Perbaikan/Revisi Log Laporan Kasus yang Ditolak Admin
     */
    public function edit($id)
    {
        $patrol = Patrol::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($patrol->status, ['Perlu Perbaikan', 'Rejection'])) {
            return redirect()->route('petugas.patrol.history')->with('error', 'Akses Modul Ditolak: Log laporan terkunci dari pembukaan revisi.');
        }

        $listOPD = ['Dinas Komunikasi, Informatika dan Statistik', 'Badan Kepegawaian Daerah', 'Dinas Kesehatan', 'BAPPEDA Lampung', 'Bapenda'];
        $listKategori = ['Web Defacement / Peretasan Situs', 'Judi Online (Judol)', 'Malware / Ransomware Infection', 'Phishing Page / Social Engineering', 'DDoS Attack / Kelumpuhan Jaringan'];

        return view('petugas.edit-patroli', compact('patrol', 'listOPD', 'listKategori'));
    }

    /**
     * Memproses Pengiriman Pembaruan Rekaman Data Pasca Koreksi Petugas Lapangan
     */
    public function update(Request $request, $id)
    {
        $patrol = Patrol::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($patrol->status, ['Perlu Perbaikan', 'Rejection'])) {
            return redirect()->route('petugas.patrol.history')->with('error', 'Tindakan Ilegal: Status log terkunci dari manipulasi data eksternal.');
        }

        $request->validate([
            'opd_target'        => 'required|string|max:255',
            'incident_category' => 'required|string|max:255',
            'url_target'        => 'required|url|max:255',
            'chronology'        => 'required|string',
            'evidence'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048' 
        ]);

        if ($request->hasFile('evidence')) {
            $file = $request->file('evidence');
            
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'application/pdf'])) {
                return redirect()->back()->withErrors(['evidence' => 'Validasi Magic Bytes mendeteksi tipe ekstensi berkas berbahaya/palsu.']);
            }

            // Hapus berkas bukti lama
            if ($patrol->file_evidence) {
                $oldCategory = str_replace([' ', '/', '\\', '(', ')'], '_', $patrol->kategori_insiden);
                $oldPath = 'public/bukti_files/' . $patrol->created_at->year . '/' . $oldCategory . '/' . $patrol->file_evidence;
                Storage::delete($oldPath);
            }

            $cleanCategory = str_replace([' ', '/', '\\', '(', ')'], '_', $request->incident_category);
            $subFolder = 'public/bukti_files/' . $patrol->created_at->year . '/' . $cleanCategory;
            $fileName = 'EVIDENCE_' . time() . '_' . Auth::id() . '.' . $file->getClientOriginalExtension();
            $file->storeAs($subFolder, $fileName);
            
            $patrol->file_evidence = $fileName;
        }

        $patrol->update([
            'opd_sasaran'       => $request->opd_target,
            'kategori_insiden'  => $request->incident_category,
            'target_url'        => $request->url_target,
            'description'       => $request->chronology,
            'status'            => 'Pending',
            'admin_correction'  => null 
        ]);

        return redirect()->route('petugas.patrol.history')->with('success', 'Log Kasus #' . $patrol->id_log . ' telah berhasil dikoreksi dan dikirim ulang ke Admin.');
    }

    /**
     * Menampilkan Halaman Dashboard Utama Administrator Pusat + Visualisasi Metriks Tren Grafik Realtime
     */
    public function adminDashboard(Request $request) 
    {
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        // 1. STRUKTURISASI DATA GRAFIK MATRIKS UNTUK TEMPLATE BLADE JS (Format: Array Objek Month-Total)
        $currentYear = Carbon::now()->year;
        $chartData = Patrol::select(
                                DB::raw('MONTH(created_at) as month'),
                                DB::raw('count(*) as total')
                            )
                            ->whereYear('created_at', $currentYear)
                            ->groupBy('month')
                            ->orderBy('month', 'asc')
                            ->get()
                            ->map(function($item) {
                                return [
                                    'month' => (int) $item->month,
                                    'total' => (int) $item->total
                                ];
                            })
                            ->toArray();

        // Card Metrics Utama
        $totalInsiden = Patrol::count();
        $totalJudol = Patrol::where('kategori_insiden', 'LIKE', '%Judi Online%')->count();
        $totalDefacement = Patrol::where('kategori_insiden', 'LIKE', '%Defacement%')->count();
        $totalMalware = Patrol::where('kategori_insiden', 'LIKE', '%Malware%')->count();

        // 2. QUERY UTAMA DENGAN EAGER LOADING RELASI USER (PETUGAS LAPANGAN)
        $query = Patrol::with(['user']);
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('opd_sasaran', 'like', "%{$search}%")
                  ->orWhere('kategori_insiden', 'like', "%{$search}%")
                  ->orWhere('id_log', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $allowedSort = ['created_at', 'opd_sasaran', 'kategori_insiden', 'status'];
        $sortBy = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
        $sortOrder = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

        $patrols = $query->orderBy($sortBy, $sortOrder)->paginate(15)->withQueryString();

        return view('admin.dashboard', compact(
            'patrols', 'chartData', 'totalInsiden', 
            'totalJudol', 'totalDefacement', 'totalMalware'
        ));
    }

    /**
     * Validasi Matrix & Penentuan Keputusan Catatan Audit Pembukaan Akses Revisi Laporan oleh Admin
     */
    public function updateStatus(Request $request, $id) 
    {
        $request->validate([
            'status'           => 'required|in:Pending,Perlu Perbaikan,Rejection,Verified,Approved',
            'admin_correction' => 'required_if:status,Perlu Perbaikan,Rejection|nullable|string|max:1000'
        ]);

        $patrol = Patrol::findOrFail($id);
        
        // Pemetaan keseragaman status agar toleran terhadap UI
        $targetStatus = $request->status;
        if ($targetStatus === 'Approved') { $targetStatus = 'Verified'; }
        if ($targetStatus === 'Rejection') { $targetStatus = 'Perlu Perbaikan'; }

        $patrol->status = $targetStatus;
        $patrol->admin_correction = in_array($targetStatus, ['Perlu Perbaikan', 'Rejection']) ? $request->admin_correction : null;
        
        if ($targetStatus === 'Verified') {
            $patrol->verified_by = Auth::id();
            $patrol->verified_at = Carbon::now();
        } else {
            $patrol->verified_by = null;
            $patrol->verified_at = null;
        }

        $patrol->save();
        return redirect()->back()->with('success', 'Status validasi matriks pelaporan dan log jejak audit admin berhasil direkam.');
    }

    /**
     * Penghapusan Berkas Laporan dari Log Sistem Permanen beserta File Fisiknya
     */
    public function destroy($id) 
    {
        $patrol = Patrol::findOrFail($id);
        
        if ($patrol->file_evidence) {
            $cleanCategory = str_replace([' ', '/', '\\', '(', ')'], '_', $patrol->kategori_insiden);
            $fullPath = 'public/bukti_files/' . $patrol->created_at->year . '/' . $cleanCategory . '/' . $patrol->file_evidence;
            Storage::delete($fullPath);
        }
        
        $patrol->delete();
        return redirect()->back()->with('success', 'Data rekaman kasus insiden siber berhasil dibersihkan dari storage penyimpanan lokal.');
    }
}