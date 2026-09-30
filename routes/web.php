<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\MasterOpdController;
use App\Http\Controllers\Admin\ArchiveFolderController;

/*
|--------------------------------------------------------------------------
| WEB ROUTES SIP-O-SIBER (FINAL & KOMPLEKS)
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| GUEST (Belum Login)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [AuthController::class, 'showLoginForm']);

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.post');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,1')
        ->name('register.post');

    /* Lupa & Reset Password */
    Route::get('/lupa-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    Route::post('/lupa-password', [AuthController::class, 'sendResetLinkEmail'])
        ->name('password.email');
});

/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])
        ->name('logout');
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | PETUGAS
    |--------------------------------------------------------------------------
    */
    Route::prefix('petugas')
        ->middleware('checkRole:Petugas')
        ->name('petugas.')
        ->group(function () {

            Route::get('/dashboard', [LaporanController::class, 'index'])->name('dashboard');

            /* Presensi Petugas */
            Route::get('/absensi', [PresensiController::class, 'showCheckForm'])->name('attendance.form');
            Route::post('/attendance/store', [PresensiController::class, 'store'])->name('attendance.store');
            Route::get('/attendance/history', [PresensiController::class, 'myAttendanceLog'])->name('attendance.log');

            /* Patroli & Laporan Petugas */
            Route::get('/patrol/create', [LaporanController::class, 'create'])->name('patrol.create');
            Route::post('/patrol/store', [LaporanController::class, 'store'])->name('patrol.store');
            Route::get('/patrol/history', [LaporanController::class, 'history'])->name('patrol.history');
            Route::get('/patrol/{id}', [LaporanController::class, 'show'])->name('patrol.show');
            Route::get('/patrol/{id}/edit', [LaporanController::class, 'edit'])->name('patrol.edit');
            Route::put('/patrol/{id}', [LaporanController::class, 'update'])->name('patrol.update');
            
            /* Export PDF Laporan Petugas */
            Route::get('/laporan/patroli/pdf/{id}', [LaporanController::class, 'cetakPatroliPdf'])->name('laporan.patroli.pdf');
        });

    /*
    |--------------------------------------------------------------------------
    | ADMIN
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')
        ->middleware('checkRole:Admin')
        ->name('admin.')
        ->group(function () {

            /* Dashboard & Profil Admin */
            Route::get('/dashboard', [LaporanController::class, 'adminDashboard'])->name('dashboard');
            Route::get('/profil', [LaporanController::class, 'profilAdmin'])->name('profil');
            Route::put('/profil', [LaporanController::class, 'updateProfilAdmin'])->name('profil.update');

            /* Presensi Admin & Monitoring */
            Route::get('/absensi-admin', [PresensiController::class, 'showCheckForm'])->name('attendance.form');
            Route::post('/attendance/store', [PresensiController::class, 'store'])->name('attendance.store');
            Route::get('/attendances', [PresensiController::class, 'index'])->name('attendance.index');
            Route::get('/attendances/export', [PresensiController::class, 'exportCsv'])->name('attendance.export');

            /* Validasi & Manajemen Patroli */
            Route::get('/patrols/all', [LaporanController::class, 'allPatrols'])->name('validasi');
            Route::get('/patrol/{id}', [LaporanController::class, 'show'])->name('patrol.show');
            Route::post('/patrol/{id}/update-status', [LaporanController::class, 'updateStatus'])->name('patrol.update-status');
            Route::get('/patrol/{id}/pdf', [LaporanController::class, 'cetakPdf'])->name('patrol.pdf');
            Route::delete('/patrol/{id}', [LaporanController::class, 'destroy'])->name('patrol.delete');

            /* MANAJEMEN FOLDER VIRTUAL ARSIP */
            Route::prefix('archives/folders')->name('folders.')->group(function () {
                Route::get('/', [ArchiveFolderController::class, 'index'])->name('index');
                Route::post('/', [ArchiveFolderController::class, 'store'])->name('store');
                Route::get('/{id}', [ArchiveFolderController::class, 'show'])->name('show');
                Route::put('/{id}', [ArchiveFolderController::class, 'update'])->name('update');
                Route::delete('/{id}', [ArchiveFolderController::class, 'destroy'])->name('destroy');
                
                // File operations
                Route::post('/{id}/upload', [ArchiveFolderController::class, 'uploadFile'])->name('upload');
                Route::delete('/{folderId}/files/{fileId}', [ArchiveFolderController::class, 'deleteFile'])->name('delete-file');
                
                // Import laporan
                Route::post('/{id}/import-laporan', [ArchiveFolderController::class, 'importLaporan'])->name('import-laporan');
            });

            /* SMTP Settings & Email Distribution Simulation */
            Route::get('/smtp', [LaporanController::class, 'showSmtpSettings'])->name('smtp');
            Route::post('/smtp/distribute/{id}', [LaporanController::class, 'distributeEmail'])->name('patrol.distribute');
            Route::post('/smtp/send-test', [LaporanController::class, 'sendTestEmail'])->name('smtp.send-test');

            /* MANAJEMEN MASTER OPD (Email, Sosmed, Aplikasi) */
            Route::prefix('master-opd')->name('master-opd.')->group(function () {

                // Halaman Utama Tabs (Generates Name: admin.master-opd.index)
                Route::get('/', [MasterOpdController::class, 'index'])->name('index');

                // Route Email OPD
                Route::post('/email/store', [MasterOpdController::class, 'storeEmail'])->name('email.store');
                Route::put('/email/{id}', [MasterOpdController::class, 'updateEmail'])->name('email.update');
                Route::delete('/email/{id}', [MasterOpdController::class, 'destroyEmail'])->name('email.destroy');
                Route::get('/email/export', [MasterOpdController::class, 'exportEmailCsv'])->name('email.export');

                // Route Media Sosial OPD
                Route::post('/sosmed/store', [MasterOpdController::class, 'storeSosmed'])->name('sosmed.store');
                Route::put('/sosmed/{id}', [MasterOpdController::class, 'updateSosmed'])->name('sosmed.update');
                Route::delete('/sosmed/{id}', [MasterOpdController::class, 'destroySosmed'])->name('sosmed.destroy');
                Route::get('/sosmed/export', [MasterOpdController::class, 'exportSosmedCsv'])->name('sosmed.export');

                // Route Aplikasi Pemprov
                Route::post('/aplikasi/store', [MasterOpdController::class, 'storeAplikasi'])->name('aplikasi.store');
                Route::put('/aplikasi/{id}', [MasterOpdController::class, 'updateAplikasi'])->name('aplikasi.update');
                Route::delete('/aplikasi/{id}', [MasterOpdController::class, 'destroyAplikasi'])->name('aplikasi.destroy');
                Route::get('/aplikasi/export', [MasterOpdController::class, 'exportAplikasiCsv'])->name('aplikasi.export');
            });

            /* Rekap Laporan & Presensi (PDF / Excel Export) */
            Route::prefix('laporan')->name('laporan.')->group(function () {
                Route::get('/patroli', [LaporanController::class, 'indexPatroli'])->name('patroli.index');
                Route::get('/patroli/pdf/{id}', [LaporanController::class, 'cetakPatroliPdf'])->name('patroli.pdf');
                Route::get('/patroli/excel', [LaporanController::class, 'exportPatroliExcel'])->name('patroli.excel');
                Route::get('/patroli/rekap-pdf', [LaporanController::class, 'rekapPatroliPdf'])->name('patroli.rekap-pdf');

                Route::get('/presensi', [LaporanController::class, 'indexPresensi'])->name('presensi.index');
                Route::get('/presensi/excel', [LaporanController::class, 'exportPresensiExcel'])->name('presensi.excel');
                Route::get('/presensi/rekap-pdf', [LaporanController::class, 'rekapPresensiPdf'])->name('presensi.rekap-pdf');
            });

        });

    /*
    |--------------------------------------------------------------------------
    | HOME DIRECT
    |--------------------------------------------------------------------------
    */
    Route::get('/home', function () {
        return strcasecmp(auth()->user()->role, 'Admin') === 0
            ? redirect()->route('admin.dashboard')
            : redirect()->route('petugas.dashboard');
    })->name('home');

});

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTE
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    if (auth()->check()) {
        return strcasecmp(auth()->user()->role, 'Admin') === 0
            ? redirect()->route('admin.dashboard')
            : redirect()->route('petugas.dashboard');
    }

    return redirect()->route('login');
});