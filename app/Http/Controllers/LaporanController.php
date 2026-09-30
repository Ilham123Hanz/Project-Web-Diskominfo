<?php

namespace App\Http\Controllers;

use App\Models\Laporan;
use App\Models\Presensi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
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

            // Base Query khusus data laporan milik petugas yang login
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

            // Hitung Metriks Ringkasan Kinerja Laporan Secara Agregat
            $totalLaporan  = (clone $baseQuery)->count();
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

            // Ambil Data Laporan Terpaginasi
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
                'totalLaporan',
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
     * Menampilkan Form Input Laporan Baru.
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
                    ->with('error', 'Akses Ditolak: Anda wajib melakukan Presensi Masuk (Clock In) terlebih dahulu sebelum menginput laporan.');
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
            Log::error('Gagal memuat Formulir Laporan: ' . $e->getMessage());
            return redirect()->route('petugas.dashboard')->with('error', 'Gagal membuka formulir laporan.');
        }
    }

    /**
         * Menyimpan Data Laporan Baru.
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
                    ->with('error', 'Akses Ditolak: Anda wajib melakukan Presensi Masuk (Clock In) terlebih dahulu sebelum menginput laporan.');
            }

            // Validasi Input Formulir Laporan - Menggunakan field name dari view (wizard form)
            $validator = Validator::make($request->all(), [
                'rumpun_kategori'   => 'required|string|max:255',
                'main_menu'         => 'required|string|max:255',
                'agency_name'       => 'required|string|max:255',
                'agency_name_manual' => 'nullable|string|max:255',
                'category'          => 'required|string|max:255',
                'category_manual'   => 'nullable|string|max:255',
                'target_url'        => 'nullable|url|max:255',
                'description'       => 'required|string|min:10',
                'threat_level'      => 'required|in:Low,Medium,High,Critical',
                'coordination_note' => 'nullable|string|max:1000',
                'file_evidence'     => 'required|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
            ], [
                'rumpun_kategori.required' => 'Rumpun kategori kerja wajib dipilih.',
                'main_menu.required'       => 'Folder virtual drive tujuan wajib ditentukan.',
                'agency_name.required'     => 'OPD/Instansi sasaran wajib ditentukan.',
                'category.required'        => 'Kategori insiden wajib ditentukan.',
                'description.required'     => 'Kronologi temuan wajib diisi minimal 10 karakter.',
                'description.min'          => 'Kronologi temuan minimal 10 karakter.',
                'threat_level.required'    => 'Tingkat ancaman wajib dipilih.',
                'threat_level.in'          => 'Tingkat ancaman tidak valid.',
                'file_evidence.required'   => 'Berkas bukti wajib diunggah.',
                'file_evidence.file'       => 'Berkas bukti harus berupa file.',
                'file_evidence.mimes'      => 'Format file tidak didukung. Gunakan: JPG, PNG, PDF, DOCX, XLSX.',
                'file_evidence.max'        => 'Ukuran file maksimal 2MB.',
            ]);

            if ($validator->fails()) {
                Log::warning('Validasi gagal simpan patroli', [
                    'user_id' => Auth::id(),
                    'errors' => $validator->errors()->toArray(),
                    'input' => $request->except('file_evidence'),
                    'files' => $request->hasFile('file_evidence') ? [
                        'name' => $request->file('file_evidence')->getClientOriginalName(),
                        'size' => $request->file('file_evidence')->getSize(),
                        'mime' => $request->file('file_evidence')->getMimeType(),
                        'extension' => $request->file('file_evidence')->getClientOriginalExtension(),
                    ] : 'No file uploaded',
                ]);

                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validasi Gagal: ' . $validator->errors()->first());
            }

            DB::beginTransaction();
            try {
                $fileName = null;

                // Pengolahan File Bukti secara Aman
                if ($request->hasFile('file_evidence')) {
                                    $file = $request->file('file_evidence');
                                    $realMimeType = $file->getMimeType();
                                    $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

                                    if (!in_array($realMimeType, $allowedMimes)) {
                                        DB::rollBack();
                                        return redirect()->back()->withInput()->withErrors(['file_evidence' => 'Berkas tidak valid atau terdeteksi korup.'])->with('error', 'Format file tidak didukung.');
                                    }

                                    $year = Carbon::now()->year;
                                    $catName = $request->input('category') ?? $request->input('category_manual') ?? 'General';
                                    $cleanCategory = preg_replace('/[^A-Za-z0-9_\\\\-]/', '_', $catName);
                                    $subFolder = "bukti_files/{$year}/{$cleanCategory}";

                                    $extension = $file->getClientOriginalExtension();
                                    $fileName = 'EVIDENCE_' . time() . '_' . Auth::id() . '_' . uniqid() . '.' . $extension;

                                    $file->storeAs($subFolder, $fileName, 'public');
                                }

                // Auto-Generate Code Log Unik Laporan
                $nextId = (Laporan::max('id') ?? 0) + 1;
                $idLog  = 'LOG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                // Map field dari view ke model
                $opdSasaran = $request->input('agency_name') === 'Lainnya' 
                    ? $request->input('agency_name_manual') 
                    : $request->input('agency_name');
            
                $kategoriInsiden = $request->input('category') === 'Lainnya' 
                    ? $request->input('category_manual') 
                    : $request->input('category');

                $laporan = Laporan::create([
                    'log_code'         => $idLog,
                    'user_id'          => Auth::id(),
                    'presensi_id'      => $attendance->id,
                    'opd_sasaran'      => strip_tags($opdSasaran ?? '-'),
                    'kategori_insiden' => strip_tags($kategoriInsiden ?? '-'),
                    'rumpun_kategori'  => strip_tags($request->input('rumpun_kategori') ?? 'Patroli Harian'),
                    'main_menu'        => strip_tags($request->input('main_menu') ?? 'Patroli Siber'),
                    'target_url'       => $request->input('target_url') ?? '#',
                    'description'      => strip_tags($request->input('description') ?? '-'),
                    'threat_level'     => $request->input('threat_level'),
                    'coordination_note'=> strip_tags($request->input('coordination_note') ?? ''),
                    'file_evidence'    => $fileName,
                    'status'           => 'Pending'
                ]);

                DB::commit();
                Log::info("Laporan Baru Berhasil Disimpan ID: {$laporan->id} [{$idLog}] oleh User ID: " . Auth::id());

                return redirect()->route('petugas.dashboard')
                    ->with('success', 'Laporan insiden siber (' . $idLog . ') berhasil dikirim dan menunggu validasi.');

            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Gagal Menyimpan Laporan: ' . $e->getMessage(), [
                    'user_id' => Auth::id(),
                    'line'    => $e->getLine()
                ]);

                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan sistem saat menyimpan laporan: ' . $e->getMessage());
            }
        }

    public function indexPresensi()
    {
        return view('admin.absensi');
    }

    /**
     * Tampilan Riwayat Laporan Petugas secara Lengkap.
     * Route: petugas.patrol.history
     */
    public function history(Request $request)
    {
        try {
            $search = trim($request->input('search'));
            $status = $request->input('status');

            $query = Laporan::where('user_id', Auth::id());

            // Filtering Berdasarkan Pengelompokan Status Laporan
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

            $laporans = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
            
            return view('petugas.riwayat-log-petugas', compact('laporans'));

        } catch (Exception $e) {
            Log::error('Gagal memuat Riwayat Laporan Petugas: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat riwayat laporan.');
        }
    }

    /**
     * Tampilan Detail Tunggal Laporan Petugas.
     * Route: petugas.laporan.show
     */
    public function show($id)
    {
        try {
            $laporan = Laporan::with(['user', 'presensi'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            // Use petugas-specific detail view
            if (view()->exists('petugas.patroli-detail')) {
                return view('petugas.patroli-detail', compact('laporan'));
            }

            // Fallback to admin view if petugas view doesn't exist
            return view('admin.patroli-detail', compact('laporan'));

        } catch (Exception $e) {
            return redirect()->route('petugas.patrol.history')->with('error', 'Data laporan tidak ditemukan.');
        }
    }

    /**
     * Form Edit/Revisi Laporan untuk Petugas Lapangan.
     * Route: petugas.laporan.edit
     */
    public function edit($id)
    {
        try {
            $laporan = Laporan::where('user_id', Auth::id())->findOrFail($id);

            // Hak Edit Hanya untuk Laporan Berstatus Perbaikan/Penolakan
            if (!in_array($laporan->status, ['Perlu Perbaikan', 'Revision', 'Rejection'])) {
                return redirect()->route('petugas.patrol.history')
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

            return view('petugas.edit-patroli', compact('laporan', 'listOPD', 'listKategori'));

        } catch (Exception $e) {
            return redirect()->route('petugas.patrol.history')->with('error', 'Gagal memuat data laporan untuk direvisi.');
        }
    }

    /**
         * Memproses Pembaruan Data Laporan Revisi dari Petugas.
         * Route: petugas.laporan.update
         */
        public function update(Request $request, $id)
        {
            $laporan = Laporan::where('user_id', Auth::id())->findOrFail($id);

            if (!in_array($laporan->status, ['Perlu Perbaikan', 'Revision', 'Rejection'])) {
                return redirect()->route('petugas.patrol.history')
                    ->with('error', 'Akses Ditolak: Laporan ini sudah tidak dapat diubah.');
            }

            // Validasi Input Formulir Laporan - Menggunakan field name dari view (wizard form)
            $validator = Validator::make($request->all(), [
                'rumpun_kategori'   => 'required|string|max:255',
                'main_menu'         => 'required|string|max:255',
                'agency_name'       => 'required|string|max:255',
                'agency_name_manual' => 'nullable|string|max:255',
                'category'          => 'required|string|max:255',
                'category_manual'   => 'nullable|string|max:255',
                'target_url'        => 'nullable|url|max:255',
                'description'       => 'required|string|min:10',
                'threat_level'      => 'required|in:Low,Medium,High,Critical',
                'coordination_note' => 'nullable|string|max:1000',
                'file_evidence'     => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:2048',
            ], [
                'rumpun_kategori.required' => 'Rumpun kategori kerja wajib dipilih.',
                'main_menu.required'       => 'Folder virtual drive tujuan wajib ditentukan.',
                'agency_name.required'     => 'OPD/Instansi sasaran wajib ditentukan.',
                'category.required'        => 'Kategori insiden wajib ditentukan.',
                'description.required'     => 'Kronologi temuan wajib diisi minimal 10 karakter.',
                'description.min'          => 'Kronologi temuan minimal 10 karakter.',
                'threat_level.required'    => 'Tingkat ancaman wajib dipilih.',
                'threat_level.in'          => 'Tingkat ancaman tidak valid.',
                'file_evidence.file'       => 'Berkas bukti harus berupa file.',
                'file_evidence.mimes'      => 'Format file tidak didukung. Gunakan: JPG, PNG, PDF, DOCX, XLSX.',
                'file_evidence.max'        => 'Ukuran file maksimal 2MB.',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', 'Validasi Gagal: Silakan periksa kembali kelengkapan form input Anda.');
            }

            DB::beginTransaction();
            try {
                // Pembaruan Berkas Jika Mengunggah File Bukti Baru
                if ($request->hasFile('file_evidence')) {
                    $file = $request->file('file_evidence');
                
                    if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
                        return redirect()->back()->withErrors(['file_evidence' => 'Format file yang diunggah tidak diizinkan.']);
                    }

                    // Hapus Berkas Bukti Lama
                    if ($laporan->file_evidence) {
                        $oldCategory = preg_replace('/[^A-Za-z0-9_\\-]/', '_', $laporan->kategori_insiden);
                        $oldPath = "public/bukti_files/{$laporan->created_at->year}/{$oldCategory}/{$laporan->file_evidence}";
                        if (Storage::exists($oldPath)) {
                            Storage::delete($oldPath);
                        }
                    }

                    $year = $laporan->created_at->year;
                    $cleanCategory = preg_replace('/[^A-Za-z0-9_\\-]/', '_', $request->input('category') ?? $request->input('category_manual') ?? 'General');
                    $subFolder = "public/bukti_files/{$year}/{$cleanCategory}";
                    $fileName  = 'EVIDENCE_' . time() . '_' . Auth::id() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                
                    $file->storeAs($subFolder, $fileName);
                    $laporan->file_evidence = $fileName;
                }

                // Map field dari view ke model
                $opdSasaran = $request->input('agency_name') === 'Lainnya' 
                    ? $request->input('agency_name_manual') 
                    : $request->input('agency_name');
            
                $kategoriInsiden = $request->input('category') === 'Lainnya' 
                    ? $request->input('category_manual') 
                    : $request->input('category');

                // Kembalikan Status Laporan Menjadi "Pending" Pasca-Revisi
                $laporan->update([
                    'opd_sasaran'      => strip_tags($opdSasaran ?? '-'),
                    'kategori_insiden' => strip_tags($kategoriInsiden ?? '-'),
                    'rumpun_kategori'  => strip_tags($request->input('rumpun_kategori') ?? 'Patroli Harian'),
                    'main_menu'        => strip_tags($request->input('main_menu') ?? 'Patroli Siber'),
                    'target_url'       => $request->input('target_url') ?? '#',
                    'description'      => strip_tags($request->input('description') ?? '-'),
                    'threat_level'     => $request->input('threat_level'),
                    'coordination_note'=> strip_tags($request->input('coordination_note') ?? ''),
                    'file_evidence'    => $laporan->file_evidence,
                    'status'           => 'Pending',
                    'admin_correction' => null,
                ]);

                DB::commit();
                Log::info("Revisi Laporan ID {$id} [{$laporan->log_code}] berhasil dikirim ulang oleh User " . Auth::id());

                return redirect()->route('petugas.patrol.history')
                    ->with('success', 'Laporan #' . $laporan->log_code . ' telah berhasil diperbarui dan dikirim kembali untuk diverifikasi.');

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
     * Dashboard Utama Administrator Pusat.
     * Route: admin.dashboard
     */
    public function adminDashboard(Request $request) 
    {
        try {
            $currentYear = Carbon::now('Asia/Jakarta')->year;

            $laporansRaw = Laporan::whereYear('created_at', $currentYear)->get();

            $chartDataRaw = array_fill(1, 12, 0);
            foreach ($laporansRaw as $laporanItem) {
                $bulan = (int) Carbon::parse($laporanItem->created_at)->format('n');
                if (isset($chartDataRaw[$bulan])) {
                    $chartDataRaw[$bulan]++;  
                }
            }

            $chartData = array_values($chartDataRaw);

            $totalInsiden    = Laporan::count();
            $totalJudol      = Laporan::where('kategori_insiden', 'LIKE', '%judi%')
                                        ->orWhere('kategori_insiden', 'LIKE', '%judol%')
                                        ->orWhere('main_menu', 'LIKE', '%judi%')
                                        ->count();

            $totalDefacement = Laporan::where('kategori_insiden', 'LIKE', '%defacement%')
                                        ->orWhere('kategori_insiden', 'LIKE', '%web%')
                                        ->orWhere('main_menu', 'LIKE', '%defacement%')
                                        ->count();

            $totalMalware    = Laporan::where('kategori_insiden', 'LIKE', '%malware%')
                                        ->orWhere('kategori_insiden', 'LIKE', '%injection%')
                                        ->orWhere('main_menu', 'LIKE', '%malware%')
                                        ->count();

            $laporans = Laporan::with('user')->latest()->take(5)->get();

            return view('admin.dashboard', compact(
                'laporans', 
                'chartData', 
                'totalInsiden', 
                'totalJudol', 
                'totalDefacement', 
                'totalMalware'
            ));

        } catch (Exception $e) {
            Log::error('Gagal memuat Dashboard Admin: ' . $e->getMessage(), [
                'line' => $e->getLine()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memuat data dashboard.');
        }
    }

    /**
     * Halaman Validasi dan Verifikasi Seluruh Laporan Admin.
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
            Log::error('Gagal memuat daftar validasi laporan: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal memuat modul validasi laporan.');
        }
    }

    /**
     * Memperbarui status laporan.
     * Route: admin.patrol.update-status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|in:Pending,Verified,Perlu Perbaikan,Rejected',
            'admin_correction' => 'nullable|string|max:1000',
            'catatan_revisi'   => 'nullable|string|max:1000',
        ]);

        try {
            $laporan = Laporan::findOrFail($id);
            $laporan->status = $request->status;

            $correctionNote = $request->admin_correction ?? $request->catatan_revisi;

            if ($request->status === 'Perlu Perbaikan' || !empty($correctionNote)) {
                $laporan->admin_correction = $correctionNote;
            } elseif ($request->status === 'Verified') {
                $laporan->admin_correction = null;
            }

            $laporan->save();

            return redirect()->back()->with('success', 'Status laporan #' . $laporan->log_code . ' berhasil diperbarui.');

        } catch (Exception $e) {
            Log::error('Gagal Update Status Laporan: ' . $e->getMessage());
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
            $laporan = Laporan::findOrFail($id);
            
            if ($laporan->file_evidence) {
                $cleanCategory = preg_replace('/[^A-Za-z0-9_\-]/', '_', $laporan->kategori_insiden);
                $fullPath      = "public/bukti_files/{$laporan->created_at->year}/{$cleanCategory}/{$laporan->file_evidence}";
                
                if (Storage::exists($fullPath)) {
                    Storage::delete($fullPath);
                }
            }
            
            $laporan->delete();
            DB::commit();

            Log::info("Laporan ID {$id} [{$laporan->log_code}] berhasil dihapus oleh Admin ID " . Auth::id());

            return redirect()->back()->with('success', 'Laporan beserta berkas terkait berhasil dihapus dari sistem.');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus Laporan ID {$id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus laporan.');
        }
    }

    /**
     * Menampilkan Konfigurasi SMTP Email.
     * Route: admin.smtp
     */
    public function showSmtpSettings()
    {
        $patrols = Laporan::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.smtp', compact('patrols'));
    }

    /**
     * Distribusi Notifikasi Laporan via SMTP Email (Simulasi/Uji Coba).
     * Route: admin.patrol.distribute
     */
    public function distributeEmail(Request $request, $id)
    {
        try {
            $laporan = Laporan::with('user')->findOrFail($id);
            
            // Get email recipients - from OPD email settings or default
            $emails = \App\Models\OpdEmail::whereHas('masterOpd', function($q) use ($laporan) {
                $q->where('nama_opd', 'LIKE', "%{$laporan->opd_sasaran}%");
            })->pluck('alamat_email')->toArray();
            
            // Fallback: default admin email
            if (empty($emails)) {
                $emails = [config('mail.from.address', 'admin@lampungprov.go.id')];
            }

            // SIMULASI: Log email yang seharusnya dikirim (tanpa kirim aktual)
            foreach ($emails as $email) {
                Log::info("[SIMULASI SMTP] Email notifikasi Laporan ID {$id} [{$laporan->log_code}] seharusnya dikirim ke: {$email} oleh Admin ID " . Auth::id());
                Log::info("[SIMULASI SMTP] Subjek: [SIP-O-SIBER] Notifikasi Laporan: {$laporan->log_code}");
                Log::info("[SIMULASI SMTP] Isi email: Laporan {$laporan->log_code} untuk OPD {$laporan->opd_sasaran} - Status: {$laporan->status}");
            }

            // Update status laporan jika perlu (misal: set ke 'Dikirim' atau catat log distribusi)
            $laporan->update([
                'admin_correction' => ($laporan->admin_correction ?? '') . "\n\n[DISTRIBUSI EMAIL] Notifikasi disimulasikan terkirim ke " . count($emails) . " penerima pada " . now()->format('d/m/Y H:i') . " oleh " . (Auth::user()->name ?? 'System'),
            ]);

            return redirect()->back()->with('success', 'SIMULASI: Notifikasi laporan #' . $laporan->log_code . ' berhasil didistribusikan (log tersimpan) ke ' . count($emails) . ' penerima. Email TIDAK benar-benar terkirim (mode uji coba).');

        } catch (Exception $e) {
            Log::error('Gagal mensimulasikan distribusi email notifikasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mensimulasikan distribusi email: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Master Data OPD.
     * Route: admin.master-opd.index
     */
    public function masterOpd()
    {
        return view('admin.master-opd');
    }

    /**
     * Tampilan Arsip Virtual Berdasarkan Pengelompokan Tahun.
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

            return view('admin.folder-virtual', compact('folders'));

        } catch (Exception $e) {
            Log::error('Gagal memuat arsip folder virtual: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memuat folder arsip virtual.');
        }
    }

    /**
     * Menampilkan Halaman Profil Admin.
     * Route: admin.profil
     */
    public function profilAdmin()
    {
        $user = auth()->user();
        return view('admin.profil', compact('user'));
    }

    /**
     * Update Profil Admin
     * Route: admin.profil.update
     */
    public function updateProfilAdmin(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profil Admin berhasil diperbarui!');
    }

    /**
     * =========================================================================
     * 3. MODUL REKAPITULASI, EKSPOR DATA & CETAK PDF (LAPORAN & PRESENSI)
     * =========================================================================
     */

    /**
     * Rekapitulasi Data Laporan pada Panel Admin.
     * Route: admin.laporan.patroli.index
     */
    public function indexPatroli(Request $request)
    {
        try {
            $query = Laporan::with('user');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            $laporans = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

            return view('admin.laporan.patroli-index', compact('laporans'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat data laporan: ' . $e->getMessage());
        }
    }

    /**
     * Cetak Lembar Dokumen PDF Laporan Tunggal.
     * Route: petugas.laporan.patroli.pdf & admin.laporan.patroli.pdf
     */
    public function cetakPdf($id)
    {
        try {
            $laporan = Laporan::with('user')->findOrFail($id);

            $pdf = Pdf::loadView('pdf.patroli-detail', compact('laporan'));
            return $pdf->stream('laporan-insiden-' . $laporan->log_code . '.pdf');

        } catch (Exception $e) {
            Log::error('Gagal Cetak PDF Laporan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh cetakan PDF laporan.');
        }
    }

    /**
     * Cetak Lembar Dokumen PDF Laporan Tunggal (Alias untuk cetakPdf).
     * Route: petugas.laporan.patroli.pdf & admin.laporan.patroli.pdf
     */
    public function cetakPatroliPdf($id)
    {
        return $this->cetakPdf($id);
    }

    /**
     * Ekspor Data Rekapitulasi Laporan ke Format CSV / Excel.
     * Route: admin.laporan.patroli.excel
     */
    public function exportPatroliExcel(Request $request)
    {
        try {
            $fileName = 'rekap-laporan-' . date('Y-m-d_H-i-s') . '.csv';

            $query = Laporan::with('user');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00', 
                    $request->end_date . ' 23:59:59'
                ]);
            }

            return response()->streamDownload(function () use ($query) {
                $handle = fopen('php://output', 'w');
                
                // Tambahkan BOM untuk Excel agar format UTF-8 terbaca presisi
                fwrite($handle, "\xEF\xBB\xBF");

                // Header Kolom CSV Laporan
                fputcsv($handle, [
                    'Log Code', 
                    'Petugas', 
                    'OPD/Sasaran', 
                    'Kategori Insiden',
                    'URL Target',
                    'Status',
                    'Tanggal Dibuat'
                ]);

                $query->chunk(100, function ($laporans) use ($handle) {
                    foreach ($laporans as $laporan) {
                        fputcsv($handle, [
                            $laporan->log_code,
                            $laporan->user->name ?? '-',
                            $laporan->opd_sasaran,
                            $laporan->kategori_insiden,
                            $laporan->target_url,
                            $laporan->status,
                            $laporan->created_at ? $laporan->created_at->format('Y-m-d H:i:s') : '-'
                        ]);
                    }
                });

                fclose($handle);
            }, $fileName, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (Exception $e) {
            Log::error('Gagal Ekspor Laporan Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor data laporan.');
        }
    }

    /**
     * Rekapitulasi PDF Laporan Patroli.
     * Route: admin.laporan.patroli.rekap-pdf
     */
    public function rekapPatroliPdf(Request $request)
    {
        try {
            $query = Laporan::with('user');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('created_at', [
                    $request->start_date . ' 00:00:00',
                    $request->end_date . ' 23:59:59'
                ]);
            }

            $laporans = $query->orderBy('created_at', 'desc')->get();

            $pdf = Pdf::loadView('pdf.patroli-rekap', compact('laporans', 'request'));
            return $pdf->stream('rekap-laporan-patroli-' . date('Y-m-d') . '.pdf');

        } catch (Exception $e) {
            Log::error('Gagal Cetak Rekap PDF Laporan: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh rekap PDF laporan.');
        }
    }

    /**
     * Rekapitulasi PDF Presensi.
     * Route: admin.laporan.presensi.rekap-pdf
     */
    public function rekapPresensiPdf(Request $request)
    {
        try {
            $query = Presensi::with('user');

            if ($request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('tanggal_presensi', [
                    $request->start_date,
                    $request->end_date
                ]);
            } elseif ($request->filled('date')) {
                $query->whereDate('tanggal_presensi', $request->date);
            } elseif ($request->filled('month')) {
                $query->whereMonth('tanggal_presensi', $request->month)
                      ->whereYear('tanggal_presensi', $request->year ?? now()->year);
            }

            $presensis = $query->orderBy('tanggal_presensi', 'desc')->get();

            $pdf = Pdf::loadView('pdf.presensi-rekap', compact('presensis', 'request'));
            return $pdf->stream('rekap-presensi-' . date('Y-m-d') . '.pdf');

        } catch (Exception $e) {
            Log::error('Gagal Cetak Rekap PDF Presensi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengunduh rekap PDF presensi.');
        }
    }

    /**
     * Kirim Test Email SMTP.
     * Route: admin.smtp.send-test
     */
    public function sendTestEmail(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
            ]);

            Mail::raw('Ini adalah email test dari SIP-O-SIBER untuk memverifikasi konfigurasi SMTP.', function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('[SIP-O-SIBER] Test Email SMTP')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Test email SMTP dikirim ke: ' . $request->email . ' oleh Admin ID ' . Auth::id());

            return redirect()->back()->with('success', 'Test email berhasil dikirim ke ' . $request->email);

        } catch (Exception $e) {
            Log::error('Gagal kirim test email SMTP: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengirim test email: ' . $e->getMessage());
        }
    }
}