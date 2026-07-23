<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Pemulihan Kata Sandi - SIP-O-SIBER Diskominfo</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & FontAwesome 6 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --kominfo-dark: #0A1D37;
            --kominfo-navy: #0F233D;
            --kominfo-blue: #0052A3;
            --cyber-cyan: #00D2FF;
            --amber-gold: #FFB800;
            --text-main: #0F172A;
            --text-muted: #475569;
            --border-custom: #CBD5E1;
            --font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            overflow-x: hidden;
        }

        body { 
            background: 
                radial-gradient(circle at 50% 50%, rgba(0, 82, 163, 0.35) 0%, rgba(10, 29, 55, 0.95) 60%, #050E1A 100%),
                radial-gradient(circle at 50% 30%, rgba(0, 210, 255, 0.12) 0%, transparent 50%);
            background-color: var(--kominfo-dark);
            font-family: var(--font-family);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem 1rem;
            position: relative;
            -webkit-font-smoothing: antialiased;
        }

        /* Cyber Pattern Grid Background (Serasi dengan Login & Register) */
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            background-size: 26px 26px;
            pointer-events: none;
            z-index: 1;
        }

        .main-container {
            width: 100%;
            max-width: 430px;
            z-index: 10;
            position: relative;
        }

        .white-recovery-card {
            background-color: #FFFFFF;
            border: none;
            border-top: 4px solid var(--amber-gold);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6), 0 0 20px rgba(0, 82, 163, 0.25);
            border-radius: 14px;
            padding: 1.75rem 1.6rem 1.4rem 1.6rem;
        }

        .card-brand-header {
            text-align: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #E2E8F0;
        }

        .card-brand-icon {
            width: 48px;
            height: 48px;
            background-color: #FEF3C7;
            color: #D97706;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 8px;
            box-shadow: 0 2px 8px rgba(217, 119, 6, 0.15);
        }

        .card-brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--kominfo-dark);
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }

        .card-brand-subtitle {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.4;
            font-weight: 500;
        }

        .form-group-item {
            margin-bottom: 0.95rem;
        }

        .form-label-custom {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 5px;
            display: block;
        }

        .input-group-cyber {
            position: relative;
            background-color: #F8FAFC;
            border: 1.5px solid var(--border-custom);
            border-radius: 8px;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }

        .input-group-cyber:focus-within {
            background-color: #FFFFFF;
            border-color: var(--kominfo-blue);
            box-shadow: 0 0 0 3px rgba(0, 82, 163, 0.15);
        }

        .input-group-cyber .input-addon-left {
            padding: 8px 12px;
            color: #64748B;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .input-group-cyber:focus-within .input-addon-left {
            color: var(--kominfo-blue);
        }

        .input-group-cyber .form-control-cyber {
            background: transparent !important;
            border: none !important;
            color: var(--text-main) !important;
            padding: 8px 12px 8px 0;
            font-size: 0.82rem;
            font-weight: 600;
            font-family: var(--font-family);
            box-shadow: none !important;
            width: 100%;
        }

        .input-group-cyber .form-control-cyber::placeholder {
            color: #94A3B8;
            font-size: 0.78rem;
            font-weight: 400;
        }

        .btn-toggle-eye {
            background: transparent;
            border: none;
            color: #94A3B8;
            padding: 0 12px;
            cursor: pointer;
            font-size: 0.85rem;
            transition: color 0.15s ease;
        }

        .btn-toggle-eye:hover {
            color: var(--kominfo-blue);
        }

        .recovery-info-box {
            background-color: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 0.72rem;
            color: #166534;
            line-height: 1.4;
            margin-bottom: 1.1rem;
            font-weight: 500;
        }

        .btn-cyber-primary { 
            background-color: var(--kominfo-dark);
            color: #FFFFFF; 
            font-weight: 700; 
            font-size: 0.85rem;
            padding: 10px;
            border: none;
            border-radius: 8px;
            letter-spacing: 0.3px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(10, 29, 55, 0.25);
        }
        
        .btn-cyber-primary:hover { 
            background-color: var(--kominfo-blue);
            box-shadow: 0 6px 16px rgba(0, 82, 163, 0.35);
            color: #FFFFFF;
        }

        .link-custom {
            color: var(--kominfo-blue);
            font-weight: 700;
            text-decoration: none;
            transition: color 0.15s ease;
        }
        
        .link-custom:hover {
            color: var(--kominfo-dark);
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="card white-recovery-card">
            
            <!-- BRAND HEADER -->
            <div class="card-brand-header">
                <div class="card-brand-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="card-brand-title">Pemulihan Kata Sandi</div>
                <div class="card-brand-subtitle">
                    Verifikasi identitas akun Anda untuk memperbarui kata sandi secara langsung ke pangkalan data CSIRT.
                </div>
            </div>

            <!-- ALERT NOTIFIKASI STATUS -->
            @if (session('status'))
                <div class="alert alert-success bg-success bg-opacity-10 border-0 text-success rounded-3 p-2.5 mb-3" style="font-size: 0.76rem; font-weight: 600;">
                    <i class="fas fa-circle-check me-1.5"></i> {{ session('status') }}
                </div>
            @endif

            <!-- ALERT NOTIFIKASI ERROR -->
            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 border-0 text-danger rounded-3 p-2.5 mb-3" style="font-size: 0.76rem; font-weight: 600;">
                    <i class="fas fa-circle-exclamation me-1.5"></i> {{ $errors->first() }}
                </div>
            @endif

            <!-- FORM RESET DIRECT -->
            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                <!-- 1. IDENTITAS AKUN -->
                <div class="form-group-item">
                    <label class="form-label-custom" for="usernameInput">Nama Pengguna (Username / NPM / NIP)</label>
                    <div class="input-group-cyber">
                        <span class="input-addon-left"><i class="fas fa-id-card"></i></span>
                        <input type="text" 
                               id="usernameInput"
                               name="username" 
                               class="form-control-cyber" 
                               value="{{ old('username') }}" 
                               placeholder="Contoh: 19850312... atau nama_petugas" 
                               required 
                               autofocus>
                    </div>
                </div>

                <!-- 2. KATA SANDI BARU -->
                <div class="form-group-item">
                    <label class="form-label-custom" for="pwdInput">Kata Sandi Baru</label>
                    <div class="input-group-cyber">
                        <span class="input-addon-left"><i class="fas fa-lock"></i></span>
                        <input type="password" 
                               id="pwdInput"
                               name="password" 
                               class="form-control-cyber" 
                               placeholder="Minimal 8 karakter terenkripsi" 
                               required>
                        <button type="button" class="btn-toggle-eye" onclick="toggleVisibility('pwdInput', 'eyeIcon1')">
                            <i class="fas fa-eye" id="eyeIcon1"></i>
                        </button>
                    </div>
                </div>

                <!-- 3. KONFIRMASI KATA SANDI BARU -->
                <div class="form-group-item mb-3">
                    <label class="form-label-custom" for="pwdConfirmInput">Konfirmasi Kata Sandi Baru</label>
                    <div class="input-group-cyber">
                        <span class="input-addon-left"><i class="fas fa-shield-halved"></i></span>
                        <input type="password" 
                               id="pwdConfirmInput"
                               name="password_confirmation" 
                               class="form-control-cyber" 
                               placeholder="Ulangi kata sandi baru" 
                               required>
                        <button type="button" class="btn-toggle-eye" onclick="toggleVisibility('pwdConfirmInput', 'eyeIcon2')">
                            <i class="fas fa-eye" id="eyeIcon2"></i>
                        </button>
                    </div>
                </div>

                <!-- INFO SECURITY BOX -->
                <div class="recovery-info-box">
                    <i class="fas fa-user-shield me-1 text-success"></i>
                    Sistem akan memverifikasi integritas akun. Perubahan kata sandi akan langsung berlaku untuk sesi masuk berikutnya.
                </div>

                <!-- SUBMIT BUTTON -->
                <button type="submit" class="btn btn-cyber-primary w-100">
                    <i class="fas fa-save me-1.5"></i> Perbarui & Simpan Kata Sandi
                </button>

                <!-- LINK BACK TO LOGIN -->
                <div class="text-center small mt-3 pt-2.5 border-top border-slate-200" style="font-size: 0.78rem;">
                    <a href="{{ route('login') }}" class="link-custom">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Login
                    </a>
                </div>

                <!-- HELPDESK FOOTER -->
                <div class="text-center text-muted mt-2.5 pt-1" style="font-size: 0.68rem; font-weight: 500;">
                    Kendala akun terblokir? Hubungi <a href="https://wa.me/6281234567890" target="_blank" class="text-decoration-none fw-bold text-primary">Helpdesk CSIRT Lampung</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT TOGGLE VISIBILITY -->
    <script>
        function toggleVisibility(inputId, iconId) {
            const inputField = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (inputField.type === "password") {
                inputField.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                inputField.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>