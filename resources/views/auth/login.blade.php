<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - SIP-O-SIBER Diskominfo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(180deg, #eef2f7 0%, #d9e2ec 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 16px;
            color: #333333;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .login-container {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-top: 5px solid #002060;
            width: 100%;
            max-width: 440px;
            padding: 40px 35px;
        }
        .gov-logo-zone {
            text-align: center;
            margin-bottom: 25px;
        }
        .gov-title {
            color: #002060;
            font-weight: 800;
            font-size: 1.7rem;
            letter-spacing: 0.5px;
            margin: 0;
        }
        .gov-subtitle {
            color: #ffc000;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .system-desc {
            font-size: 0.9rem;
            color: #666666;
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 1px solid #e1e8ed;
            padding-bottom: 15px;
            line-height: 1.5;
        }
        .form-label-custom {
            color: #0f3057;
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 8px;
        }
        .form-control-custom {
            background-color: #f8fafc;
            border: 1px solid #ccd6e0;
            color: #333333;
            font-size: 1rem;
            padding: 12px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }
        .form-control-custom:focus {
            background-color: #ffffff;
            border-color: #002060;
            box-shadow: 0 0 0 3px rgba(0, 32, 96, 0.15);
            outline: none;
        }
        .btn-gov-login {
            background-color: #002060;
            color: #ffffff;
            font-weight: 600;
            padding: 12px;
            font-size: 1rem;
            border: none;
            border-radius: 6px;
            transition: background-color 0.2s;
        }
        .btn-gov-login:hover {
            background-color: #0f3057;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="gov-logo-zone">
            <h2 class="gov-title"><i class="fas fa-shield-alt text-primary me-2"></i>LAMPUNGPROV</h2>
            <div class="gov-subtitle">SIP-O-SIBER</div>
        </div>
        
        <div class="system-desc">
            Sistem Informasi Presensi & Operasional Siber<br>
            <strong>Diskominfo Provinsi Lampung</strong>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 text-success small fw-bold bg-success bg-opacity-10 mb-3 p-3 rounded">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 text-danger small fw-bold bg-danger bg-opacity-10 mb-3 p-3 rounded">
                <i class="fas fa-exclamation-circle me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label form-label-custom"><i class="fas fa-user me-2 text-muted"></i>Nama Pengguna (Username)</label>
                <input type="text" name="username" class="form-control form-control-custom w-100" required placeholder="Masukkan nama pengguna" value="{{ old('username') }}">
            </div>
            
            <div class="mb-4">
                <label class="form-label form-label-custom"><i class="fas fa-lock me-2 text-muted"></i>Kata Sandi (Password)</label>
                <input type="password" name="password" class="form-control form-control-custom w-100" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-gov-login w-100 mb-3 shadow-sm">Masuk ke Sistem</button>
        </form>

        <div class="text-center small mt-3 pt-2 border-top">
            <span class="text-muted">Operator/Petugas Baru?</span> 
            <a href="{{ route('register') }}" class="fw-bold text-decoration-none ms-1" style="color: #002060;">
                Daftar Akun Mandiri <i class="fas fa-arrow-right small ms-1"></i>
            </a>
        </div>
    </div>

</body>
</html>