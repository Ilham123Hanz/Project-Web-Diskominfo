<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul Absensi Harian Reguler - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f4f6f9; 
            color: #333333; 
            font-family: 'Segoe UI', system-ui, sans-serif; 
        }
        .main-container {
            max-width: 900px;
            margin: 40px auto;
        }
        .card-custom {
            background: #ffffff;
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 25px;
        }
        .form-label-custom {
            font-size: 0.75rem;
            font-weight: 700;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        .form-control-custom {
            background-color: #f8f9fa;
            border: 1px solid #e2e8f0;
            padding: 12px 15px;
            font-weight: 600;
            color: #2c3e50;
            border-radius: 8px;
        }
        .form-control-custom:disabled, .form-control-custom[readonly] {
            background-color: #f1f3f5;
            color: #495057;
        }
        .btn-attendance {
            background-color: #198754;
            color: #ffffff;
            font-weight: 600;
            padding: 14px 30px;
            border-radius: 8px;
            border: none;
            transition: all 0.2s ease;
        }
        .btn-attendance:hover {
            background-color: #157347;
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.3);
        }
        .btn-attendance.clock-out {
            background-color: #dc3545;
        }
        .btn-attendance.clock-out:hover {
            background-color: #bb2d3b;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
        }
        .telemetry-badge {
            font-size: 0.75rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="container main-container">
        
        {{-- Flash Message Notifikasi --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Row Indikator Status Absensi Waktu Nyata --}}
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <div class="p-2 bg-white rounded shadow-sm d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold"><i class="fas fa-business-time text-primary me-1"></i> Jam Kerja Berlaku:</span>
                    <span class="badge bg-secondary telemetry-badge">07:30 - 16:00 WIB</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-2 bg-white rounded shadow-sm d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold"><i class="fas fa-user-clock text-warning me-1"></i> Indikator Waktu Reguler:</span>
                    <span id="punctual-status" class="badge bg-info text-dark telemetry-badge">Menganalisis Waktu...</span>
                </div>
            </div>
        </div>

        {{-- Form Absensi Utama --}}
        <div class="card card-custom">
            <h4 class="fw-bold text-dark mb-1">Modul Absensi Harian Reguler</h4>
            <p class="text-muted small mb-4">Sistem mencatat rekap kehadiran kerja tunggal non-shift berdasarkan parameter zona waktu Asia/Jakarta.</p>

            <form action="{{ Auth::user()->role === 'Admin' ? route('admin.attendance.store') : route('petugas.attendance.store') }}" method="POST" id="attendanceForm">
                @csrf
                
                {{-- Lemparan Data Status Penentu Aksi Form --}}
                <input type="hidden" name="attendance_info" value="{{ !$hasMasuk ? 'Masuk' : 'Pulang' }}">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Nama Petugas Operasional</label>
                        <input type="text" class="form-control form-control-custom" value="{{ Auth::user()->name }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Jenis Kehadiran Terdeteksi</label>
                        <input type="text" class="form-control form-control-custom text-fw-bold" 
                               value="{{ !$hasMasuk ? 'ABSEN MASUK (CLOCK IN)' : 'ABSEN PULANG (CLOCK OUT)' }}" readonly disabled>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label form-label-custom">Stamp Waktu Server Kontrol</label>
                        <input type="text" id="live-clock-input" name="manual_time" class="form-control form-control-custom" 
                               value="{{ \Carbon\Carbon::now()->timezone('Asia/Jakarta')->translatedFormat('d F Y | H:i') }} WIB" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label form-label-custom">Catatan / Keterangan Kendala Operasional</label>
                        <textarea name="notes" class="form-control form-control-custom" rows="4" 
                                  placeholder="{{ !$hasMasuk ? 'Tuliskan catatan kondisi perimeter siber masuk atau keterangan jika Anda terlambat...' : 'Tuliskan catatan ringkasan serah terima tugas sebelum meninggalkan workstation...' }}"></textarea>
                    </div>

                    <div class="col-12 text-end border-top pt-3 mt-4">
                        @if(!$hasMasuk)
                            <button type="submit" id="btn-submit-attendance" class="btn btn-attendance">
                                <i class="fas fa-check-square me-2"></i> Kirim Kehadiran Masuk (Clock In)
                            </button>
                        @else
                            <button type="submit" id="btn-submit-attendance" class="btn btn-attendance clock-out">
                                <i class="fas fa-sign-out-alt me-2"></i> Kirim Kehadiran Pulang (Clock Out)
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // 1. REAL-TIME RUNNING CLOCK & LIVE CLIENT-SIDE ANALYSIS
        function updateClockAndValidation() {
            const now = new Date();
            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            const dateStr = now.toLocaleDateString('id-ID', options);
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            const clockInput = document.getElementById('live-clock-input');
            const isPulangMode = {{ $hasMasuk ? 'true' : 'false' }};
            
            // Jam digital terus berjalan jika status rekam belum dikunci mati oleh controller
            if(clockInput) { 
                clockInput.value = `${dateStr} | ${hours}:${minutes} WIB`;
            }

            const statusBadge = document.getElementById('punctual-status');
            if (statusBadge) {
                const currentHr = now.getHours();
                const currentMin = now.getMinutes();

                if (!isPulangMode) {
                    // --- ATURAN COCK IN (Batas Toleransi Keterlambatan: 07:30) ---
                    if ((currentHr > 7) || (currentHr === 7 && currentMin > 30)) {
                        statusBadge.className = "badge bg-danger telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Terlambat Masuk`;
                    } else {
                        statusBadge.className = "badge bg-success telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-clock"></i> Tepat Waktu`;
                    }
                } else {
                    // --- ATURAN CLOCK OUT (Batas Pulang Cepat: 16:00) ---
                    if (currentHr < 16) {
                        statusBadge.className = "badge bg-warning telemetry-badge text-dark";
                        statusBadge.innerHTML = `<i class="fas fa-running"></i> Pulang Awal`;
                    } else {
                        statusBadge.className = "badge bg-success telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-check-circle"></i> Jam Pulang Sesuai`;
                    }
                }
            }
        }
        
        // Eksekusi trigger waktu berjalan setiap 1 detik
        setInterval(updateClockAndValidation, 1000);
        window.addEventListener('load', updateClockAndValidation);
    </script>
</body>
</html>