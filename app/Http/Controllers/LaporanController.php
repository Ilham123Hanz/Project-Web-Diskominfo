<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Exception;

class LaporanController extends Controller 
{
    /**
     * =========================================================================
     * 1. FITUR & MODUL OPERASIONAL PETUGAS LAPANGAN
     * =========================================================================
     */

    /**
     * Menampilkan Dashboard Utama Petugas Lapangan.
     * Route: petugas.dashboard
     */
    public function index(Request $request) 
    {
        try {
            $user = Auth::user();
            $today = Carbon::today('Asia/Jakarta')->toDateString();

            // Status Presensi Hari Ini
            $todayAttendance = Presensi::where('user_id', $user->id)
                ->where('tanggal_presensi', $today)
                ->first();

            $hasPulang = ($todayAttendance && !empty($todayAttendance->jam_pulang));

            // Base Query khusus data milik petugas yang login
            $baseQuery = Laporan::where('user_id', $user->id);

            // Filter Pencarian Pasien / Sasaran Laporan
            $search = trim($request->input('search'));
            $folder = $request->input('folder'); 

            if (!empty($folder)) {
                if (is_numeric($folder)) {
                    $baseQuery->whereYear('created_at', $folder);
                } else {
                    $baseQuery->where('main_menu', $folder);
                }
            }

            if (!empty($search)) {
                $baseQuery->where(function($q) use ($search) {
                    $q->where('opd_sasaran', 'like', "%{$search}%")
                      ->orWhere('kategori_insiden', 'like', "%{$search}%")
                      ->orWhere('log_code', 'like', "%{$search}%")
                      ->orWhere('target_url', 'like', "%{$search}%");
                });
            }

            // Hitung Metriks Ringkasan Kinerja Secara Agregat (Bukan Hanya Per Halaman Pagination)
            $totalPatrols  = (clone $baseQuery)->count();
            $totalVerified = (clone $baseQuery)->whereIn('status', ['Verified', 'Approved', 'Disetujui Admin'])->count();
            $totalPending  = (clone $baseQuery)->whereIn('status', ['Pending', 'Menunggu Validasi'])->count();
            $totalRevision = (clone $baseQuery)->whereIn('status', ['Perlu Perbaikan', 'Revision', 'Rejection'])->count();

            $metrics = [
                'total_hari_ini' => Laporan::where('user_id', $user->id)->whereDate('created_at', $today)->count(),
                'pending'        => $totalPending,
                'verified'       => $totalVerified,
                'rejection'      => $totalRevision,
            ];

            // Peringatan Catatan Koreksi Terbaru dari Admin
            $latestCorrection = Laporan::where('user_id', $user->id)
                ->whereIn('status', ['Perlu Perbaikan', 'Revision', 'Rejection'])
                ->whereNotNull('admin_correction')
                ->orderBy('updated_at', 'desc')
                ->first();

            // Sorting Dynamic
            $sortBy    = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $allowedSort = ['created_at', 'opd_sasaran', 'kategori_insiden', 'status'];
            $sortBy      = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
            $sortOrder   = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

            // Ambil Data Terpaginasi
            $patrols = $baseQuery->orderBy($sortBy, $sortOrder)
                ->paginate(10)
                ->withQueryString();

            // Master Data Dropdown
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

            return view('petugas.dashboard-petugas', compact(
                'todayAttendance', 
                'hasPulang', 
                'patrols', 
                'listOPD', 
                'listKategori', 
                'metrics', 
                'latestCorrection',
                'totalPatrols',
                'totalVerified',
                'totalPending',
                'totalRevision'
            ));

        } catch (Exception $e) {
            Log::error('Gagal memuat Dashboard Petugas: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'line'    => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memuat data dashboard.');
        }
    }

