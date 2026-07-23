<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Enkripsi Masuk - SIP-O-SIBER Diskominfo</title>

    <!-- Google Fonts, Bootstrap 5 & FontAwesome -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            /* 🎨 PALET WARNA RESMI DISKOMINFO PROVINSI LAMPUNG */
            --kominfo-dark: #06111E;       /* Darkest Navy */
            --kominfo-navy: #0B1D33;       /* Main Navy */
            --kominfo-blue: #0052A3;       /* Biru Kominfo */
            --cyber-cyan: #00D2FF;         /* Accent Light Blue */
            --amber-gold: #FFB800;         /* Emas Aksen */
            --text-dark: #0F172A;
            --text-muted: #64748B;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        body {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2.5rem 1rem; 
            /* 🌌 BACKGROUND GRADIENT SEAMLESS CYBER */
            background: radial-gradient(circle at 15% 15%, #0A2E5C 0%, #061528 50%, #030A14 100%);
            position: relative;
            color: var(--text-dark);
            background-attachment: fixed;
            overflow-x: hidden;
        }

        /* 🌐 MOTIF CYBER GRID BACKGROUND UNIFORM */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: 
                radial-gradient(rgba(0, 210, 255, 0.22) 1px, transparent 1px),
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 24px 24px, 32px 32px, 32px 32px;
            z-index: 1;
            animation: cyberPulse 6s infinite alternate ease-in-out;
            pointer-events: none;
        }

        /* Ambient Glow Effects */
        .cyber-glow-topleft {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 112, 224, 0.35) 0%, rgba(0,0,0,0) 70%);
            top: -120px;
            left: -120px;
            z-index: 2;
            pointer-events: none;
            animation: orbFloat 8s infinite alternate ease-in-out;
        }

        .cyber-glow-bottomright {
            position: fixed;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(0, 210, 255, 0.2) 0%, rgba(255, 184, 0, 0.1) 45%, rgba(0,0,0,0) 70%);
            bottom: -140px;
            right: -140px;
            z-index: 2;
            pointer-events: none;
            animation: orbFloat 10s infinite alternate-reverse ease-in-out;
        }

        @keyframes cyberPulse {
            0% { opacity: 0.4; }
            50% { opacity: 0.85; }
            100% { opacity: 0.5; }
        }

        @keyframes orbFloat {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(30px, 25px) scale(1.08); }
        }

        /* 📦 CONTAINER & CARD LAYOUT */
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            margin: auto;
        }

        .login-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 25px rgba(0, 210, 255, 0.18);
            padding: 30px 32px 26px 32px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        /* Aksen Border Gold, Blue, & Cyan Atas Card */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--amber-gold), var(--kominfo-blue), var(--cyber-cyan));
        }

        /* Header Logo & Icon */
        .brand-header-icon {
            width: 50px;
            height: 50px;
            background: rgba(0, 82, 163, 0.08);
            border: 1.5px solid rgba(0, 82, 163, 0.18);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0, 82, 163, 0.08);
        }

        .brand-title {
            font-weight: 800;
            letter-spacing: 0.5px;
            color: var(--kominfo-dark);
            font-size: 1.35rem;
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 0.8125rem;
            color: var(--text-muted);
            line-height: 1.45;
            font-weight: 500;
        }

        /* Label Form & Input Formatting (Jarak Lega & Jelas) */
        .form-group-custom {
            margin-bottom: 1.25rem; /* Memberikan jarak antar input agar tidak dempet */
        }

        .form-label-custom {
            font-size: 0.825rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 7px;
            display: block;
        }

        .input-group-custom {
            position: relative;
        }

        .input-icon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            z-index: 5;
            font-size: 0.95rem;
            transition: color 0.2s ease;
        }

        .focus-cyber {
            background-color: #F8FAFC !important;
            border: 1.5px solid #CBD5E1 !important;
            padding: 10px 14px 10px 42px !important; /* Padding pas & tinggi ~44px */
            border-radius: 10px !important;
            font-size: 0.875rem !important;
            color: var(--text-dark) !important;
            font-weight: 500;
            height: 44px;
            transition: all 0.2s ease-in-out;
        }

        .focus-cyber:focus {
            background-color: #FFFFFF !important;
            border-color: var(--kominfo-blue) !important;
            box-shadow: 0 0 0 4px rgba(0, 82, 163, 0.12) !important;
        }

        .input-group-custom:focus-within .input-icon-left {
            color: var(--kominfo-blue);
        }

        /* Toggle Password Button */
        .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 6px;
            color: #94A3B8;
            z-index: 5;
            cursor: pointer;
            font-size: 0.9rem;
            border-radius: 6px;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: var(--kominfo-dark);
        }

        /* Option Checks & Links */
        .form-check-label {
            cursor: pointer;
            user-select: none;
            font-size: 0.8rem;
            color: #475569;
            font-weight: 600;
        }

        .link-custom {
            color: var(--kominfo-blue);
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .link-custom:hover {
            color: var(--kominfo-dark);
        }

        /* Tombol Utama (Submit Button) */
        .btn-cyber-submit {
            background-color: var(--kominfo-navy);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 11px 16px;
            border: none;
            border-radius: 10px;
            height: 46px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(11, 29, 51, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-cyber-submit:hover {
            background-color: var(--kominfo-blue);
            color: #FFFFFF;
            box-shadow: 0 6px 18px rgba(0, 82, 163, 0.35);
            transform: translateY(-1.5px);
        }

        .btn-cyber-submit:active {
            transform: translateY(0);
        }

        /* Warning Box Cyber Security (CSIRT) */
        .security-alert-box {
            font-size: 0.735rem;
            font-weight: 600;
            color: #991B1B;
            background-color: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-left: 4px solid #EF4444;
            padding: 10px 12px;
            border-radius: 8px;
            line-height: 1.45;
            text-align: justify;
        }

        /* Footer Keterangan Sistem */
        .card-inner-footer {
            color: var(--text-muted);
            font-size: 0.735rem;
            text-align: center;
            margin-top: 14px;
            font-weight: 500;
            line-height: 1.4;
        }
    </style>
</head>
<body>

    <!-- Light Ambient Background Effects -->
    <div class="cyber-glow-topleft"></div>
    <div class="cyber-glow-bottomright"></div>

    <div class="login-wrapper">
        <div class="login-card">
            
            <!-- HEADER LOGO & JUDUL SISTEM -->
            <div class="text-center mb-4">
                <div class="brand-header-icon">
                    <i class="fas fa-shield-halved text-primary" style="font-size: 1.35rem;"></i>
                </div>
                <h2 class="brand-title">SIP-O-SIBER</h2>
                <p class="brand-subtitle">Sistem Informasi Pencatatan & Monitoring Keamanan Siber<br>Dinas Kominfo Provinsi Lampung</p>
            </div>

            <!-- NOTIFIKASI SUKSES -->
            @if(session('status') || session('success'))
                <div class="alert alert-success border-0 text-success small fw-bold bg-success bg-opacity-10 mb-3.5 p-2.5 px-3 rounded-3 shadow-sm d-flex align-items-center" style="font-size: 0.8rem;">
                    <i class="fas fa-check-circle me-2 flex-shrink-0" style="font-size: 1rem;"></i>
                    <div>{{ session('status') ?? session('success') }}</div>
                </div>
            @endif

            <!-- NOTIFIKASI ERROR -->
            @if($errors->any())
                <div class="alert alert-danger border-0 text-danger bg-danger bg-opacity-10 mb-3.5 p-2.5 px-3 rounded-3 shadow-sm">
                    <div class="d-flex align-items-center fw-bold mb-1" style="font-size: 0.8rem;">
                        <i class="fas fa-triangle-exclamation me-2 flex-shrink-0" style="font-size: 0.95rem;"></i>
                        <span>Otorisasi Akses Gagal!</span>
                    </div>
                    <ul class="mb-0 ps-3 mt-1" style="font-size: 0.75rem; font-weight: 500;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM LOGIN Utama -->
            <form action="{{ route('login.post') }}" method="POST" autocomplete="off" class="needs-validation" novalidate>
                @csrf
                
                <!-- Input Username / NPM / NIP -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="usernameInput">Nama Pengguna (NPM/NIP)</label>
                    <div class="input-group-custom">
                        <i class="fas fa-user input-icon-left"></i>
                        <input type="text" 
                               id="usernameInput"
                               name="username" 
                               class="form-control focus-cyber w-100 @error('username') is-invalid @enderror" 
                               required 
                               placeholder="Masukkan NIP / NPM Anda" 
                               value="{{ old('username') }}" 
                               autofocus>
                    </div>
                </div>
                
                <!-- Input Password -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="passwordField">Kata Sandi (Password)</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password" 
                               name="password" 
                               id="passwordField"
                               class="form-control focus-cyber w-100 @error('password') is-invalid @enderror" 
                               required 
                               placeholder="••••••••">
                        <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Tampilkan kata sandi">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me & Lupa Password (Diberi Jarak Atas & Bawah) -->
                <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input mt-0 me-1.5" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }} style="transform: scale(0.95); cursor: pointer;">
                        <label class="form-check-label" for="rememberMe">
                            Ingat Saya
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-decoration-none link-custom" style="font-size: 0.8rem;">Lupa Password?</a>
                </div>

                <!-- Tombol Submit Enkripsi Masuk -->
                <button type="submit" class="btn btn-cyber-submit w-100 mb-3.5">
                    <i class="fas fa-shield-lock"></i>
                    <span>Enkripsi Masuk Ke Sistem</span>
                </button>
            </form>

            <!-- Navigasi Registrasi Akun Mandiri -->
            <div class="text-center pt-3 border-top border-slate-200 mb-3" style="font-size: 0.8rem;">
                <span class="text-muted fw-medium">Petugas Baru Lapangan?</span> 
                <a href="{{ route('register') }}" class="text-decoration-none ms-1 link-custom">
                    Registrasi Akun Mandiri <i class="fas fa-arrow-right small ms-1"></i>
                </a>
            </div>

            <!-- Peringatan Keamanan Siber (CSIRT) -->
            <div class="security-alert-box mb-2.5">
                <i class="fas fa-shield-cat me-1 text-danger"></i> 
                <strong>PENGAWASAN KEAMANAN SIBER:</strong> Setiap aktivitas di portal ini dipantau secara <em>real-time</em>. Tindakan akses tanpa izin, peretasan, maupun pelanggaran keamanan siber akan direkam bersama alamat IP Anda dan ditindaklanjuti secara hukum ke Tim Cyber CSIRT terkait.
            </div>

            <!-- FOOTER DALAM CARD -->
            <div class="card-inner-footer">
                © {{ date('Y') }} Diskominfo Provinsi Lampung.<br>Akses Terbatas Internal (Closed System v2.4).
            </div>

        </div>
    </div>

    <!-- Script Bootstrap & Interaktivitas Toggle Password -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById("passwordField");
            const toggleIcon = document.getElementById("togglePasswordIcon");
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        // Bootstrap Native Client-side Validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>