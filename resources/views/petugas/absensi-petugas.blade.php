@extends('petugas.petugas-layout.petugas-layout')

@section('title', 'Modul Absensi Harian Reguler - SIP-O-SIBER')

@push('styles')
<style>
    :root {
        --absensi-navy: #0B1D33;
        --absensi-blue: #0052A3;
        --absensi-emerald: #10B981;
        --absensi-rose: #EF4444;
        --absensi-amber: #F59E0B;
        --absensi-bg-slate: #F8FAFC;
    }

    .main-container-absensi {
        max-width: 960px;
        margin: 24px auto;
    }
    
    .card-custom {
        background: #FFFFFF;
        border-radius: 16px;
        border: 1px solid #E2E8F0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        padding: 36px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
    }

    .card-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--absensi-blue), #00D2FF, var(--absensi-emerald));
    }

    .form-label-custom {
        font-size: 0.76rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 8px;
        display: block;
    }

    .input-group-absensi {
        position: relative;
    }

    .input-icon-left {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94A3B8;
        z-index: 5;
        font-size: 0.95rem;
    }

    .form-control-custom {
        background-color: var(--absensi-bg-slate);
        border: 1.5px solid #CBD5E1;
        padding: 12px 16px 12px 44px;
        font-size: 0.925rem;
        font-weight: 600;
        color: #0F172A;
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
    }

    .form-control-custom:focus {
        background-color: #FFFFFF;
        border-color: var(--absensi-blue);
        box-shadow: 0 0 0 4px rgba(0, 82, 163, 0.12);
        outline: none;
    }

    .form-control-custom:disabled, 
    .form-control-custom[readonly] {
        background-color: #F1F5F9;
        color: #334155;
        border-color: #E2E8F0;
        cursor: not-allowed;
    }

    textarea.form-control-custom {
        padding-left: 16px;
    }

    /* Interactive Action Buttons */
    .btn-attendance {
        background-color: var(--absensi-emerald);
        color: #FFFFFF;
        font-size: 0.95rem;
        font-weight: 700;
        padding: 14px 32px;
        border-radius: 10px;
        border: none;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.25);
    }

    .btn-attendance:hover {
        background-color: #059669;
        color: #FFFFFF;
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        transform: translateY(-2px);
    }

    .btn-attendance.clock-out {
        background-color: var(--absensi-rose);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.25);
    }

    .btn-attendance.clock-out:hover {
        background-color: #DC2626;
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    }

    .telemetry-card {
        background-color: #FFFFFF;
        border: 1px solid #E2E8F0;
        border-radius: 12px;
        padding: 16px 20px;
        transition: all 0.2s ease;
    }

    .telemetry-card:hover {
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
        border-color: #CBD5E1;
    }

    .telemetry-badge {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 7px 16px;
        border-radius: 8px;
        letter-spacing: 0.3px;
    }

    .status-completed-box {
        background: linear-gradient(135deg, #F0FDF4 0%, #DCFCE7 100%);
        border: 1.5px solid #BBF7D0;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="main-container-absensi">
        
        <!-- Header Navigasi -->
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            @php
                $userRole = strtolower(Auth::user()->role ?? 'petugas');
                $dashboardRoute = in_array($userRole, ['admin', 'superadmin']) ? route('admin.dashboard') : route('petugas.dashboard');
                $storeRoute = in_array($userRole, ['admin', 'superadmin']) ? route('admin.attendance.store') : route('petugas.attendance.store');
            @endphp
            <a href="{{ $dashboardRoute }}" class="btn btn-outline-secondary btn-sm rounded-3 fw-bold px-3 py-2">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Dashboard
            </a>
            <span class="badge bg-dark font-monospace px-3 py-2 rounded-3 shadow-sm" style="font-size: 0.8rem;">
                <i class="fas fa-network-wired me-2 text-info"></i> IP Terdeteksi: {{ request()->ip() }}
            </span>
        </div>

        <!-- Flash Message Notifikasi -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert" style="border-left: 4px solid var(--absensi-emerald) !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-circle-check fa-lg me-3 text-success"></i>
                    <div class="fw-semibold">{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4 rounded-3" role="alert" style="border-left: 4px solid var(--absensi-rose) !important;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-triangle-exclamation fa-lg me-3 text-danger"></i>
                    <div class="fw-semibold">{{ session('error') }}</div>
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
                    <span class="text-secondary small fw-bold"><i class="fas fa-user-clock text-warning me-2"></i> Status Ketepatan Waktu:</span>
                    <span id="punctual-status" class="badge bg-info text-dark telemetry-badge">Menganalisis...</span>
                </div>
            </div>
        </div>

        <!-- Panel Informasi Jika Presensi Hari Ini Sudah Lengkap -->
        @if(!empty($hasMasuk) && !empty($hasPulang))
            <div class="status-completed-box d-flex align-items-center justify-content-between shadow-sm flex-wrap gap-2">
                <div>
                    <h6 class="fw-bold text-success mb-1" style="font-size: 1.05rem;">
                        <i class="fas fa-circle-check me-2"></i> Presensi Hari Ini Selesai
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
                <h4 class="fw-bold mb-1" style="color: #0B1D33; letter-spacing: -0.3px;">Modul Absensi Harian Reguler</h4>
                <p class="text-muted small mb-0" style="font-size: 0.88rem;">Sistem mencatat rekap kehadiran kerja tunggal non-shift berdasarkan parameter waktu server Asia/Jakarta.</p>
            </div>

            <form action="{{ $storeRoute }}" method="POST" id="attendanceForm">
                @csrf
                
                <!-- Parameter Tersembunyi Kebutuhan Controller -->
                <input type="hidden" name="attendance_info" value="{{ empty($hasMasuk) ? 'Masuk' : 'Pulang' }}">
                <input type="hidden" name="action_type" value="{{ empty($hasMasuk) ? 'clock_in' : 'clock_out' }}">

                <div class="row g-3">
                    <!-- Nama Petugas -->
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Nama Petugas Operasional</label>
                        <div class="input-group-absensi">
                            <i class="fas fa-user-check input-icon-left"></i>
                            <input type="text" class="form-control form-control-custom" value="{{ Auth::user()->name }}" readonly>
                        </div>
                    </div>

                    <!-- Jenis Kehadiran -->
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Jenis Kehadiran Terdeteksi</label>
                        <div class="input-group-absensi">
                            <i class="fas fa-fingerprint input-icon-left"></i>
                            @if(empty($hasMasuk))
                                <input type="text" class="form-control form-control-custom fw-bold text-success" value="ABSEN MASUK (CLOCK IN)" readonly>
                            @elseif(empty($hasPulang))
                                <input type="text" class="form-control form-control-custom fw-bold text-danger" value="ABSEN PULANG (CLOCK OUT)" readonly>
                            @else
                                <input type="text" class="form-control form-control-custom fw-bold text-secondary" value="PRESENSI HARI INI TERCATAT" readonly>
                            @endif
                        </div>
                    </div>

                    <!-- Timestamp Live Server -->
                    <div class="col-md-12">
                        <label class="form-label form-label-custom">Stamp Waktu Server Kontrol</label>
                        <div class="input-group-absensi">
                            <i class="fas fa-clock input-icon-left"></i>
                            <input type="text" id="live-clock-input" name="manual_time" class="form-control form-control-custom font-monospace" 
                                   value="{{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y | H:i') }} WIB" readonly>
                        </div>
                    </div>

                    <!-- Catatan Kendala / Ringkasan -->
                    <div class="col-12">
                        <label class="form-label form-label-custom">Catatan / Keterangan Kendala Operasional</label>
                        @if(!empty($hasMasuk) && !empty($hasPulang))
                            <textarea name="notes" class="form-control form-control-custom" rows="3" disabled readonly 
                                      placeholder="Catatan Masuk: {{ $presensiHariIni->catatan_masuk ?? '-' }} &#10;Catatan Pulang: {{ $presensiHariIni->catatan_pulang ?? '-' }}"></textarea>
                        @else
                            <textarea name="notes" class="form-control form-control-custom" rows="3" 
                                      placeholder="{{ empty($hasMasuk) ? 'Tuliskan catatan kondisi perimeter siber masuk atau keterangan jika Anda terlambat...' : 'Tuliskan catatan ringkasan serah terima tugas sebelum meninggalkan workstation...' }}"></textarea>
                        @endif
                    </div>

                    <!-- Submit Action Button -->
                    <div class="col-12 text-end border-top pt-4 mt-4">
                        @if(empty($hasMasuk))
                            <button type="submit" id="btn-submit-attendance" class="btn btn-attendance w-100 w-md-auto">
                                <i class="fas fa-sign-in-alt me-2"></i> Kirim Kehadiran Masuk (Clock In)
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

        // Real-Time Running Clock & Evaluasi Ketepatan Waktu
        function updateClockAndValidation() {
            const now = new Date();

            if (!isCompleted && clockInput) { 
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                const dateStr = now.toLocaleDateString('id-ID', options);
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                
                clockInput.value = `${dateStr} | ${hours}:${minutes} WIB`;
            }

            if (statusBadge) {
                if (isCompleted) {
                    statusBadge.className = "badge bg-secondary telemetry-badge text-white";
                    statusBadge.innerHTML = `<i class="fas fa-check-double me-1"></i> Presensi Selesai`;
                    return;
                }

                const currentHr = now.getHours();
                const currentMin = now.getMinutes();

                if (!isPulangMode) {
                    // Batas Toleransi Masuk: 07:30 WIB
                    if ((currentHr > 7) || (currentHr === 7 && currentMin > 30)) {
                        statusBadge.className = "badge bg-danger telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-triangle-exclamation me-1"></i> Terlambat Masuk`;
                    } else {
                        statusBadge.className = "badge bg-success telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-circle-check me-1"></i> Tepat Waktu`;
                    }
                } else {
                    // Batas Pulang Normal: 16:00 WIB
                    if (currentHr < 16) {
                        statusBadge.className = "badge bg-warning telemetry-badge text-dark";
                        statusBadge.innerHTML = `<i class="fas fa-person-walking-arrow-right me-1"></i> Pulang Cepat`;
                    } else {
                        statusBadge.className = "badge bg-success telemetry-badge text-white";
                        statusBadge.innerHTML = `<i class="fas fa-circle-check me-1"></i> Jam Pulang Sesuai`;
                    }
                }
            }
        }
        
        if (!isCompleted) {
            setInterval(updateClockAndValidation, 1000);
        }
        updateClockAndValidation();

        // Prevensi Double Submit
        if (attendanceForm && submitBtn) {
            attendanceForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i> Memproses Presensi...`;
            });
        }
    });
</script>
@endpush