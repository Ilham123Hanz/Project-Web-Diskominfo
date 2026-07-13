<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patrol;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PatrolController extends Controller {
    
    /**
     * Menampilkan Halaman Dashboard Utama Petugas Lapangan
     */
    public function index(Request $request) {
        $userId = Auth::id();
        $today = Carbon::today()->toDateString();

        // 1. PENGOPTIMALAN QUERY ABSENSI (Mengunci Khusus Shift 'Masuk' Agar Status Evaluasi/Terlambat Tepat)
        $todayAttendance = Attendance::where('user_id', $userId)
                                     ->where('date', $today)
                                     ->where('shift', 'Masuk') // Saringan krusial agar tidak tertukar data Pulang
                                     ->first();

        // 2. CEK STATUS PULANG HARI INI (Untuk mengontrol tampilan form absen sore di dashboard)
        $hasPulang = Attendance::where('user_id', $userId)
                               ->where('date', $today)
                               ->where('shift', 'Pulang')
                               ->exists();

        // --- FITUR BAWAAN: Fitur Pencarian & Filter Berkas Laporan ---
        $search = $request->input('search');
        $folder = $request->input('folder');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = Patrol::where('user_id', $userId);
        if ($folder) { $query->where('main_menu', $folder); }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('agency_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $patrols = $query->orderBy($sortBy, $sortOrder)->get();

        // 3. KIRIMKAN VARIABEL KE VIEW (Menambahkan 'hasPulang')
        return view('petugas.dashboard', compact('todayAttendance', 'hasPulang', 'patrols'));
    }

    /**
     * Menampilkan Halaman Dashboard Utama Administrator Pusat
     */
    public function adminDashboard(Request $request) {
        $search = $request->input('search');
        $folder = $request->input('folder');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = Patrol::with('user');
        if ($folder) { $query->where('main_menu', $folder); }
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('agency_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $patrols = $query->orderBy($sortBy, $sortOrder)->get();
        return view('admin.dashboard', compact('patrols'));
    }

    /**
     * Transmisi Pengiriman Berkas Laporan Kerja Petugas ke Database Jaringan
     */
    public function store(Request $request) {
        $request->validate([
            'attendance_info' => 'required|string',
            'main_menu' => 'required|string',
            'category' => 'required|string',
            'agency_name' => 'required|string',
            'target_url' => 'nullable|url',
            'threat_level' => 'required|in:Low,Medium,High,Critical',
            'description' => 'required|string',
            'bukti_file' => 'required|mimes:pdf,docx,xlsx|max:10240' // Max 10MB
        ]);

        $fileName = null;
        if ($request->hasFile('bukti_file')) {
            $fileName = time() . '_' . $request->file('bukti_file')->getClientOriginalName();
            $request->file('bukti_file')->storeAs('public/bukti_files', $fileName);
        }

        Patrol::create([
            'user_id' => Auth::id(),
            'attendance_info' => $request->attendance_info,
            'main_menu' => $request->main_menu,
            'category' => $request->category,
            'agency_name' => $request->agency_name,
            'target_url' => $request->target_url,
            'threat_level' => $request->threat_level,
            'description' => $request->description,
            'coordination_note' => $request->coordination_note,
            'file_evidence' => $fileName,
            'status' => 'Pending'
        ]);

        // 🚀 SELESAI: Diarahkan absolut ke route dashboard agar reset filter query dan data langsung ter-render di tabel kanan
        return redirect()
            ->route('petugas.dashboard')
            ->with('success', 'Laporan pengerjaan berhasil diunggah ke Pusat Admin dan dicatat pada log riwayat.');
    }

    /**
     * Validasi Matrix & Koreksi Laporan Petugas oleh Admin
     */
    public function updateStatus(Request $request, $id) {
        $request->validate([
            'status' => 'required|in:Pending,Perlu Perbaikan,Verified',
            'admin_correction' => 'required_if:status,Perlu Perbaikan|nullable|string'
        ]);

        $patrol = Patrol::findOrFail($id);
        $patrol->status = $request->status;
        $patrol->admin_correction = ($request->status === 'Perlu Perbaikan') ? $request->admin_correction : null;
        $patrol->save();

        return redirect()->back()->with('success', 'Status validasi berkas laporan diperbarui.');
    }

    /**
     * Penghapusan Berkas Laporan dari Log Sistem Permanen
     */
    public function destroy($id) {
        $patrol = Patrol::findOrFail($id);
        if ($patrol->file_evidence) {
            Storage::delete('public/bukti_files/' . $patrol->file_evidence);
        }
        $patrol->delete();
        return redirect()->back()->with('success', 'Data rekaman berhasil dihapus secara permanen.');
    }
}