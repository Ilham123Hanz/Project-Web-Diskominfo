<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatrolController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\MasterOpdController;
use App\Http\Controllers\Admin\MasterKategoriController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Pelaporan & Operasional Siber (SIP-O-SIBER)
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. ZONA UMUM / PENGUNJUNG (GUEST ZONES)
// ==========================================
Route::middleware(['guest'])->group(function () {
    // Gerbang Masuk Otentikasi Terpusat
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Alur Registrasi Akun Mandiri Personel Baru
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Sesi Pemutusan Autentikasi (Logout)
Route::middleware(['auth'])->group(function () {
    Route::any('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ==========================================
// 2. ZONA TERPROTEKSI (AUTHENTICATED ZONES)
// ==========================================
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | HAK AKSES ROLE: OPERATOR / PETUGAS LAPANGAN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Petugas'])->prefix('petugas')->name('petugas.')->group(function () {
        
        // 🚨 LEVEL 1 GATING: Pintu Masuk Absensi Mandiri (Bisa diakses SAAT/SEBELUM absen)
        Route::get('/check-attendance', [AttendanceController::class, 'showCheckForm'])->name('attendance.form');
        Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
        Route::get('/attendance/my-log', [AttendanceController::class, 'myAttendanceLog'])->name('attendance.log');

        // 🚨 LEVEL 2 GATING: Wajib Lolos Validasi Presensi Masuk Hari Ini via Middleware
        Route::middleware(['ensureClockedIn'])->group(function () {
            
            // --- MODUL UTAMA PANEL KONTROL & METRIKS ---
            Route::get('/dashboard', [PatrolController::class, 'index'])->name('dashboard');
            
            // --- MODUL INPUT LOG PATROLI BARU ---
            Route::get('/patrol/create', [PatrolController::class, 'create'])->name('patrol.create');
            Route::post('/patrol/store', [PatrolController::class, 'store'])->name('patrol.store');
            
            // --- MODUL REVISI & KOREKSI DATA ---
            Route::get('/patrol/{id}/edit', [PatrolController::class, 'edit'])->name('patrol.edit')->whereNumber('id');
            Route::put('/patrol/{id}/update', [PatrolController::class, 'update'])->name('patrol.update')->whereNumber('id');
            
            // --- MODUL TRASABILITAS HISTORIS ---
            Route::get('/patrol/history', [PatrolController::class, 'history'])->name('patrol.history');
            Route::get('/patrol/{id}/detail', [PatrolController::class, 'show'])->name('patrol.show')->whereNumber('id');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | HAK AKSES ROLE: ADMINISTRATOR PUSAT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // 🛡️ LEVEL 1 GATING SAFE-ZONE: Formulir Presensi Mandiri Khusus Admin
        Route::get('/check-attendance', [AttendanceController::class, 'showCheckForm'])->name('attendance.form');
        Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
        
        // 🚨 LEVEL 2 GATING ZONE: Wajib Lolos Validasi Kerja Presensi Masuk Hari Ini
        Route::middleware(['ensureClockedIn'])->group(function () {
            
            // --- MODUL UTAMA PANEL PUSAT & STATISTIK GRAFIK ---
            Route::get('/dashboard', [PatrolController::class, 'adminDashboard'])->name('dashboard');

            // --- MANAJEMEN DATA & VALIDASI MATRIX LAPORAN ---
            // Alias 'admin.validasi' disinkronkan untuk kebutuhan tombol menu sidebar
            Route::get('/patrols/all', [PatrolController::class, 'allPatrols'])->name('validasi');
            Route::post('/patrol/{id}/update-status', [PatrolController::class, 'updateStatus'])->name('patrol.update-status')->whereNumber('id');
            Route::delete('/patrol/{id}/delete', [PatrolController::class, 'destroy'])->name('patrol.delete')->whereNumber('id');
            
            // --- MODUL FOLDER ARSIP & DISTRIBUSI ELEKTRONIK ---
            Route::get('/patrol/{id}/generate-pdf', [PatrolController::class, 'generatePdf'])->name('patrol.pdf')->whereNumber('id');
            
            // Rute Distribusi Laporan Elektronik (SMTP Setup) - Mengakomodasi tombol 'admin.smtp'
            Route::get('/smtp/distribution', [PatrolController::class, 'showSmtpSettings'])->name('smtp');
            Route::post('/patrol/{id}/distribute-email', [PatrolController::class, 'distributeEmail'])->name('patrol.distribute')->whereNumber('id');
            
            // Alias 'admin.folder_virtual' disinkronkan untuk kebutuhan tombol menu sidebar
            Route::get('/archives/folders', [PatrolController::class, 'archiveFolders'])->name('folder_virtual');
            
            // --- MODUL MANAJEMEN MASTER DATA KLASTER ---
            // Menggunakan penamaan eksplisit agar sinkron dengan requestIs('admin.master_opd') di layout
            Route::resource('master-opd', MasterOpdController::class)->names([
                'index' => 'master_opd'
            ]);
            Route::resource('master-kategori', MasterKategoriController::class);

            // --- MONITORING LOG REKAP ABSENSI GLOBAL ---
            // Alias 'admin.absensi' disinkronkan untuk kebutuhan pemantauan log absensi petugas di sidebar
            Route::get('/attendances', [AttendanceController::class, 'index'])->name('absensi');
            Route::get('/attendances/export-excel', [AttendanceController::class, 'exportExcel'])->name('attendance.export');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | INTELLIGENT FALLBACK REDIRECTOR
    |--------------------------------------------------------------------------
    */
    Route::get('/home', function () {
        $user = auth()->user();
        if (strcasecmp($user->role, 'Admin') === 0) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('petugas.dashboard');
    });
});

/*
|--------------------------------------------------------------------------
| ZONA PROTEKSI FALLBACK GLOBAL (404 SAFETY NET)
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (auth()->check()) {
        return auth()->user()->role === 'Admin' 
            ? redirect()->route('admin.dashboard')->with('error', 'Enkripsi Peringatan: Jalur URL tidak ditemukan.')
            : redirect()->route('petugas.dashboard')->with('error', 'Enkripsi Peringatan: Jalur URL tidak ditemukan.');
    }
    return redirect()->route('login')->with('error', 'Akses Ditolak: Alamat URL berada di luar enkripsi SIP-O-SIBER.');
});