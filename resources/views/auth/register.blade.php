<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Petugas - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f4f6f9; 
            font-family: 'Segoe UI', sans-serif; 
        }
        .card-register { 
            border: none; 
            border-top: 5px solid #0f3057; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.1); 
            border-radius: 8px;
        }
        .btn-register { 
            background-color: #0f3057; 
            color: white; 
            font-weight: 600; 
        }
        .btn-register:hover { 
            background-color: #002060; 
            color: white; 
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

    <div class="container" style="max-width: 500px;">
        <div class="text-center mb-4">
            <h3 class="fw-bold mb-1" style="color: #0f3057;"><i class="fas fa-shield-alt text-warning me-2"></i>SIP-O-SIBER</h3>
            <span class="text-muted text-uppercase small fw-bold">Registrasi Mandiri Operator Petugas</span>
        </div>

        <div class="card card-register p-4 bg-white">
            @if ($errors->any())
                <div class="alert alert-danger py-2 border-0 small">
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Lengkap Petugas</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-user-tie"></i></span>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Rian Hidayat" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Username (Untuk Login)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" placeholder="Contoh: rian.hidayat" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Kata Sandi (Password)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary">Ulangi Kata Sandi (Konfirmasi)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-check-double"></i></span>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ketik ulang password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-register w-100 py-2.5 mb-3 shadow-sm rounded"><i class="fas fa-user-plus me-2"></i>Daftarkan Akun Saya</button>
                
                <div class="text-center small">
                    <span class="text-muted">Sudah punya akun?</span> <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #0f3057;">Masuk di sini</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>