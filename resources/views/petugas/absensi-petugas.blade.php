@extends('petugas.petugas-layout.petugas-layout')

@section('title', 'Modul Absensi Harian Reguler - SIP-O-SIBER')

@push('styles')
<style>
    .main-container-absensi {
        max-width: 920px;
        margin: 20px auto;
    }
    
    .card-custom {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        padding: 32px;
        margin-bottom: 25px;
    }

    .form-label-custom {
        font-size: 0.78rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-custom {
        background-color: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 12px 16px;
        font-size: 0.95rem;
        font-weight: 600;
        color: #0f172a;
        border-radius: 8px;
        transition: all 0.2s ease-in-out;
    }

    .form-control-custom:focus {
        background-color: #ffffff;
        border-color: #0052a3;
        box-shadow: 0 0 0 3px rgba(0, 82, 163, 0.15);
        outline: none;
    }

    .form-control-custom:disabled, 
    .form-control-custom[readonly] {
        background-color: #f1f5f9;
        color: #334155;
        border-color: #e2e8f0;
    }

    .btn-attendance {
        background-color: #10b981;
        color: #ffffff;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 14px 32px;
        border-radius: 8px;
        border: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-attendance:hover {
        background-color: #059669;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        transform: translateY(-1px);
    }

    .btn-attendance.clock-out {
        background-color: #ef4444;
    }

    .btn-attendance.clock-out:hover {
        background-color: #dc2626;
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.35);
    }

    .telemetry-card {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 18px;
        transition: all 0.2s ease;
    }

    .telemetry-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
    }

    .telemetry-badge {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 6px 14px;
        border-radius: 6px;
        letter-spacing: 0.3px;
    }

    .status-completed-box {
        background-color: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-radius: 10px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="main-container-absensi">
        
        <!-- Header Navigasi / Kembali ke Dashboard Sesuai Role -->
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            @php
                $userRole = strtolower(Auth::user()->role ?? 'petugas');
                $dashboardRoute = in_array($userRole, ['admin', 'superadmin']) ? route('admin.dashboard') : route('petugas.dashboard');
                $storeRoute = in_array($userRole, ['admin', 'superadmin']) ? route('admin.attendance.store') : route('petugas.attendance.store');
            @endphp
            <a href="{{ $dashboardRoute }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-bold px-3 py-2">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Dashboard
            </a>
            <span class="badge bg-dark font-monospace px-3 py-2 rounded-2" style="font-size: 0.8rem;">
                <i class="fas fa-network-wired me-1.5 text-info"></i> IP: {{ request()->ip() }}
            </span>
        </div>

        <!-- Flash Message Notifikasi -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert" style="border-left: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-lg me-2 text-success"></i>
                    <div class="fw-medium">{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert" style="border-left: 4px solid #ef4444 !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle fa-lg me-2 text-danger"></i>
                    <div class="fw-medium">{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Row Indikator Status Absensi Waktu Nyata -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="telemetry-card shadow-sm d-flex justify-content-between align-items-center">
                    <span class="text-secondary small fw-bold"><i class="fas fa-business-time text-primary me-2"></i> Jam Kerja Berlaku:</span>
                    <span class="badge bg-secondary telemetry-badge">07:30 - 16:00 WIB</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="telemetry-card shadow-sm d-flex justify-content-between align-items-center">
                    <span class="text-secondary small fw-bold"><i class="fas fa-user-clock text-warning me-2"></i> Indikator Waktu Reguler:</span>
                    <span id="punctual-status" class="badge bg-info text-dark telemetry-badge">Menganalisis Waktu...</span>
                </div>
            </div>
        </div>

        <!-- Panel Informasi Jika Presensi Hari Ini Sudah Lengkap -->
        @if(!empty($hasMasuk) && !empty($hasPulang))
            <div class="status-completed-box d-flex align-items-center justify-content-between shadow-sm flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-success mb-1" style="font-size: 1.05rem;">
                        <i class="fas fa-check-circle me-2"></i> Presensi Hari Ini Telah Selesai
                    </h6>
                    <p class="text-muted small mb-0" style="font-size: 0.88rem;">
                        Masuk: <strong class="text-dark">{{ $presensiHariIni->jam_masuk ?? '-' }} WIB</strong> &nbsp;|&nbsp; 
                        Pulang: <strong class="text-dark">{{ $presensiHariIni->jam_pulang ?? '-' }} WIB</strong>
                    </p>
                </div>
                <span class="badge bg-success px-3 py-2 font-monospace rounded-2" style="font-size: 0.82rem; letter-spacing: 0.5px;">VERIFIED</span>
            </div>
        @endif

        <!-- Form Absensi Utama -->
        <div class="card card-custom">
            <div class="border-bottom pb-3 mb-4">
                <h4 class="fw-bold mb-1" style="color: #0a1d37; letter-spacing: -0.3px;">Modul Absensi Harian Reguler</h4>
                <p class="text-muted small mb-0" style="font-size: 0.88rem;">Sistem mencatat rekap kehadiran kerja tunggal non-shift berdasarkan parameter zona waktu Asia/Jakarta.</p>
            </div>

            <form action="{{ $storeRoute }}" method="POST" id="attendanceForm">
                @csrf
                
                <!-- Parameter Tersembunyi Kebutuhan PresensiController -->
                <input type="hidden" name="attendance_info" value="{{ empty($hasMasuk) ? 'Masuk' : 'Pulang' }}">
                <input type="hidden" name="action_type" value="{{ empty($hasMasuk) ? 'clock_in' : 'clock_out' }}">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Nama Petugas Operasional</label>
                        <input type="text" class="form-control form-control-custom" value="{{ Auth::user()->name }}" readonly>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Jenis Kehadiran Terdeteksi</label>
                        @if(empty($hasMasuk))
                            <input type="text" class="form-control form-control-custom fw-bold text-success" value="ABSEN MASUK (CLOCK IN)" readonly disabled>
                        @elseif(empty($hasPulang))
                            <input type="text" class="form-control form-control-custom fw-bold text-danger" value="ABSEN PULANG (CLOCK OUT)" readonly disabled>
                        @else
                            <input type="text" class="form-control form-control-custom fw-bold text-secondary" value="PRESENSI HARI INI TERCATAT" readonly disabled>
                        @endif
                    </div>

                    <div class="col-md-12">
                        <label class="form-label form-label-custom">Stamp Waktu Server Kontrol</label>
                        <input type="text" id="live-clock-input" name="manual_time" class="form-control form-control-custom font-monospace" 
                               value="{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y | H:i') }} WIB" readonly>
                    </div>

                    <div class="col-12">
                        <label class="form-label form-label-custom">Catatan / Keterangan Kendala Operasional</label>
                        @if(!empty($hasMasuk) && !empty($hasPulang))
                            <textarea name="notes" class="form-control form-control-custom" rows="3" disabled readonly 
                                      placeholder="Catatan Masuk: {{ $presensiHariIni->catatan_masuk ?? '-' }} &#10;Catatan Pulang: {{ $presensiHariIni->catatan_pulang ?? '-' }}"></textarea>
                        @else
                            <textarea name="notes" class="form-control form-control-custom" rows="4" 
                                      placeholder="{{ empty($hasMasuk) ? 'Tuliskan catatan kondisi perimeter siber masuk atau keterangan jika Anda terlambat...' : 'Tuliskan catatan ringkasan serah terima tugas sebelum meninggalkan workstation...' }}"></textarea>
                        @endif
                    </div>

                    <div class="col-12 text-end border-top pt-4 mt-4">
                        @if(empty($hasMasuk))
                            <button type="submit" id="btn-submit-attendance" class="btn btn-attendance w-100 w-md-auto">
                                <i class="fas fa-check-square me-2"></i> Kirim Kehadiran Masuk (Clock In)
                            </button>
                        @elseif(empty($hasPulang))
                            <button type="submit" id="btn-submit-attendance" class="btn btn-attendance clock-out w-100 w-md-auto">
                                <i class="fas fa-sign-out-alt me-2"></i> Kirim Kehadiran Pulang (Clock Out)
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary w-100 w-md-auto py-2.5 fw-bold" disabled>
                                <i class="fas fa-lock me-2"></i> Presensi Hari Ini Sudah Dikunci
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isPulangMode = {{ !empty($hasMasuk) ? 'true' : 'false' }};
        const isCompleted = {{ (!empty($hasMasuk) && !empty($hasPulang)) ? 'true' : 'false' }};
        
        const clockInput = document.getElementById('live-clock-input');
        const statusBadge = document.getElementById('punctual-status');
        const attendanceForm = document.getElementById('attendanceForm');
        const submitBtn = document.getElementById('btn-submit-attendance');

        // 1. REAL-TIME RUNNING CLOCK & ANALISIS STATUS KETEPATAN WAKTU
        function updateClockAndValidation() {
            const now = new Date();

            // Jalankan jam real-time jika presensi belum selesai dikunci
            if (!isCompleted && clockInput) { 
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                const dateStr = now.toLocaleDateString('id-ID', options);
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                
                clockInput.value = `${dateStr} | ${hours}:${minutes} WIB`;
            }

            // Indikator Ketepatan Waktu Real-Time
            if (statusBadge) {
                if (isCompleted) {
                    statusBadge.className = "badge bg-secondary telemetry-badge text-white";
                    statusBadge.innerHTML = `<i class="fas fa-check-double me-1"></i> Presensi Selesai`;
                    return;
                }

                const currentHr = now.getHours();
                const currentMin = now.getMinutes();

                if (!isPulangMode) {
                    // --- ATURAN CLOCK IN (Batas Toleransi: 07:30 WIB) ---
                    if ((currentHr > 7) || (currentHr === 7 && currentMin > 30)) {
                        statusBadge.className = "badge bg-danger telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-exclamation-triangle me-1"></i> Terlambat Masuk`;
                    } else {
                        statusBadge.className = "badge bg-success telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-clock me-1"></i> Tepat Waktu`;
                    }
                } else {
                    // --- ATURAN CLOCK OUT (Batas Pulang Cepat: 16:00 WIB) ---
                    if (currentHr < 16) {
                        statusBadge.className = "badge bg-warning telemetry-badge text-dark";
                        statusBadge.innerHTML = `<i class="fas fa-running me-1"></i> Pulang Awal`;
                    } else {
                        statusBadge.className = "badge bg-success telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-check-circle me-1"></i> Jam Pulang Sesuai`;
                    }
                }
            }
        }
        
        // Timer Interval Real-Time
        if (!isCompleted) {
            setInterval(updateClockAndValidation, 1000);
        }
        updateClockAndValidation();

        // 2. PREVENSI SUBMIT GANDA (ANTI DOUBLE CLICK)
        if (attendanceForm && submitBtn) {
            attendanceForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> Mengirim Data Presensi...`;
            });
        }
    });
</script>
@endpush