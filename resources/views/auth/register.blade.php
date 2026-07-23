<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Pendaftaran Personel Siber - SIP-O-SIBER Diskominfo</title>

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
        .register-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            margin: auto;
        }

        .register-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 25px rgba(0, 210, 255, 0.18);
            padding: 28px 30px 24px 30px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        /* Aksen Border Gold, Blue, & Cyan Atas Card */
        .register-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--amber-gold), var(--kominfo-blue), var(--cyber-cyan));
        }

        /* Header Logo & Icon */
        .brand-header-icon {
            width: 48px;
            height: 48px;
            background: rgba(0, 82, 163, 0.08);
            border: 1.5px solid rgba(0, 82, 163, 0.18);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            box-shadow: 0 4px 12px rgba(0, 82, 163, 0.08);
        }

        .brand-title {
            font-weight: 800;
            letter-spacing: 0.5px;
            color: var(--kominfo-dark);
            font-size: 1.3rem;
            margin-bottom: 2px;
        }

        .brand-subtitle {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.4;
            font-weight: 500;
        }

        /* Label Form & Input Formatting */
        .form-group-custom {
            margin-bottom: 1.05rem;
        }

        .form-label-custom {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .focus-cyber {
            background-color: #F8FAFC !important;
            border: 1.5px solid #CBD5E1 !important;
            padding: 9px 14px 9px 42px !important;
            border-radius: 10px !important;
            font-size: 0.85rem !important;
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
            font-size: 0.88rem;
            border-radius: 6px;
            transition: color 0.2s ease;
        }

        .password-toggle-btn:hover {
            color: var(--kominfo-dark);
        }

        /* 📊 INDIKATOR KEKUATAN SANDI (ENTROPY METER) */
        .entropy-meter-container {
            height: 4px;
            width: 100%;
            background-color: #E2E8F0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 6px;
        }

        .entropy-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        /* Link Custom */
        .link-custom {
            color: var(--kominfo-blue);
            font-weight: 700;
            transition: color 0.2s ease;
        }

        .link-custom:hover {
            color: var(--kominfo-dark);
        }

        /* Tombol Utama */
        .btn-cyber-submit {
            background-color: var(--kominfo-navy);
            color: #FFFFFF;
            font-weight: 700;
            font-size: 0.88rem;
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

        .laravel-invalid-msg {
            font-size: 0.725rem;
            color: #DC2626;
            font-weight: 600;
            margin-top: 4px;
            display: block;
        }

        .has-error-cyber {
            border-color: #EF4444 !important;
            background-color: #FEF2F2 !important;
        }

        /* Footer Keterangan */
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

    <div class="register-wrapper">
        <div class="register-card">
            
            <!-- HEADER LOGO & JUDUL -->
            <div class="text-center mb-3.5">
                <div class="brand-header-icon">
                    <i class="fas fa-user-plus text-primary" style="font-size: 1.25rem;"></i>
                </div>
                <h2 class="brand-title">SIP-O-SIBER</h2>
                <p class="brand-subtitle">Registrasi Mandiri Operator & Petugas Siber<br>Dinas Kominfo Provinsi Lampung</p>
            </div>

            <!-- NOTIFIKASI ERROR LARAVEL -->
            @if ($errors->any())
                <div class="alert alert-danger border-0 text-danger bg-danger bg-opacity-10 mb-3 p-2.5 px-3 rounded-3 shadow-sm">
                    <div class="d-flex align-items-center fw-bold mb-1" style="font-size: 0.8rem;">
                        <i class="fas fa-triangle-exclamation me-2 flex-shrink-0" style="font-size: 0.95rem;"></i>
                        <span>Pendaftaran Gagal Diproses!</span>
                    </div>
                    <ul class="mb-0 ps-3 mt-1" style="font-size: 0.74rem; font-weight: 500;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM REGISTRASI -->
            <form action="{{ route('register.post') }}" method="POST" id="formRegistration" class="needs-validation" novalidate autocomplete="off">
                @csrf
                
                <!-- Field: Nama Lengkap -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="nameInput">Nama Lengkap Petugas</label>
                    <div class="input-group-custom">
                        <i class="fas fa-id-card input-icon-left"></i>
                        <input type="text" 
                               id="nameInput"
                               name="name" 
                               class="form-control focus-cyber w-100 @error('name') has-error-cyber @enderror" 
                               value="{{ old('name') }}" 
                               placeholder="Masukkan nama lengkap & gelar" 
                               required 
                               autofocus>
                    </div>
                    @error('name')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- Field: Username / NPM / NIP -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="usernameInput">
                        <span>Nama Pengguna</span>
                        <span class="text-muted fw-normal" style="font-size: 0.7rem;">(Username / NPM / NIP)</span>
                    </label>
                    <div class="input-group-custom">
                        <i class="fas fa-user-shield input-icon-left"></i>
                        <input type="text" 
                               id="usernameInput"
                               name="username" 
                               class="form-control focus-cyber w-100 @error('username') has-error-cyber @enderror" 
                               value="{{ old('username') }}" 
                               placeholder="Masukkan Username, NPM, atau NIP" 
                               required>
                    </div>
                    @error('username')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- Field: Email Pengguna -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="emailInput">Email Pengguna</label>
                    <div class="input-group-custom">
                        <i class="fas fa-envelope input-icon-left"></i>
                        <input type="email" 
                               id="emailInput"
                               name="email" 
                               class="form-control focus-cyber w-100 @error('email') has-error-cyber @enderror" 
                               value="{{ old('email') }}" 
                               placeholder="nama@domain.com" 
                               required>
                    </div>
                    @error('email')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- Field: Kata Sandi -->
                <div class="form-group-custom">
                    <label class="form-label-custom" for="password">Kata Sandi</label>
                    <div class="input-group-custom">
                        <i class="fas fa-lock input-icon-left"></i>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-control focus-cyber w-100 @error('password') has-error-cyber @enderror" 
                               placeholder="Minimal 8 karakter" 
                               required>
                        <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('password', 'togglePasswordIcon')" aria-label="Tampilkan Kata Sandi">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    
                    <!-- Indikator Kekuatan Sandi -->
                    <div class="entropy-meter-container">
                        <div id="entropyBar" class="entropy-bar"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        <span class="text-muted" style="font-size: 0.68rem;">Gunakan kombinasi huruf, angka & simbol</span>
                        <span id="entropyLabel" class="fw-bold text-uppercase d-none" style="font-size: 0.65rem;"></span>
                    </div>
                    
                    @error('password')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- Field: Konfirmasi Kata Sandi -->
                <div class="form-group-custom mb-4">
                    <label class="form-label-custom" for="password_confirmation">Ulangi Kata Sandi</label>
                    <div class="input-group-custom">
                        <i class="fas fa-circle-check input-icon-left"></i>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               class="form-control focus-cyber w-100" 
                               placeholder="Ketik kembali kata sandi" 
                               required>
                        <button class="password-toggle-btn" type="button" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')" aria-label="Tampilkan Ulang Kata Sandi">
                            <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                        </button>
                    </div>
                    <div id="passwordMatchMessage" class="mt-1 fw-bold d-none" style="font-size: 0.725rem;"></div>
                </div>

                <!-- Tombol Submit Register -->
                <button type="submit" class="btn btn-cyber-submit w-100 mb-3">
                    <i class="fas fa-user-check"></i>
                    <span>Daftarkan Akun Baru</span>
                </button>
            </form>

            <!-- Link Kembali ke Halaman Login -->
            <div class="text-center pt-3 border-top border-slate-200" style="font-size: 0.8rem;">
                <span class="text-muted fw-medium">Sudah memiliki akun?</span> 
                <a href="{{ route('login') }}" class="text-decoration-none ms-1 link-custom">
                    Masuk di sini <i class="fas fa-arrow-right small ms-1"></i>
                </a>
            </div>

            <!-- FOOTER CARD -->
            <div class="card-inner-footer">
                © {{ date('Y') }} Diskominfo Provinsi Lampung.<br>Akses Terbatas Internal (Closed System v2.4).
            </div>

        </div>
    </div>

    <!-- Script Bootstrap & Logic Interaktif -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Visibilitas Kata Sandi
        function togglePasswordVisibility(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                toggleIcon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                toggleIcon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        // Indikator Kekuatan Password (Real-time Entropy Meter)
        const pwdInput = document.getElementById('password');
        const entropyBar = document.getElementById('entropyBar');
        const entropyLabel = document.getElementById('entropyLabel');

        if (pwdInput) {
            pwdInput.addEventListener('input', function() {
                const val = pwdInput.value;
                let score = 0;

                if (val.length === 0) {
                    entropyBar.style.width = '0%';
                    entropyLabel.classList.add('d-none');
                    return;
                }

                entropyLabel.classList.remove('d-none');

                if (val.length >= 8) score += 25;
                if (/[A-Z]/.test(val)) score += 25;
                if (/[0-9]/.test(val)) score += 25;
                if (/[^A-Za-z0-9]/.test(val)) score += 25;

                entropyBar.style.width = score + '%';

                if (score <= 25) {
                    entropyBar.style.backgroundColor = '#DC2626';
                    entropyLabel.textContent = 'Lemah';
                    entropyLabel.style.color = '#DC2626';
                } else if (score <= 50) {
                    entropyBar.style.backgroundColor = '#D97706';
                    entropyLabel.textContent = 'Sedang';
                    entropyLabel.style.color = '#D97706';
                } else if (score <= 75) {
                    entropyBar.style.backgroundColor = '#2563EB';
                    entropyLabel.textContent = 'Kuat';
                    entropyLabel.style.color = '#2563EB';
                } else {
                    entropyBar.style.backgroundColor = '#16A34A';
                    entropyLabel.textContent = 'Sangat Aman';
                    entropyLabel.style.color = '#16A34A';
                }
            });
        }

        // Validasi Kesesuaian Konfirmasi Kata Sandi Real-time
        const confirmInput = document.getElementById('password_confirmation');
        const matchMessage = document.getElementById('passwordMatchMessage');

        function verifyMatch() {
            if (!confirmInput || confirmInput.value === "") {
                matchMessage.classList.add('d-none');
                return;
            }
            
            matchMessage.classList.remove('d-none');
            if (pwdInput.value === confirmInput.value) {
                matchMessage.innerHTML = '<i class="fas fa-circle-check me-1"></i> Kata sandi cocok';
                matchMessage.className = "mt-1 text-success fw-bold";
            } else {
                matchMessage.innerHTML = '<i class="fas fa-circle-xmark me-1"></i> Kata sandi tidak cocok';
                matchMessage.className = "mt-1 text-danger fw-bold";
            }
        }

        if (pwdInput && confirmInput) {
            pwdInput.addEventListener('input', verifyMatch);
            confirmInput.addEventListener('input', verifyMatch);
        }

        // Bootstrap Native Form Validation
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