    /**
     * Menampilkan Form Input Patroli Siber Baru.
     * Route: petugas.laporan.create
     */
    public function create()
    {
        try {
            $today = Carbon::today('Asia/Jakarta')->toDateString();
            $attendance = Presensi::where('user_id', Auth::id())
                ->where('tanggal_presensi', $today)
                ->first();

            // Proteksi Presensi Masuk sebelum Input Laporan
            if (!$attendance || empty($attendance->jam_masuk)) {
                return redirect()->route('petugas.attendance.form')
                    ->with('error', 'Akses Ditolak: Anda wajib melakukan Presensi Masuk (Clock In) terlebih dahulu sebelum menginput laporan patroli.');
            }

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

            return view('petugas.input-patroli-petugas', compact('listOPD', 'listKategori'));

        } catch (Exception $e) {
            Log::error('Gagal memuat Formulir Patroli: ' . $e->getMessage());
            return redirect()->route('petugas.dashboard')->with('error', 'Gagal membuka formulir patroli.');
        }
    }

    /**
     * Menyimpan Data Laporan Patroli Baru (Atomic Transaction & Secure File Storage).
     * Route: petugas.laporan.store
     */
    public function store(Request $request) 
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $attendance = Presensi::where('user_id', Auth::id())
            ->where('tanggal_presensi', $today)
            ->first();

        if (!$attendance || empty($attendance->jam_masuk)) {
            return redirect()->route('petugas.attendance.form')
                ->with('error', 'Akses Ditolak: Logika validasi mendeteksi Anda belum melakukan Presensi Masuk hari ini.');
        }

