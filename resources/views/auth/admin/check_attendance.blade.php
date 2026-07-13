<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi Penugasan - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #0f3057; color: #ffffff; font-family: 'Segoe UI', sans-serif; }
        .box-attendance { background: #ffffff; border-radius: 8px; color: #333333; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border-top: 5px solid #ffc000; }
        .btn-gov { background-color: #0f3057; color: #fff; font-weight: bold; }
        .btn-gov:hover { background-color: #002060; color: #fff; }
    </style>
</head>
<body class="d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold text-warning"><i class="fas fa-shield-alt me-2"></i>SIP-O-SIBER</h3>
                    <p class="text-white-50 small text-uppercase fw-bold">Sistem Informasi Pelaporan & Operasional Siber</p>
                </div>

                <div class="card box-attendance p-4">
                    <h5 class="fw-bold text-center mb-3" style="color: #0f3057;">
                        <i class="fas fa-user-check me-2"></i>REGISTRASI PRESENSI HARIAN
                    </h5>
                    <p class="text-muted small text-center mb-4">
                        Halo <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role }}), silakan lengkapi data presensi manual Anda di bawah ini.
                    </p>

                    @if(session('error'))
                        <div class="alert alert-danger small border-0 fw-bold p-2 mb-3">{{ session('error') }}</div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success small border-0 fw-bold p-2 mb-3">{{ session('success') }}</div>
                    @endif

                    <form action="{{ Auth::user()->role === 'Admin' ? route('admin.attendance.store') : route('petugas.attendance.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label small fw-bold">1. Kategori Presensi</label>
                            <select name="attendance_info" class="form-select" required>
                                @if(!$hasMasuk)
                                    <option value="Masuk">Presensi Masuk Tugas Harian</option>
                                @else
                                    <option value="Pulang">Presensi Pulang Tugas Harian</option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">
                                2. Jam @if(!$hasMasuk) Kedatangan @else Kepulangan @endif (Ketik Manual)
                            </label>
                            <input type="time" name="manual_time" class="form-control input-gov rounded" 
                            value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold">3. Catatan Kegiatan Hari Ini (Bebas)</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Contoh: Monitoring jaringan, penanganan insiden siber, atau rekapitulasi berkas..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-gov w-100 py-2">
                            <i class="fas fa-unlock me-2"></i>Kirim & Buka Dashboard
                        </button>
                    </form>
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('logout') }}" class="text-white-50 text-decoration-none small">
                        <i class="fas fa-arrow-left me-1"></i> Kembali / Keluar Aplikasi
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>