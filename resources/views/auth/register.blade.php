<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>Pendaftaran Personel Siber - SIP-O-SIBER</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            /* 🎨 PALET WARNA UTAMA INTERNAL & EKSTERNAL */
            --kominfo-dark: #0A1D37;
            --kominfo-navy: #102A43;
            --kominfo-blue: #0052A3;
            --amber-gold: #FFB800;
            --text-dark: #1E293B;
            --text-muted: #64748B;
            --border-custom: #CBD5E1;
        }

        body { 
            background: linear-gradient(135deg, var(--kominfo-dark) 0%, var(--kominfo-navy) 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* 🤍 PEMBARUAN UTAMA: KARTU FORM LAYOUT PUTIH BERSIH DENGAN KONTRAS TINGGI */
        .white-register-card {
            background-color: #FFFFFF;
            border: none;
            border-top: 6px solid var(--amber-gold);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border-radius: 16px;
        }

        .form-title-custom {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--kominfo-dark);
        }

        /* 📝 LABEL INPUT DIATAS FORM: SANGAT JELAS & TEBAL */
        .form-label-custom {
            font-size: 0.95rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        /* ⚡ INDIKATOR STATE FOCUS INPUT */
        .input-group-cyber {
            position: relative;
            background-color: #F8FAFC;
            border: 1.5px solid var(--border-custom);
            border-radius: 8px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
        }

        .input-group-cyber:focus-within {
            background-color: #FFFFFF;
            border-color: var(--kominfo-blue);
            box-shadow: 0 0 0 4px rgba(0, 82, 163, 0.15);
        }

        .input-group-cyber .input-addon-left {
            padding: 14px 16px;
            color: #94A3B8;
            font-size: 1.1rem;
            transition: color 0.25s;
        }

        .input-group-cyber:focus-within .input-addon-left {
            color: var(--kominfo-blue);
        }

        /* 🎯 WARNA TEKS DALAM INPUT BOX: HITAM TINGGI */
        .input-group-cyber .form-control-cyber {
            background: transparent !important;
            border: none !important;
            color: var(--text-dark) !important;
            padding: 14px 16px 14px 0;
            font-size: 0.98rem;
            font-weight: 500;
            box-shadow: none !important;
            width: 100%;
        }

        .input-group-cyber .form-control-cyber::placeholder {
            color: #94A3B8;
        }

        .password-toggle-trigger {
            background: none;
            border: none;
            padding: 14px 16px;
            color: #94A3B8;
            cursor: pointer;
            font-size: 1.05rem;
            transition: color 0.2s;
        }

        .password-toggle-trigger:hover {
            color: var(--text-dark);
        }

        /* 📊 PROGRESS BAR KEKUATAN SANDI */
        .entropy-meter-container {
            height: 6px;
            width: 100%;
            background-color: #E2E8F0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 8px;
        }

        .entropy-bar {
            height: 100%;
            width: 0%;
            transition: all 0.4s ease;
        }

        /* Tombol Registrasi Utama */
        .btn-cyber-register { 
            background-color: var(--kominfo-dark);
            color: #FFFFFF; 
            font-weight: 600; 
            font-size: 1.05rem;
            padding: 14px;
            border: none;
            border-radius: 8px;
            transition: all 0.25s ease;
        }
        
        .btn-cyber-register:hover { 
            background-color: var(--kominfo-blue);
            box-shadow: 0 4px 14px rgba(0, 82, 163, 0.3);
            color: #FFFFFF;
        }

        /* Tombol SSO Jalur Google Workspace */
        .btn-sso-google {
            background-color: #FAFAFA;
            color: var(--text-dark);
            border: 1px solid var(--border-custom);
            font-weight: 700;
            font-size: 0.95rem;
            padding: 13px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .btn-sso-google:hover {
            background-color: #F1F5F9;
            border-color: #94A3B8;
            color: #000000;
        }

        .laravel-invalid-msg {
            font-size: 0.85rem;
            color: #DC2626;
            font-weight: 600;
            margin-top: 6px;
            display: block;
        }
        
        .has-error-cyber {
            border-color: #EF4444 !important;
            background-color: #FEF2F2 !important;
        }

        .link-custom {
            color: var(--kominfo-blue);
            font-weight: 700;
            transition: color 0.2s;
        }
        
        .link-custom:hover {
            color: var(--kominfo-dark);
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center py-5">

    <div class="container" style="max-width: 580px; z-index: 10; position: relative;">
        
        <div class="text-center mb-4">
            <h2 class="fw-bold text-white mb-1 tracking-wide" style="font-size: 2.2rem;">
                <i class="fas fa-shield-halved text-warning me-2"></i>SIP-O-SIBER
            </h2>
            <p class="text-uppercase small fw-bold tracking-widest text-info mb-0" style="font-size: 0.8rem; opacity: 0.9;">
                Registrasi Mandiri Operator Petugas Siber
            </p>
        </div>

        <div class="card white-register-card p-4 p-sm-5">
            
            <div class="mb-4 text-center">
                <h3 class="form-title-custom mb-1">Pendaftaran Akun</h3>
                <p class="text-muted small mb-0">Lengkapi berkas identitas penugasan siber Anda</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger bg-danger bg-opacity-10 border-0 text-danger rounded-3 p-3 small mb-4" role="alert">
                    <div class="d-flex align-items-center fw-bold mb-1">
                        <i class="fas fa-triangle-exclamation me-2 fs-5"></i>
                        <span>Pendaftaran Gagal Diproses!</span>
                    </div>
                    <ul class="mb-0 ps-3 small font-weight-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" id="formRegistration" class="needs-validation" novalidate>
                @csrf
                
                <div class="mb-3">
                    <label class="form-label-custom">Nama Lengkap Petugas</label>
                    <div class="input-group-cyber @error('name') has-error-cyber @enderror">
                        <span class="input-addon-left"><i class="fas fa-user-gear"></i></span>
                        <input type="text" name="name" class="form-control-cyber" 
                               value="{{ old('name') }}" placeholder="Contoh: Ilham Professional" required autofocus>
                    </div>
                    @error('name')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label-custom">Nama Pengguna (Username Untuk Login)</label>
                    <div class="input-group-cyber @error('username') has-error-cyber @enderror">
                        <span class="input-addon-left"><i class="fas fa-user-shield"></i></span>
                        <input type="text" name="username" class="form-control-cyber" 
                               value="{{ old('username') }}" placeholder="Contoh: ilham.siber" required>
                    </div>
                    @error('username')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label-custom">Alamat Email Resmi Dinas</label>
                    <div class="input-group-cyber @error('email') has-error-cyber @enderror">
                        <span class="input-addon-left"><i class="fas fa-envelope-open-text"></i></span>
                        <input type="email" name="email" class="form-control-cyber" 
                               value="{{ old('email') }}" placeholder="petugas@lampungprov.go.id" required>
                    </div>
                    @error('email')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label-custom">Kata Sandi (Password Amankan)</label>
                    <div class="input-group-cyber @error('password') has-error-cyber @enderror">
                        <span class="input-addon-left"><i class="fas fa-key"></i></span>
                        <input type="password" name="password" id="password" class="form-control-cyber" 
                               placeholder="Kombinasi minimal 8 karakter unik" required>
                        <button class="password-toggle-trigger" type="button" onclick="togglePasswordVisibility('password', 'togglePasswordIcon')">
                            <i class="fas fa-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                    
                    <div class="entropy-meter-container">
                        <div id="entropyBar" class="entropy-bar"></div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-1.5">
                        <div class="text-muted" style="font-size: 0.8rem; font-weight: 500;">
                            <i class="fas fa-circle-info me-1 text-secondary"></i>Wajib menyertakan Huruf Besar, Angka, dan Simbol.
                        </div>
                        <div id="entropyLabel" class="fw-bold text-uppercase d-none" style="font-size: 0.75rem;"></div>
                    </div>
                    
                    @error('password')
                        <span class="laravel-invalid-msg"><i class="fas fa-circle-exclamation me-1"></i> {{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-4 mt-3">
                    <label class="form-label-custom">Ulangi Kata Sandi (Konfirmasi Otentikasi)</label>
                    <div class="input-group-cyber">
                        <span class="input-addon-left"><i class="fas fa-circle-check"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="form-control-cyber" 
                               placeholder="Ketik kembali sandi di atas" required>
                        <button class="password-toggle-trigger" type="button" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')">
                            <i class="fas fa-eye" id="toggleConfirmPasswordIcon"></i>
                        </button>
                    </div>
                    <div id="passwordMatchMessage" class="small mt-1.5 fw-bold d-none" style="font-size: 0.85rem;"></div>
                </div>

                <button type="submit" class="btn btn-cyber-register w-100 shadow-sm mb-3">
                    <i class="fas fa-user-plus me-2"></i>Inisialisasi Pendaftaran Akun
                </button>
                
                <div class="position-relative d-flex align-items-center justify-content-center my-4">
                    <hr class="w-100 opacity-20" style="color: #cbd5e1;">
                    <span class="position-absolute px-3 small text-muted text-uppercase fw-bold" style="font-size: 0.75rem; background-color: #FFFFFF; letter-spacing: 1px;">Atau</span>
                </div>

                <a href="#" class="btn btn-sso-google w-100 text-decoration-none d-flex align-items-center justify-content-center shadow-sm">
                    <img src="https://www.gstatic.com/images/branding/product/1x/gsa_512dp.png" alt="Google Logo" class="me-2" style="width: 18px; height: 18px;">
                    Registrasi Cepat via Google
                </a>
                
                <div class="text-center small mt-4 pt-3 border-top border-slate-200" style="font-size: 0.95rem;">
                    <span class="text-muted fw-medium">Sudah lolos sertifikasi berkas akun?</span> 
                    <a href="{{ route('login') }}" class="text-decoration-none ms-1 link-custom">
                        Masuk Dashboard <i class="fas fa-chevron-right small ms-0.5" style="font-size: 0.75rem;"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Fitur 1: Otorisasi Visibilitas Tipe Field Sandi
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

        // Fitur 2: Real-time Password Entropy Meter
        const pwdInput = document.getElementById('password');
        const entropyBar = document.getElementById('entropyBar');
        const entropyLabel = document.getElementById('entropyLabel');

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
                entropyBar.style.backgroundColor = '#DC2626'; // Merah
                entropyLabel.textContent = 'Lemah';
                entropyLabel.style.color = '#DC2626';
            } else if (score <= 50) {
                entropyBar.style.backgroundColor = '#D97706'; // Jingga
                entropyLabel.textContent = 'Sedang';
                entropyLabel.style.color = '#D97706';
            } else if (score <= 75) {
                entropyBar.style.backgroundColor = '#2563EB'; // Biru
                entropyLabel.textContent = 'Kuat';
                entropyLabel.style.color = '#2563EB';
            } else {
                entropyBar.style.backgroundColor = '#16A34A'; // Hijau
                entropyLabel.textContent = 'Sangat Aman';
                entropyLabel.style.color = '#16A34A';
            }
        });

        // Fitur 3: Real-time Match Tracker Validation
        const confirmInput = document.getElementById('password_confirmation');
        const matchMessage = document.getElementById('passwordMatchMessage');

        function verifyMatch() {
            if (confirmInput.value === "") {
                matchMessage.classList.add('d-none');
                return;
            }
            
            matchMessage.classList.remove('d-none');
            if (pwdInput.value === confirmInput.value) {
                matchMessage.innerHTML = '<i class="fas fa-circle-check me-1"></i> Kredensial kata sandi sesuai (Match)';
                matchMessage.className = "small mt-1.5 text-success fw-bold";
            } else {
                matchMessage.innerHTML = '<i class="fas fa-circle-xmark me-1"></i> Kredensial kata sandi tidak sesuai';
                matchMessage.className = "small mt-1.5 text-danger fw-bold";
            }
        }

        pwdInput.addEventListener('input', verifyMatch);
        confirmInput.addEventListener('input', verifyMatch);

        // Native Bootstrap Form Validation
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