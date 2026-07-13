<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatrolController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Informasi Pelaporan & Operasional Siber (SIP-O-SIBER)
|--------------------------------------------------------------------------
*/

// Halaman Utama Langsung Menampilkan Form Login (Menghindari Pengalihan Berulang / Loop)
Route::get('/', function () {
    return view('auth.login');
});

// Jalur Autentikasi & Registrasi (Hanya untuk Tamu / Belum Login)
Route::middleware(['guest'])->group(function () {
    // Jalur Masuk Sistem (Login)
    Route::get('/login', function () { 
        return view('auth.login'); 
    })->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // Jalur Registrasi Mandiri Petugas Baru
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Proses Putus Sesi (Logout) - Ditempatkan di Luar Middleware Auth untuk Kemudahan Reset Sesi
Route::get('/logout', [AuthController::class, 'logout'])->name('logout'); 

// Jalur Terproteksi Sistem (Harus Login Terlebih Dahulu)
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | OTORITAS: OPERATOR / PETUGAS LAPANGAN
    |--------------------------------------------------------------------------
    | Menggunakan parameter 'Petugas' sesuai dengan nilai role di database.
    */
    Route::middleware(['checkRole:Petugas'])->group(function () {
        // 1. HALAMAN PENGADANGAN / CHECKPOINT (Standalone Form Absen Mandiri)
        Route::get('/petugas/check-attendance', [AttendanceController::class, 'showCheckForm'])->name('petugas.attendance.form');
        
        // 2. PROSES TRANSMISI LOG PRESENSI HARIAN (MANUAL)
        Route::post('/petugas/attendance', [AttendanceController::class, 'store'])->name('petugas.attendance.store');
        
        // 3. HALAMAN UTAMA PANEL KONTROL PETUGAS
        Route::get('/petugas/dashboard', [PatrolController::class, 'index'])->name('petugas.dashboard');
        
        // 4. TRANSMISI UNGGAH BERKAS LAPORAN KERJA KE SISTEM
        Route::post('/petugas/patrol/store', [PatrolController::class, 'store'])->name('petugas.patrol.store');
    });

    /*
    |--------------------------------------------------------------------------
    | OTORITAS: ADMINISTRATOR PUSAT
    |--------------------------------------------------------------------------
    | Menggunakan parameter 'Admin' untuk mengunci seluruh hak akses internal admin.
    */
    Route::middleware(['checkRole:Admin'])->group(function () {
        // 1. HALAMAN GERBANG ABSENSI KHUSUS ADMIN (Agar Tampilan Tidak Tertukar)
        Route::get('/admin/check-attendance', [AttendanceController::class, 'showCheckForm'])->name('admin.attendance.form');
        Route::post('/admin/attendance', [AttendanceController::class, 'store'])->name('admin.attendance.store');

        // 2. HALAMAN UTAMA PANEL KONTROL PUSAT ADMIN
        Route::get('/admin/dashboard', [PatrolController::class, 'adminDashboard'])->name('admin.dashboard');
        
        // Validasi Matrix & Koreksi Laporan Petugas
        Route::post('/admin/patrol/{id}/update-status', [PatrolController::class, 'updateStatus'])->name('admin.patrol.update-status');
        
        // Penghapusan Berkas Laporan dari Log Jaringan
        Route::delete('/admin/patrol/{id}/delete', [PatrolController::class, 'destroy'])->name('admin.patrol.delete');
        
        // Pemantauan Log Rekap Absensi Seluruh Petugas
        Route::get('/admin/attendances', [AttendanceController::class, 'index'])->name('admin.attendances');
    });

});