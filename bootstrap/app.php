<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 🛠️ 1. PENDAFTARAN ALIAS MIDDLEWARE (Mengatasi Error Target Class Not Found)
        $middleware->alias([
            'checkRole'       => \App\Http\Middleware\CheckRole::class,
            'ensureClockedIn' => \App\Http\Middleware\EnsureClockedIn::class,
        ]);

        // 🔄 2. PENGALIHAN OTOMATIS JIKA PENGGUNA BELUM LOGIN (GUEST REDIRECT)
        $middleware->redirectGuestsTo(fn (Request $request) => route('login'));

        // 🛡️ 3. KONFIGURASI KEAMANAN TAMBAHAN (Pengecualian CSRF jika dibutuhkan API Luar)
        $middleware->validateCsrfTokens(except: [
            // Tambahkan URL di sini jika ada webhook instansi luar yang tidak memerlukan token CSRF
            // 'api/v1/webhook/diskominfo/*',
        ]);

        // 🔒 4. MENGAMANKAN STATE STATE KHUSUS COOKIE
        $middleware->encryptCookies(except: [
            // 'nama_cookie_tanpa_enkripsi'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        
        // 🚨 5. MANAJEMEN PENGECUALIAN & RESPONS EROR SECARA PROFESIONAL
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Sesi otentikasi tidak valid atau telah berakhir.'
                ], 401);
            }
            
            return redirect()->guest(route('login'))
                ->with('error', 'Silakan masuk ke sistem terlebih dahulu untuk memverifikasi hak akses Anda.');
        });
        
    })->create();