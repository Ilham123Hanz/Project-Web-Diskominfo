<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Enkripsi Masuk - SIP-O-SIBER Diskominfo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            /* 🎨 PALET WARNA GRADASI RESMI DISKOMINFO - TEDUH & PROFESIONAL */
            --kominfo-dark: #0A1D37;      /* Biru Gelap Utama */
            --kominfo-navy: #102A43;      /* Navy Sekunder */
            --kominfo-blue: #0052A3;      /* Biru Identitas Kominfo */
            --kominfo-light-blue: #0070E0;/* Aksen Cyber */
            --amber-gold: #FFB800;        /* Emas Tipis Pembatas */
            --text-dark: #1E293B;
            --text-muted: #64748B;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #F8FAFC;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
            color: var(--text-dark);
        }

        /* 🌓 IMPLEMENTASI SPLIT LAYOUT DESIGN */
        .split-layout {
            min-height: 100vh;
            display: flex;
        }

        /* Sisi Kiri: Branding Visual Siber (Gradasi Kominfo Teduh) */
        .brand-panel {
            flex: 1;
            background: linear-gradient(135deg, var(--kominfo-dark) 0%, var(--kominfo-navy) 50%, var(--kominfo-blue) 100%);
            display: flex;
            flex-direction: column; /* Perbaikan dari flex-column */
            justify-content: center;
            align-items: center;
            padding: 60px;
            position: relative;
            color: #ffffff;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: radial-gradient(rgba(0, 112, 224, 0.2) 0.8px, transparent 0.8px);
            background-size: 20px 20px;
            opacity: 0.25;
            z-index: 1;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 460px;
        }

        /* BOOSTER: Pembungkus Ikon Dibuat Lebih Berkelas */
        .icon-container {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
        }

        /* BOOSTER: Teks Deskripsi Kiri Diperbesar & Dipertebal */
        .brand-description {
            font-size: 1.05rem; 
            font-weight: 500;
            line-height: 1.7;
            color: #E2E8F0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Sisi Kanan: Form Autentikasi Bersih */
        .form-panel {
            flex: 1;
            background-color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px 80px;
            border-left: 6px solid var(--amber-gold);
        }

        .form-container {
            width: 100%;
            max-width: 460px;
        }

        /* BOOSTER: Judul Form Dipertegas */
        .form-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--kominfo-dark);
        }

        .form-subtitle {
            font-size: 1rem;
            color: var(--text-muted);
        }

        /* BOOSTER: Label Form Diperbesar & Dipertebal */
        .form-label-custom {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 8px;
        }

        /* ⚡ FOCUS STATE INDICATION */
        .focus-cyber {
            background-color: #F8FAFC !important;
            border: 1.5px solid #CBD5E1 !important;
            padding: 14px 14px 14px 46px !important;
            border-radius: 8px !important;
            font-size: 1rem !important;
            color: var(--text-dark) !important;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .focus-cyber:focus {
            background-color: #ffffff !important;
            border-color: var(--kominfo-blue) !important;
            box-shadow: 0 0 0 4px rgba(0, 82, 163, 0.15) !important;
        }

        .input-group-custom {
            position: relative;
        }

        .input-icon-left {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            z-index: 5;
            font-size: 1.15rem;
            transition: color 0.25s;
        }

        .input-group-custom:focus-within .input-icon-left {
            color: var(--kominfo-blue);
        }

        /* 🔐 TOGGLE PASSWORD VISIBILITY */
        .password-toggle-btn {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 10px;
            color: #94A3B8;
            z-index: 5;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .password-toggle-btn:hover {
            color: var(--text-dark);
        }

        .btn-cyber-submit {
            background-color: var(--kominfo-dark);
            color: #ffffff;
            font-weight: 600;
            font-size: 1.05rem;
            padding: 14px;
            border: none;
            border-radius: 8px;
            transition: all 0.25s ease;
        }

        .btn-cyber-submit:hover {
            background-color: var(--kominfo-blue);
            box-shadow: 0 4px 14px rgba(0, 82, 163, 0.3);
        }

        /* BOOSTER: Box Warning Diperbesar, Tebal, dan Menggunakan Teks Rata Kiri-Kanan Yang Rapi */
        .security-alert-box {
            font-size: 0.9rem;
            font-weight: 600;
            color: #991B1B;
            background-color: #FEF2F2;
            border: 1px solid #FCA5A5;
            border-left: 4px solid #EF4444;
            padding: 16px;
            border-radius: 8px;
            line-height: 1.6;
            text-align: justify;
        }

        /* Link Navigation Sisi Kanan */
        .link-custom {
            color: var(--kominfo-blue);
            font-weight: 700;
            transition: color 0.2s;
        }
        .link-custom:hover {
            color: var(--kominfo-dark);
        }

        /* Responsive Breakpoints */
        @media (max-width: 992px) {
            .brand-panel { display: none !important; }
            .form-panel { border-left: none; padding: 40px 24px; }
        }
    </style>
</head>
<body>

    <div class="split-layout">
        
        <!-- SISI KIRI: BRANDING VISUAL SIBER -->
        <div class="brand-panel">
            <div class="brand-content">
                <div class="mb-4">
                    <span class="icon-container d-inline-block shadow-lg">
                        <i class="fas fa-shield-halved fa-4x text-info"></i>
                    </span>
                </div>
                <h2 class="fw-extrabold tracking-wider text-uppercase mb-2" style="font-size: 2.5rem; letter-spacing: 2px; font-weight: 800;">SIP-O-SIBER</h2>
                <div class="badge bg-info bg-opacity-20 text-info px-3 py-2 border border-info border-opacity-30 rounded-pill mb-4 small fw-semibold">
                    PRODUCTION-READY v2.4
                </div>
                <p class="brand-description px-2">
                    Sistem Operasional Terintegrasi Pangkalan Data Monitoring Keamanan Informasi & Log Pengawasan Patroli Siber Dinas Kominfo Provinsi Lampung.
                </p>
            </div>
        </div>

        <!-- SISI KANAN: FORM AUTENTIKASI -->
        <div class="form-panel">
            <div class="form-container">
                
                <div class="mb-4 text-center text-lg-start">
                    <h3 class="form-title mb-1">Otorisasi Kredensial</h3>
                    <p class="form-subtitle">Silakan masuk menggunakan akun siber Anda</p>
                </div>

                @if(session('success'))
                    <div class="alert alert-success border-0 text-success small fw-bold bg-success bg-opacity-10 mb-4 p-3 rounded-3 shadow-sm d-flex align-items-center">
                        <i class="fas fa-check-circle me-2 fs-5"></i>
                        <div>{{ session('success') }}</div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger border-0 text-danger bg-danger bg-opacity-10 mb-4 p-3 rounded-3 shadow-sm">
                        <div class="d-flex align-items-center fw-bold mb-1">
                            <i class="fas fa-triangle-exclamation me-2 fs-5"></i>
                            <span>Otorisasi Akses Gagal!</span>
                        </div>
                        <ul class="mb-0 ps-3 mt-1" style="font-size: 0.88rem; font-weight: 500;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" autocomplete="off" class="needs-validation" novalidate>
                    @csrf
                    
                    <!-- Input Nama Pengguna -->
                    <div class="mb-3 position-relative">
                        <label class="form-label-custom">Nama Pengguna (NPM/NIP)
                        <div class="input-group-custom">
                            <i class="fas fa-user input-icon-left"></i>
                            <input type="text" name="username" 
                                   class="form-control focus-cyber w-100 @error('username') is-invalid @enderror" 
                                   required placeholder="Masukkan username dinas Anda" 
                                   value="{{ old('username') }}" autofocus>
                        </div>
                    </div>
                    
                    <!-- Input Kata Sandi -->
                    <div class="mb-3 mt-3">
                        <label class="form-label-custom">Kata Sandi (Password)</label>
                        <div class="input-group-custom">
                            <i class="fas fa-lock input-icon-left"></i>
                            <input type="password" name="password" id="passwordField"
                                   class="form-control focus-cyber w-100 @error('password') is-invalid @enderror" 
                                   required placeholder="••••••••">
                            <button type="button" class="password-toggle-btn" onclick="togglePasswordVisibility()" aria-label="Tampilkan kata sandi">
                                <i class="fas fa-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pilihan Ingat Saya & Lupa Password -->
                    <div class="d-flex justify-content-between align-items-center mt-3 mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="rememberMe" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label text-muted fw-bold" style="cursor: pointer; user-select: none; font-size: 0.95rem;" for="rememberMe">
                                Ingat Saya
                            </label>
                        </div>
                        <a href="#" class="text-decoration-none link-custom" style="font-size: 0.95rem;">Lupa Password?</a>
                    </div>

                    <!-- Tombol Submit Utama -->
                    <button type="submit" class="btn btn-cyber-submit w-100 mb-3 shadow-sm">
                        <i class="fas fa-shield-lock me-2"></i> Enkripsi Masuk Ke Sistem
                    </button>
                </form>

                <div class="text-center text-muted small my-3 fw-bold" style="font-size: 0.8rem; letter-spacing: 0.5px;">ATAU JALUR MASUK ALTERNATIF</div>

                <!-- 🌐 PERBAIKAN UTAMA: TOMBOL GOOGLE KEMBALI KE TAMPILAN AWAL ASLI DENGAN LOGO TERJAMIN AMAN -->
                <a href="#" class="btn btn-outline-secondary w-100 py-3 rounded-3 fw-bold d-flex align-items-center justify-content-center border-secondary-subtle text-dark text-decoration-none mb-4" style="font-size: 0.95rem; background-color: #FAFAFA;">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google Logo" width="20" class="me-2">
                    Masuk Menggunakan Google
                </a>

                <!-- Navigasi Registrasi Akun -->
                <div class="text-center pt-3 border-top border-slate-200" style="font-size: 0.95rem;">
                    <span class="text-muted fw-medium">Petugas Baru Lapangan?</span> 
                    <a href="{{ route('register') }}" class="text-decoration-none ms-1 link-custom">
                        Registrasi Akun Mandiri <i class="fas fa-arrow-right small ms-1"></i>
                    </a>
                </div>

                <!-- PERBAIKAN UTAMA: BOX AUDIT TRAIL TEKS BESAR, TEBAL & JELAS -->
                <div class="security-alert-box mt-4">
                    <i class="fas fa-triangle-exclamation me-1 text-danger"></i> <strong>AUDIT TRAIL LOGGING ACTIVE:</strong> Kawasan digital berikat terbatas. Segala upaya akses ilegal, <em>credential stuffing</em>, maupun manipulasi parameter transmisi akan direkam bersama alamat IP (<em>IPv4/IPv6</em>) Anda dan dilaporkan langsung ke Tim Keamanan Jaringan.
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fitur 2: Interaktif Toggle Show/Hide Password
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

        // Bootstrap Native Form Client-side Validation Trigger
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