<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Pemulihan Kata Sandi - SIP-O-SIBER Diskominfo</title>
    
    <!-- Google Fonts, Bootstrap 5 & FontAwesome 6 -->
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
        .recovery-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            margin: auto;
        }

        .white-recovery-card {
            background: #FFFFFF;
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 25px rgba(0, 210, 255, 0.18);
            padding: 32px 32px 28px 32px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        /* Aksen Border Gold, Blue, & Cyan Atas Card */
        .white-recovery-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--amber-gold), var(--kominfo-blue), var(--cyber-cyan));
        }

        /* Header Logo & Icon */
        .card-brand-header {
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .card-brand-icon {
            width: 52px;
            height: 52px;
            background: rgba(255, 184, 0, 0.12);
            border: 1.5px solid rgba(255, 184, 0, 0.3);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(255, 184, 0, 0.15);
        }

        .card-brand-title {
            font-weight: 800;
            letter-spacing: -0.3px;
            color: var(--kominfo-dark);
            font-size: 1.35rem;
            margin-bottom: 4px;
        }

        .card-brand-subtitle {
            font-size: 0.8rem;
            color: var(--text-muted);
            line-height: 1.45;
            font-weight: 500;
        }

        /* Form Grouping & Custom Inputs */
        .form-group-item {
            margin-bottom: 1.15rem;
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

        .input-group-cyber {
            position: relative;
        }

        .input-addon-left {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            z-index: 5;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .form-control-cyber {
            background-color: #F8FAFC !important;
            border: 1.5px solid #CBD5E1 !important;
            padding: 9px 40px 9px 42px !important;
            border-radius: 10px !important;
            font-size: 0.85rem !important;
            color: var(--text-dark) !important;
            font-weight: 500;
            height: 44px;
            transition: all 0.2s ease-in-out;
        }

        .form-control-cyber:focus {
            background-color: #FFFFFF !important;
            border-color: var(--kominfo-blue) !important;
            box-shadow: 0 0 0 4px rgba(0, 82, 163, 0.12) !important;
        }

        .input-group-cyber:focus-within .input-addon-left {
            color: var(--kominfo-blue);
        }

        /* Toggle Eye Button */
        .btn-toggle-eye {
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

        .btn-toggle-eye:hover {
            color: var(--kominfo-dark);
        }

        /* 📊 INDIKATOR KEKUATAN SANDI (ENTROPY METER) */
        .entropy-meter-container {
            height: 4px;
            width: 100%;
            background-color: #E2E8F0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 8px;
        }

        .entropy-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
        }

        /* Info Box */
        .recovery-info-box {
            background-color: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.74rem;
            color: #166534;
            line-height: 1.45;
            margin-bottom: 1.25rem;
            font-weight: 500;
        }

        /* Custom Button */
        .btn-cyber-primary { 
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
        
        .btn-cyber-primary:hover { 
            background-color: var(--kominfo-blue);
            color: #FFFFFF;
            box-shadow: 0 6px 18px rgba(0, 82, 163, 0.35);
            transform: translateY(-1.5px);
        }

        .btn-cyber-primary:active {
            transform: translateY(0);
        }

        .link-custom {
            color: var(--kominfo-blue);
            font-weight: 700;
            transition: color 0.2s ease;
        }
        
        .link-custom:hover {
            color: var(--kominfo-dark);
        }

        .laravel-invalid-msg {
            font-size: 0.725rem;
            color: #DC2626;
            font-weight: 600;
            margin-top: 5px;
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
            margin-top: 16px;
            font-weight: 500;
            line-height: 1.45;
        }
    </style>
</head>
<body>

    <!-- Light Ambient Background Effects -->
    <div class="cyber-glow-topleft"></div>
    <div class="cyber-glow-bottomright"></div>

    <div class="recovery-wrapper">
        <div class="white-recovery-card">
            
            <!-- BRAND HEADER -->
            <div class="card-brand-header">
                <div class="card-brand-icon">
                    <i class="fas fa-key text-warning" style="font-size: 1.25rem;"></i>
                </div>
                <h2 class="card-brand-title">Pemulihan Kata Sandi</h2>
                <p class="card-brand-subtitle">
                    Verifikasi identitas akun Anda untuk memperbarui kata sandi secara langsung ke pangkalan data CSIRT.
                </p>
            </div>

            <!-- ALERT NOTIFIKASI STATUS -->
            @if (session('status'))
                <div class="alert alert-success border-0 text-success bg-success bg-opacity-10 mb-3 p-3 rounded-3 shadow-sm" style="font-size: 0.78rem; font-weight: 600;">
                    <i class="fas fa-circle-check me-2"></i> {{ session('status') }}
                </div>
            @endif

            <!-- ALERT NOTIFIKASI ERROR KESELURUHAN -->
            @if ($errors->any())
                <div class="alert alert-danger border-0 text-danger bg-danger bg-opacity-10 mb-3 p-3 rounded-3 shadow-sm">
                    <div class="d-flex align-items-center fw-bold mb-1" style="font-size: 0.8rem;">
                        <i class="fas fa-triangle-exclamation me-2 flex-shrink-0" style="font-size: 0.95rem;"></i>
                        <span>Gagal Memperbarui Kata Sandi!</span>
                    </div>
                    <ul class="mb-0 ps-3 mt-1" style="font-size: 0.74rem; font-weight: 500;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM RESET DIRECT -->
            <form action="{{ route('password.email') }}" method="POST" id="formRecovery" class="needs-validation" novalidate autocomplete="off">
                @csrf

                <!-- 1. IDENTITAS AKUN -->
                <div class="form-group-item">
                    <label class="form-label-custom" for="usernameInput">
                        <span>Nama Pengguna</span>
                        <span class="text-muted fw-normal" style="font-size: 0.7rem;">(Username / NPM)</span>
                    </label>
                    <div class="input-group-cyber">
                        <i class="fas fa-id-card input-addon-left"></i>
                        <input type="text" 
                               id="usernameInput"
                               name="username" 
                               class="form-control form-control-cyber w-100 @error('username') has-error-cyber @enderror" 
                               value="{{ old('username') }}" 
                               placeholder="Contoh: 19850312... atau nama_petugas" 
                               required 
                               autofocus>
                    </div>
                    @error('username')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <!-- 2. KATA SANDI BARU -->
                <div class="form-group-item">
                    <label class="form-label-custom" for="pwdInput">Kata Sandi Baru</label>
                    <div class="input-group-cyber">
                        <i class="fas fa-lock input-addon-left"></i>
                        <input type="password" 
                               id="pwdInput"
                               name="password" 
                               class="form-control form-control-cyber w-100 @error('password') has-error-cyber @enderror" 
                               placeholder="Minimal 8 karakter terenkripsi" 
                               required>
                        <button type="button" class="btn-toggle-eye" onclick="toggleVisibility('pwdInput', 'eyeIcon1')" aria-label="Tampilkan Kata Sandi Baru">
                            <i class="fas fa-eye" id="eyeIcon1"></i>
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

                <!-- 3. KONFIRMASI KATA SANDI BARU -->
                <div class="form-group-item mb-3">
                    <label class="form-label-custom" for="pwdConfirmInput">Konfirmasi Kata Sandi Baru</label>
                    <div class="input-group-cyber">
                        <i class="fas fa-shield-halved input-addon-left"></i>
                        <input type="password" 
                               id="pwdConfirmInput"
                               name="password_confirmation" 
                               class="form-control form-control-cyber w-100" 
                               placeholder="Ulangi kata sandi baru" 
                               required>
                        <button type="button" class="btn-toggle-eye" onclick="toggleVisibility('pwdConfirmInput', 'eyeIcon2')" aria-label="Tampilkan Konfirmasi Kata Sandi">
                            <i class="fas fa-eye" id="eyeIcon2"></i>
                        </button>
                    </div>
                    <div id="passwordMatchMessage" class="mt-1 fw-bold d-none" style="font-size: 0.725rem;"></div>
                </div>

                <!-- INFO SECURITY BOX -->
                <div class="recovery-info-box">
                    <i class="fas fa-user-shield me-1 text-success"></i>
                    Sistem akan memverifikasi integritas akun. Perubahan kata sandi akan langsung berlaku untuk sesi masuk berikutnya.
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" class="btn btn-cyber-primary w-100 mb-3">
                    <i class="fas fa-save"></i>
                    <span>Perbarui & Simpan Kata Sandi</span>
                </button>
            </form>

            <!-- LINK BACK TO LOGIN -->
            <div class="text-center pt-3 border-top border-slate-200" style="font-size: 0.8rem;">
                <a href="{{ route('login') }}" class="text-decoration-none link-custom">
                    <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Login
                </a>
            </div>

            <!-- HELPDESK FOOTER -->
            <div class="card-inner-footer">
                Kendala akun terblokir? Hubungi <a href="https://wa.me/6281234567890" target="_blank" class="text-decoration-none fw-bold text-primary">Helpdesk CSIRT Lampung</a>
            </div>

        </div>
    </div>

    <!-- Script Bootstrap & Logic Interaktif -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Toggle Visibilitas Kata Sandi
        function toggleVisibility(inputId, iconId) {
            const inputField = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (inputField.type === "password") {
                inputField.type = "text";
                icon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                inputField.type = "password";
                icon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        // Indikator Kekuatan Password (Real-time Entropy Meter)
        const pwdInput = document.getElementById('pwdInput');
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
        const confirmInput = document.getElementById('pwdConfirmInput');
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