        // Validasi Input Formulir
        $validator = Validator::make($request->all(), [
            'opd_target'        => 'nullable|string|max:255',
            'instansi_target'   => 'nullable|string|max:255',
            'incident_category' => 'nullable|string|max:255',
            'rumpun_kategori'   => 'nullable|string|max:255',
            'main_menu'         => 'nullable|string|max:255',
            'kategori_vdrive'   => 'nullable|string|max:255',
            'url_target'        => 'nullable|url|max:255',
            'chronology'        => 'nullable|string|min:5',
            'deskripsi_masalah' => 'nullable|string|min:5',
            'evidence'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'bukti_file'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi Gagal: Silakan periksa kembali kelengkapan form input Anda.');
        }

        DB::beginTransaction();
        try {
            $fileName = null;
            $fileInput = $request->hasFile('evidence') ? 'evidence' : ($request->hasFile('bukti_file') ? 'bukti_file' : null);

            // Pengolahan File Bukti secara Aman
            if ($fileInput) {
                $file = $request->file($fileInput);
                $realMimeType = $file->getMimeType();
                $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
                
                if (!in_array($realMimeType, $allowedMimes)) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->withErrors([$fileInput => 'Berkas tidak valid atau terdeteksi korup.']);
                }

                $year = Carbon::now()->year;
                $catName = $request->input('incident_category') ?? $request->input('rumpun_kategori') ?? 'General';
                $cleanCategory = preg_replace('/[^A-Za-z0-9_\-]/', '_', $catName);
                $subFolder = "public/bukti_files/{$year}/{$cleanCategory}";

                $extension = $file->getClientOriginalExtension();
                $fileName = 'EVIDENCE_' . time() . '_' . Auth::id() . '_' . uniqid() . '.' . $extension;
                
                $file->storeAs($subFolder, $fileName);
            }

            // Auto-Generate Code Log Unik
            $nextId = (Laporan::max('id') ?? 0) + 1;
            $idLog  = 'LOG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

            $laporan = Laporan::create([
                'log_code'         => $idLog,
                'user_id'          => Auth::id(),
                'presensi_id'      => $attendance->id,
                'opd_sasaran'      => strip_tags($request->input('opd_target') ?? $request->input('instansi_target') ?? '-'),
                'kategori_insiden' => strip_tags($request->input('incident_category') ?? $request->input('rumpun_kategori') ?? '-'),
                'main_menu'        => strip_tags($request->input('main_menu') ?? $request->input('kategori_vdrive') ?? 'Patroli Siber'),
                'target_url'       => $request->input('url_target') ?? '#',
                'description'      => strip_tags($request->input('chronology') ?? $request->input('deskripsi_masalah') ?? '-'),
                'file_evidence'    => $fileName,
                'status'           => 'Pending'
            ]);

            DB::commit();
            Log::info("Laporan Baru Berhasil Disimpan ID: {$laporan->id} [{$idLog}] oleh User ID: " . Auth::id());

            return redirect()->route('petugas.dashboard')
                ->with('success', 'Laporan insiden siber (' . $idLog . ') berhasil dikirim dan menunggu validasi.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Gagal Menyimpan Laporan Patroli: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'line'    => $e->getLine()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan laporan: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan Riwayat Laporan Petugas secara Lengkap.
     * Route: petugas.laporan.history
     */
    public function history(Request $request)
    {
        try {
            $search = trim($request->input('search'));
            $status = $request->input('status');

            $query = Laporan::where('user_id', Auth::id());

            // Filtering Berdasarkan Pengelompokan Status
            if (!empty($status)) { 
                if ($status === 'Approved') {
                    $query->whereIn('status', ['Approved', 'Verified', 'Disetujui Admin']);
                } elseif ($status === 'Rejection') {
                    $query->whereIn('status', ['Rejection', 'Perlu Perbaikan', 'Revision']);
                } else {
                    $query->where('status', $status);
                }
            }

            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('opd_sasaran', 'like', "%{$search}%")
                      ->orWhere('kategori_insiden', 'like', "%{$search}%")
                      ->orWhere('log_code', 'like', "%{$search}%")
                      ->orWhere('target_url', 'like', "%{$search}%");
                });
            }

            $patrols = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
            
            return view('petugas.riwayat-log-petugas', compact('patrols'));

        } catch (Exception $e) {
            Log::error('Gagal memuat Riwayat Log Petugas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat riwayat laporan.');
        }
    }

    /**
     * Tampilan Detail Tunggal Laporan Patroli Petugas.
     * Route: petugas.laporan.show
     */
    public function show($id)
    {
        try {
            $patrol = Laporan::with(['user', 'presensi'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            return view('petugas.detail-patroli', compact('patrol'));

        } catch (Exception $e) {
            Log::warning("Akses ilegal atau Laporan ID {$id} tidak ditemukan untuk User " . Auth::id());
            return redirect()->route('petugas.laporan.history')->with('error', 'Laporan tidak ditemukan atau Anda tidak memiliki hak akses.');
        }
    }

    /**
     * Form Edit/Revisi Laporan untuk Petugas Lapangan.
     * Route: petugas.laporan.edit
     */
    public function edit($id)
    {
        try {
            $patrol = Laporan::where('user_id', Auth::id())->findOrFail($id);

            // Hak Edit Hanya untuk Laporan Berstatus Perbaikan/Penolakan
            if (!in_array($patrol->status, ['Perlu Perbaikan', 'Revision', 'Rejection'])) {
                return redirect()->route('petugas.laporan.history')
                    ->with('error', 'Akses Ditolak: Laporan ini berstatus terkunci atau sedang diproses admin.');
            }

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

            return view('petugas.edit-patroli', compact('patrol', 'listOPD', 'listKategori'));

        } catch (Exception $e) {
            return redirect()->route('petugas.laporan.history')->with('error', 'Gagal memuat data laporan untuk direvisi.');
        }
    }

    /**
     * Memproses Pembaruan Data Laporan Revisi dari Petugas.
     * Route: petugas.laporan.update
     */
    public function update(Request $request, $id)
    {
        $patrol = Laporan::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($patrol->status, ['Perlu Perbaikan', 'Revision', 'Rejection'])) {
            return redirect()->route('petugas.laporan.history')
                ->with('error', 'Akses Ditolak: Laporan ini sudah tidak dapat diubah.');
        }

        $validator = Validator::make($request->all(), [
            'opd_target'        => 'required|string|max:255',
            'incident_category' => 'required|string|max:255',
            'url_target'        => 'required|url|max:255',
            'chronology'        => 'required|string|min:10',
            'evidence'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120' 
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Pembaruan Berkas Jika Mengunggah File Bukti Baru
            if ($request->hasFile('evidence')) {
                $file = $request->file('evidence');
                
                if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'application/pdf'])) {
                    return redirect()->back()->withErrors(['evidence' => 'Format file yang diunggah tidak diizinkan.']);
                }

                // Hapus Berkas Bukti Lama dari Disk Storage
                if ($patrol->file_evidence) {
                    $oldCategory = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patrol->kategori_insiden);
                    $oldPath = "public/bukti_files/{$patrol->created_at->year}/{$oldCategory}/{$patrol->file_evidence}";
                    if (Storage::exists($oldPath)) {
                        Storage::delete($oldPath);
                    }
                }

                $year = $patrol->created_at->year;
                $cleanCategory = preg_replace('/[^A-Za-z0-9_\-]/', '_', $request->incident_category);
                $subFolder = "public/bukti_files/{$year}/{$cleanCategory}";
                $fileName  = 'EVIDENCE_' . time() . '_' . Auth::id() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                $file->storeAs($subFolder, $fileName);
                $patrol->file_evidence = $fileName;
            }

            // Kembalikan Status Laporan Menjadi "Pending" Pasca-Revisi
            $patrol->update([
                'opd_sasaran'      => strip_tags($request->opd_target),
                'kategori_insiden' => strip_tags($request->incident_category),
                'target_url'       => $request->url_target,
                'description'      => strip_tags($request->chronology),
                'status'           => 'Pending',
                'admin_correction' => null,
                'file_evidence'    => $patrol->file_evidence
            ]);

            DB::commit();
            Log::info("Revisi Laporan ID {$id} [{$patrol->log_code}] berhasil dikirim ulang oleh User " . Auth::id());

            return redirect()->route('petugas.laporan.history')
                ->with('success', 'Laporan #' . $patrol->log_code . ' telah berhasil diperbarui dan dikirim kembali untuk diverifikasi.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Gagal memperbarui revisi Laporan ID {$id}: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data laporan.');
        }
    }


    /**
     * =========================================================================
     * 2. FITUR & MODUL MANAJEMEN ADMINISTRATOR PUSAT
     * =========================================================================
     */

    /**
     * Dashboard Utama Administrator Pusat (Ringkasan Analitik & Grafis).
     * Route: admin.dashboard
     */
    public function adminDashboard(Request $request) 
    {
        try {
            $search    = trim($request->input('search'));
            $sortBy    = $request->input('sort_by', 'created_at');
            $sortOrder = $request->input('sort_order', 'desc');

            $currentYear = Carbon::now('Asia/Jakarta')->year;
            
            // Rekap Statistik Tren Bulanan untuk Visualisasi Chart
            $chartData = Laporan::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('count(*) as total')
                )
                ->whereYear('created_at', $currentYear)
                ->groupBy('month')
                ->orderBy('month', 'asc')
                ->get()
                ->map(fn($item) => ['month' => (int) $item->month, 'total' => (int) $item->total])
                ->toArray();

            // Total Metriks Insiden
            $totalInsiden    = Laporan::count();
            $totalJudol      = Laporan::where('kategori_insiden', 'LIKE', '%Judi Online%')->count();
            $totalDefacement = Laporan::where('kategori_insiden', 'LIKE', '%Defacement%')->count();
            $totalMalware    = Laporan::where('kategori_insiden', 'LIKE', '%Malware%')->count();

            // Optimasi Eager Loading Relasi User
            $query = Laporan::with(['user']);
            
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('opd_sasaran', 'like', "%{$search}%")
                      ->orWhere('kategori_insiden', 'like', "%{$search}%")
                      ->orWhere('log_code', 'like', "%{$search}%")
                      ->orWhereHas('user', function($u) use ($search) {
                          $u->where('name', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                      });
                });
            }

            $allowedSort = ['created_at', 'opd_sasaran', 'kategori_insiden', 'status'];
            $sortBy      = in_array($sortBy, $allowedSort) ? $sortBy : 'created_at';
            $sortOrder   = in_array(strtolower($sortOrder), ['asc', 'desc']) ? $sortOrder : 'desc';

            $patrols = $query->orderBy($sortBy, $sortOrder)->paginate(15)->withQueryString();

            return view('admin.dashboard', compact(
                'patrols', 'chartData', 'totalInsiden', 
                'totalJudol', 'totalDefacement', 'totalMalware'
            ));

        } catch (Exception $e) {
            Log::error('Gagal memuat Dashboard Admin: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat dashboard administrator.');
        }
    }

    /**
     * Halaman Validasi dan Verifikasi Seluruh Patroli Admin.
     * Route: admin.validasi
     */
    public function allPatrols(Request $request)
    {
        try {
            $query = Laporan::with(['user']);
            
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function($q) use ($search) {
                    $q->where('opd_sasaran', 'like', "%{$search}%")
                      ->orWhere('kategori_insiden', 'like', "%{$search}%")
                      ->orWhere('log_code', 'like', "%{$search}%");
                });
            }

            $patrols = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

            return view('admin.validasi-patroli', compact('patrols'));

        } catch (Exception $e) {
            Log::error('Gagal memuat daftar validasi patroli: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat modul validasi patroli.');
        }
    }

    /**
     * Mengubah Status Validasi & Menambahkan Catatan Perbaikan Admin.
     * Route: admin.patrol.update-status
     */
    public function updateStatus(Request $request, $id) 
    {
        $validator = Validator::make($request->all(), [
            'status'           => 'required|in:Pending,Perlu Perbaikan,Revision,Rejection,Verified,Approved',
            'admin_correction' => 'required_if:status,Perlu Perbaikan,Revision,Rejection|nullable|string|max:1000'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error', 'Gagal memperbarui status: Catatan wajib diisi jika laporan ditolak/diperlukan perbaikan.');
        }

        try {
            $patrol = Laporan::findOrFail($id);
            
            $targetStatus = $request->status;

            // Normalisasi Penamaan Status
            if ($targetStatus === 'Approved') { $targetStatus = 'Verified'; }
            if (in_array($targetStatus, ['Rejection', 'Revision'])) { $targetStatus = 'Perlu Perbaikan'; }

            $patrol->status           = $targetStatus;
            $patrol->admin_correction = ($targetStatus === 'Perlu Perbaikan') ? strip_tags($request->admin_correction) : null;
            
            if ($targetStatus === 'Verified') {
                $patrol->verified_by = Auth::id();
                $patrol->verified_at = Carbon::now('Asia/Jakarta');
            } else {
                $patrol->verified_by = null;
                $patrol->verified_at = null;
            }

            $patrol->save();

            Log::info("Status Laporan ID {$id} diperbarui menjadi '{$targetStatus}' oleh Admin ID " . Auth::id());

            return redirect()->back()->with('success', 'Status validasi laporan #' . $patrol->log_code . ' berhasil diperbarui.');

        } catch (Exception $e) {
            Log::error("Gagal mengupdate status Laporan ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui status laporan.');
        }
    }

    /**
     * Hapus Laporan & Pembersihan File Fisik Bukti di Storage.
     * Route: admin.patrol.delete
     */
    public function destroy($id) 
    {
        DB::beginTransaction();
        try {
            $patrol = Laporan::findOrFail($id);
            
            // Hapus Bukti Fisik di Storage Disk
            if ($patrol->file_evidence) {
                $cleanCategory = preg_replace('/[^A-Za-z0-9_\-]/', '_', $patrol->kategori_insiden);
                $fullPath      = "public/bukti_files/{$patrol->created_at->year}/{$cleanCategory}/{$patrol->file_evidence}";
                
                if (Storage::exists($fullPath)) {
                    Storage::delete($fullPath);
                }
            }
            
            $patrol->delete();
            DB::commit();

            Log::info("Laporan ID {$id} [{$patrol->log_code}] berhasil dihapus oleh Admin ID " . Auth::id());

            return redirect()->back()->with('success', 'Laporan beserta berkas terkait berhasil dihapus dari sistem.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus Laporan ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus laporan.');
        }
    }

    /**
     * Menampilkan Konfigurasi Distribusi SMTP Email.
     * Route: admin.smtp
     */
    public function showSmtpSettings()
    {
        return view('admin.smtp-settings');
    }

    /**
     * Distribusi Notifikasi Laporan via SMTP Email (Mock Dispatcher).
     * Route: admin.patrol.distribute
     */
    public function distributeEmail(Request $request, $id)
    {
        try {
            $patrol = Laporan::findOrFail($id);
            Log::info("Email notifikasi Laporan ID {$id} dikirim oleh Admin ID " . Auth::id());

            return redirect()->back()->with('success', 'Notifikasi laporan #' . $patrol->log_code . ' berhasil didistribusikan via Email.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mendistribusikan email notifikasi.');
        }
    }

    /**
     * Menampilkan Tampilan Arsip Virtual Berdasarkan Pengelompokan Tahun.
     * Route: admin.folder_virtual
     */
    public function archiveFolders()
    {
        try {
            $folders = Laporan::select(DB::raw('YEAR(created_at) as year'))
                ->whereNotNull('created_at')
                ->groupBy('year')
                ->orderBy('year', 'desc')
                ->pluck('year');

            return view('admin.virtual-folders', compact('folders'));

        } catch (Exception $e) {
            Log::error('Gagal memuat arsip folder virtual: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat folder arsip virtual.');
        }
    }


    /**
     * =========================================================================
     * 3. MODUL REKAPITULASI, EKSPOR DATA & CETAK PDF (PATROLI & PRESENSI)
     * =========================================================================
     */

    /**
     * Rekapitulasi Laporan Patroli pada Panel Admin.
     * Route: admin.laporan.patroli.index
     */
    public function indexPatroli(Request $request)
    {
        try {
            $query = Laporan::with('user');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            $patrols = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

            return view('admin.laporan.patroli-index', compact('patrols'));

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat rekapitulasi laporan patroli.');
        }
    }

    /**
     * Cetak Lembar Dokumen PDF Laporan Patroli Tunggal.
     * Route: petugas.laporan.patroli.pdf & admin.laporan.patroli.pdf
     */
    public function cetakPatroliPdf($id)
    {
        try {
            $patrol = Laporan::with(['user', 'presensi'])->findOrFail($id);

            // Hak Akses Restriksi untuk Petugas
            if (Auth::user()->role === 'Petugas' && $patrol->user_id !== Auth::id()) {
                abort(403, 'Akses Ditolak: Anda tidak memiliki wewenang untuk mencetak dokumen laporan ini.');
            }

            return view('pdf.patroli-single', compact('patrol'));

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengunduh cetakan PDF laporan.');
        }
    }

    /**
     * Ekspor Data Rekapitulasi Patroli ke Format CSV / Excel.
     * Route: admin.laporan.patroli.excel
     */
    public function exportPatroliExcel()
    {
        try {
            $fileName = 'rekap-patroli-siber-' . Carbon::now()->format('Y-m-d_H-i-s') . '.csv';

            return response()->streamDownload(function() {
                $handle = fopen('php://output', 'w');
                // Menambahkan UTF-8 BOM
                fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

                // Header Kolom CSV
                fputcsv($handle, ['ID Log', 'Petugas', 'OPD Sasaran', 'Kategori Insiden', 'Target URL', 'Status', 'Tanggal Laporan']);

                Laporan::with('user')->chunk(200, function($laporanChunk) use ($handle) {
                    foreach ($laporanChunk as $row) {
                        fputcsv($handle, [
                            $row->log_code,
                            $row->user->name ?? 'N/A',
                            $row->opd_sasaran,
                            $row->kategori_insiden,
                            $row->target_url,
                            $row->status,
                            Carbon::parse($row->created_at)->format('Y-m-d H:i:s')
                        ]);
                    }
                });

                fclose($handle);
            }, $fileName, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);

        } catch (Exception $e) {
            Log::error('Gagal Ekspor Excel Patroli: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor data ke format Excel.');
        }
    }

    /**
     * Cetak PDF Rekapitulasi Banyak Laporan Patroli.
     * Route: admin.laporan.patroli.rekap-pdf
     */
    public function rekapPatroliPdf(Request $request)
    {
        try {
            $query = Laporan::with('user');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            $patrols = $query->orderBy('created_at', 'desc')->get();

            return view('pdf.patroli-rekap', compact('patrols'));

        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal mencetak rekap PDF.');
        }
    }
}