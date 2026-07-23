<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\Admin\MasterOpdController;
use App\Http\Controllers\Admin\MasterKategoriController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Pelaporan & Operasional Siber (SIP-O-SIBER)
| Dinas Komunikasi, Informatika dan Statistik Provinsi Lampung
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. ZONA UMUM / PENGUNJUNG (GUEST ZONES) - DILINDUNGI THROTTLE
// =========================================================================
Route::middleware(['guest'])->group(function () {
    
    // Gerbang Masuk Otentikasi Terpusat
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm']);
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');

    // Alur Registrasi Akun Mandiri Personel Baru
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.post');

    // Alur Lupa Password / Reset Kredensial Mandiri
    Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    Route::post('/lupa-password', [AuthController::class, 'sendResetLinkEmail'])
        ->middleware('throttle:3,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');

    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

// Sesi Pemutusan Autentikasi (Logout)
Route::middleware(['auth'])->group(function () {
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
});

// =========================================================================
// 2. ZONA TERPROTEKSI (AUTHENTICATED ZONES)
// =========================================================================
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | HAK AKSES ROLE: OPERATOR / PETUGAS LAPANGAN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Petugas'])->prefix('petugas')->name('petugas.')->group(function () {
        
        // Dashboard Utama Petugas Lapangan
        Route::get('/dashboard', [LaporanController::class, 'index'])->name('dashboard');

        // Fitur Absensi Mandiri Petugas (Ditambahkan Alias / Route Pendukung)
        Route::controller(PresensiController::class)->group(function () {
            Route::get('/absensi-petugas', 'showCheckForm')->name('attendance.form');
            Route::get('/absensi/form', 'showCheckForm')->name('absensi.form'); // Alias untuk panggilan AJAX modal
            Route::post('/attendance/store', 'store')->middleware('throttle:10,1')->name('attendance.store');
            Route::get('/attendance/my-log', 'myAttendanceLog')->name('attendance.log');
        });

        // Modul Operasional & Laporan Petugas Lapangan
        Route::controller(LaporanController::class)->group(function () {
            // Modul Input Log Patroli/Laporan Baru (Alias ditambahkan agar kompatibel)
            Route::get('/patrol/create', 'create')->name('patrol.create');
            Route::get('/laporan/create', 'create')->name('laporan.create'); // Alias untuk menu Navbar
            Route::post('/patrol/store', 'store')->middleware('throttle:10,1')->name('patrol.store');
            
            // Modul Revisi & Koreksi Data Insiden Ditolak/Ditangguhkan
            Route::get('/patrol/{id}/edit', 'edit')->name('patrol.edit')->whereNumber('id');
            Route::put('/patrol/{id}/update', 'update')->middleware('throttle:10,1')->name('patrol.update')->whereNumber('id');
            
            // Modul Trasabilitas Historis Log & Detail Insiden (Alias ditambahkan agar kompatibel)
            Route::get('/patrol/history', 'history')->name('patrol.history');
            Route::get('/riwayat-log-petugas', 'history')->name('riwayat-log-petugas'); // Alias untuk menu Navbar
            Route::get('/patrol/{id}/detail', 'show')->name('patrol.show')->whereNumber('id');
        });

        // Modul Cetak PDF Laporan Petugas
        Route::controller(LaporanController::class)->prefix('laporan')->name('laporan.')->group(function () {
            Route::get('/patroli/pdf/{id}', 'cetakPatroliPdf')->name('patroli.pdf')->whereNumber('id');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | HAK AKSES ROLE: ADMINISTRATOR PUSAT
    |--------------------------------------------------------------------------
    */
    Route::middleware(['checkRole:Admin'])->prefix('admin')->name('admin.')->group(function () {
        
        // Dashboard Utama Administrator Pusat
        Route::get('/dashboard', [LaporanController::class, 'adminDashboard'])->name('dashboard');

        // Fitur Absensi Mandiri Administrator
        Route::controller(PresensiController::class)->group(function () {
            Route::get('/absensi-admin', 'showCheckForm')->name('attendance.form');
            Route::post('/attendance/store', 'store')->middleware('throttle:10,1')->name('attendance.store');
            Route::get('/attendances', 'index')->name('absensi');
        });

        // Modul Manajemen, Validasi Matrix & Operasional Admin
        Route::controller(LaporanController::class)->group(function () {
            // Manajemen Data & Validasi Status Matrix Laporan
            Route::get('/patrols/all', 'allPatrols')->name('validasi');
            Route::post('/patrol/{id}/update-status', 'updateStatus')->name('patrol.update-status')->whereNumber('id');
            Route::delete('/patrol/{id}/delete', 'destroy')->name('patrol.delete')->whereNumber('id');
            
            // Modul Distribusi Notifikasi Elektronik (SMTP)
            Route::get('/smtp/distribution', 'showSmtpSettings')->name('smtp');
            Route::post('/patrol/{id}/distribute-email', 'distributeEmail')->middleware('throttle:5,1')->name('patrol.distribute')->whereNumber('id');
            
            // Modul Manajerial Folder Virtual & Arsip Berkas
            Route::get('/archives/folders', 'archiveFolders')->name('folder_virtual');
        });

        // Modul Pusat Pelaporan, Ekspor Excel & Cetak PDF Rekapitulasi
        Route::controller(LaporanController::class)->prefix('laporan')->name('laporan.')->group(function () {
            // Rekap & Ekspor Laporan Patroli
            Route::get('/patroli', 'indexPatroli')->name('patroli.index');
            Route::get('/patroli/pdf/{id}', 'cetakPatroliPdf')->name('patroli.pdf')->whereNumber('id');
            Route::get('/patroli/export-excel', 'exportPatroliExcel')->middleware('throttle:10,1')->name('patroli.excel');
            Route::get('/patroli/rekap-pdf', 'rekapPatroliPdf')->middleware('throttle:10,1')->name('patroli.rekap-pdf');

            // Rekap & Ekspor Laporan Presensi Personel
            Route::get('/presensi', 'indexPresensi')->name('presensi.index');
            Route::get('/presensi/export-excel', 'exportPresensiExcel')->middleware('throttle:10,1')->name('presensi.excel');
            Route::get('/presensi/rekap-pdf', 'rekapPresensiPdf')->middleware('throttle:10,1')->name('presensi.rekap-pdf');
        });
        
        // Modul Manajemen Master Data Klaster (Resource Routes)
        Route::resources([
            'master-opd'      => MasterOpdController::class,
            'master-kategori' => MasterKategoriController::class,
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | INTELLIGENT FALLBACK REDIRECTOR
    |--------------------------------------------------------------------------
    */
    Route::get('/home', function () {
        $user = auth()->user();
        if ($user && strcasecmp($user->role, 'Admin') === 0) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('petugas.dashboard');
    })->name('home');
});

// =========================================================================
// 3. ZONA PROTEKSI FALLBACK GLOBAL (404 SAFETY NET)
// =========================================================================
Route::fallback(function () {
    if (auth()->check()) {
        $user = auth()->user();
        $targetRoute = (strcasecmp($user->role, 'Admin') === 0) ? 'admin.dashboard' : 'petugas.dashboard';

        return redirect()->route($targetRoute)
            ->with('error', 'Enkripsi Peringatan: Jalur URL yang Anda tuju tidak ditemukan dalam repositori sistem.');
    }

    return redirect()->route('login')
        ->with('error', 'Akses Ditolak: Silakan autentikasi kredensial Anda untuk mengakses modul SIP-O-SIBER.');
});