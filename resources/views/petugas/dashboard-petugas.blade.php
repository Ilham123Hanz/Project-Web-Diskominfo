@extends('petugas.petugas-layout.petugas-layout')

@section('title', 'Panel Operator Petugas Kompleks - Cyber Incident Command Center')

@push('styles')
<style>
    /* Tipografi & Base Styling */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
        background-color: #f8fafc;
    }

    /* Animation Utilities */
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.95); }
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(226, 232, 240, 0.8);
    }

    /* Stat Cards & Interaction */
    .stat-card {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px;
        border: 1px solid #e2e8f0 !important;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(15, 23, 42, 0.08), 0 8px 16px -6px rgba(15, 23, 42, 0.04) !important;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    .stat-card-primary::before { background-color: #3b82f6; }
    .stat-card-success::before { background-color: #10b981; }
    .stat-card-warning::before { background-color: #f59e0b; }
    .stat-card-danger::before { background-color: #ef4444; }

    /* Governance Table Styling */
    .table-gov {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-gov th {
        background-color: #f1f5f9;
        color: #334155;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.72rem;
        letter-spacing: 0.06em;
        border-bottom: 2px solid #cbd5e1;
        padding: 14px 16px;
        white-space: nowrap;
    }
    .table-gov td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
        background-color: #ffffff;
    }
    .table-gov tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-gov tbody tr:hover td {
        background-color: #f8fafc;
    }

    /* Status Badges */
    .badge-status-verified {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .badge-status-pending {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    .badge-status-revision {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    /* Digital Clock Widget */
    .clock-widget {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f8fafc;
        border-radius: 12px;
    }

    /* Form Control Enhancements */
    .input-gov-focus {
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .input-gov-focus:focus {
        border-color: #0f3057;
        box-shadow: 0 0 0 0.25rem rgba(15, 48, 87, 0.15);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 mt-4">
    
    <!-- Top Bar Header & Realtime Clock -->
    <div class="p-3 bg-white border rounded-3 mb-4 shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3 text-primary d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                <i class="fas fa-user-shield fa-a-lg"></i>
            </div>
            <div>
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark fs-6">Operator Aktif: <span class="text-primary">{{ Auth::user()->name }}</span></h6>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 rounded-pill px-2 py-0.5" style="font-size: 0.68rem;">
                        <i class="fas fa-circle text-success me-1 animate-pulse" style="font-size: 0.4rem;"></i> On Duty
                    </span>
                </div>
                <small class="text-muted fs-7">
                    <i class="far fa-calendar-alt me-1"></i>{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}
                </small>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <!-- Digital Real-time Clock -->
            <div class="clock-widget px-3 py-1.5 text-center shadow-sm d-none d-md-block">
                <div class="font-monospace fw-bold fs-6 text-warning" id="liveDigitalClock">00:00:00 WIB</div>
                <div style="font-size: 0.65rem;" class="text-light opacity-75">WAKTU SISTEM LOKAL</div>
            </div>

            <a href="{{ route('petugas.patrol.create') }}" class="btn btn-sm btn-primary fw-bold px-3 py-2 rounded-2 d-inline-flex align-items-center shadow-sm">
                <i class="fas fa-plus-circle me-1.5"></i> Buat Laporan Baru
            </a>
            <span class="badge bg-dark px-3 py-2 fw-bold font-monospace" style="font-size: 0.75rem;" data-bs-toggle="tooltip" title="IP Publik Operator">
                <i class="fas fa-network-wired me-1"></i> {{ request()->ip() }}
            </span>
        </div>
    </div>

    <!-- Alert Prioritas untuk Laporan Perlu Perbaikan -->
    @php
        $revisionCount = $patrols->getCollection()->whereIn('status', ['Perlu Perbaikan', 'Revision', 'Rejection'])->count();
    @endphp
    @if($revisionCount > 0)
    <div class="alert alert-warning border-start border-4 border-warning bg-white shadow-sm rounded-3 p-3 mb-4 fade show d-flex align-items-center justify-content-between" role="alert">
        <div class="d-flex align-items-center">
            <div class="p-2 bg-warning bg-opacity-20 text-warning rounded-circle me-3">
                <i class="fas fa-exclamation-triangle fa-lg"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0 text-dark">Perhatian Required!</h6>
                <p class="mb-0 text-muted small">Terdapat <strong>{{ $revisionCount }}</strong> laporan yang membutuhkan perbaikan data dari Anda. Silakan cek tabel di bawah.</p>
            </div>
        </div>
        <a href="#tableLogSection" class="btn btn-sm btn-warning fw-bold text-dark px-3 rounded-2">
            Perbaiki Sekarang <i class="fas fa-arrow-down ms-1"></i>
        </a>
    </div>
    @endif

    <!-- Alert Flash Messages -->
    @if(session('success')) 
        <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-semibold p-3 mb-4 rounded-3 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-check-circle fa-lg me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
    @endif

    @if($errors->any()) 
        <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-semibold p-3 mb-4 rounded-3 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
            <div>{{ $errors->first() }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
    @endif

    <div class="row">
        <!-- Kolom Kiri: Presensi, Shift Tracking & Quick Actions -->
        <div class="col-xl-3 col-lg-4 mb-4">
            
            <!-- Card Presensi -->
            <div class="card border-0 shadow-sm rounded-3 mb-4 glass-card">
                <div class="card-header bg-white fw-bold text-dark border-bottom p-3 d-flex align-items-center justify-content-between">
                    <span class="fs-6"><i class="fas fa-clock me-2 text-primary"></i> Presensi Shift Kerja</span>
                    <span class="badge bg-light text-secondary border fw-bold" style="font-size: 0.7rem;">WIB</span>
                </div>
                <div class="card-body p-3">
                    @if($todayAttendance)
                        <div class="alert border-0 bg-light text-dark mb-3 p-3 rounded-3 shadow-inner">
                            <div class="mb-2 d-flex align-items-center justify-content-between">
                                <span class="small text-muted fw-bold">Status Presensi:</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 fw-bold px-2 py-1">
                                    <i class="fas fa-circle text-success me-1 animate-pulse" style="font-size: 0.45rem;"></i> TERDISTRIBUSI
                                </span>
                            </div>
                            <hr class="my-2 text-muted opacity-25">
                            <div class="mb-2 small d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-sign-in-alt me-1 text-success"></i> Clock In:</span>
                                <strong class="text-dark">{{ $todayAttendance->time_in ? $todayAttendance->time_in . ' WIB' : '-' }}</strong>
                            </div>
                            <div class="mb-2 small d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-sign-out-alt me-1 text-danger"></i> Clock Out:</span>
                                <strong class="text-dark">{{ $todayAttendance->time_out ? $todayAttendance->time_out . ' WIB' : 'Belum Absen' }}</strong>
                            </div>
                            <div class="mb-0 small d-flex justify-content-between align-items-center">
                                <span class="text-muted"><i class="fas fa-hourglass-half me-1 text-warning"></i> Total Akumulasi:</span>
                                <span class="badge bg-dark font-monospace fw-normal">{{ $todayAttendance->duration ?? 'Sedang Berjalan' }}</span>
                            </div>
                        </div>

                        @if(!$hasPulang)
                            <div class="border-top pt-3">
                                <p class="small text-muted mb-2"><i class="fas fa-info-circle me-1 text-info"></i> Selesai piket? Laporkan kepulangan Anda:</p>
                                <form action="{{ route('petugas.attendance.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="action_type" value="clock_out">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark mb-1">Override Jam Pulang (Opsional)</label>
                                        <input type="time" name="manual_time" class="form-control input-gov-focus rounded-2" placeholder="Kosongkan jika real-time">
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100 rounded-2 fw-bold text-dark btn-sm py-2 shadow-sm d-flex align-items-center justify-content-center">
                                        <i class="fas fa-sign-out-alt me-1.5"></i> Clock Out (Presensi Pulang)
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info border-0 text-center small fw-bold mb-0 py-2 rounded-2">
                                <i class="fas fa-check-double me-1 text-info"></i> Sesi kerja hari ini telah diverifikasi & selesai.
                            </div>
                        @endif
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-clock text-muted fa-3x mb-2 opacity-50"></i>
                            <p class="small text-muted mb-3">Anda belum mengisi presensi kehadiran shift aktif hari ini.</p>
                            <a href="{{ route('petugas.attendance.form') }}" class="btn btn-danger btn-sm fw-bold w-100 py-2 shadow-sm d-inline-flex align-items-center justify-content-center">
                                <i class="fas fa-exclamation-circle me-1.5"></i> Masuk Absensi Shift
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Card Quick Action Shortcuts -->
            <div class="card border-0 shadow-sm p-3 bg-white rounded-3">
                <h6 class="fw-bold mb-3 text-dark d-flex align-items-center fs-6">
                    <i class="fas fa-bolt me-2 text-warning"></i> Pintasan Akses Cepat
                </h6>
                <div class="d-grid gap-2">
                    <a href="{{ route('petugas.patrol.create') }}" class="btn btn-outline-primary btn-sm text-start fw-semibold py-2 rounded-2 d-flex align-items-center">
                        <i class="fas fa-pen-alt me-2 text-primary"></i> Input Laporan Patroli Baru
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm text-start fw-semibold py-2 rounded-2 d-flex align-items-center" data-bs-toggle="tooltip" title="Gunakan template resmi laporan insiden">
                        <i class="fas fa-file-word me-2 text-primary"></i> Unduh Format Template
                    </a>
                    <button type="button" class="btn btn-outline-dark btn-sm text-start fw-semibold py-2 rounded-2 d-flex align-items-center" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2 text-dark"></i> Refresh Data Transmisi
                    </button>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Counter Metrics & Table -->
        <div class="col-xl-9 col-lg-8 mb-4">
            
            <!-- Statistical Cards Grid -->
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-white stat-card stat-card-primary">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted fw-bold d-block text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total Transmisi</small>
                                <span class="h3 fw-bold text-dark mb-0">{{ $patrols->total() }}</span>
                            </div>
                            <div class="p-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-folder-open fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-white stat-card stat-card-success">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-success fw-bold d-block text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Telah Diverifikasi</small>
                                <span class="h3 fw-bold text-success mb-0">
                                    {{ $patrols->getCollection()->whereIn('status', ['Verified', 'Approved', 'Disetujui Admin'])->count() }}
                                </span>
                            </div>
                            <div class="p-3 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-check-double fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-white stat-card stat-card-warning">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-warning fw-bold d-block text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Menunggu Validasi</small>
                                <span class="h3 fw-bold text-warning mb-0">
                                    {{ $patrols->getCollection()->whereIn('status', ['Pending', 'Menunggu Validasi'])->count() }}
                                </span>
                            </div>
                            <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-hourglass-half fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm p-3 bg-white stat-card stat-card-danger">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-danger fw-bold d-block text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Perlu Perbaikan</small>
                                <span class="h3 fw-bold text-danger mb-0">
                                    {{ $patrols->getCollection()->whereIn('status', ['Perlu Perbaikan', 'Revision', 'Rejection'])->count() }}
                                </span>
                            </div>
                            <div class="p-3 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <i class="fas fa-tools fa-lg"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Rekam Log Transmisi -->
            <div class="card border-0 shadow-sm p-4 bg-white rounded-3" id="tableLogSection">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                    <div>
                        <h5 class="fw-bold mb-1" style="color: #0f3057;">
                            <i class="fas fa-stream me-2 text-secondary"></i> Histori Transmisi Laporan Anda
                        </h5>
                        <p class="text-muted small mb-0">Catatan log digital atas pengerjaan dan temuan insiden siber milik akun Anda.</p>
                    </div>
                    <div>
                        <span class="badge bg-light text-dark border px-3 py-2 fw-semibold">
                            Total Records: {{ $patrols->total() }}
                        </span>
                    </div>
                </div>

                <!-- Form Filter & Pencarian -->
                <form action="{{ route('petugas.dashboard') }}" method="GET" class="row g-2 mb-4">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control input-gov-focus border-start-0 rounded-end" placeholder="Cari sasaran OPD, nama isu, atau kode tiket..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select name="folder" class="form-select input-gov-focus rounded-2">
                            <option value="">-- Semua Kategori Folder --</option>
                            <option value="Patroli Siber" {{ request('folder') == 'Patroli Siber' ? 'selected' : '' }}>Patroli Siber</option>
                            <option value="Bug Hunter" {{ request('folder') == 'Bug Hunter' ? 'selected' : '' }}>Bug Hunter</option>
                            <option value="CTI" {{ request('folder') == 'CTI' ? 'selected' : '' }}>CTI</option>
                            <option value="Laporan Insiden" {{ request('folder') == 'Laporan Insiden' ? 'selected' : '' }}>Laporan Insiden</option>
                            <option value="Sosial Media" {{ request('folder') == 'Sosial Media' ? 'selected' : '' }}>Sosial Media</option>
                            <option value="Vul/Pen Test" {{ request('folder') == 'Vul/Pen Test' ? 'selected' : '' }}>Vul/Pen Test</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-dark w-100 fw-bold rounded-2 shadow-sm d-flex align-items-center justify-content-center">
                            <i class="fas fa-filter me-1.5"></i> Filter
                        </button>
                    </div>
                </form>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-gov align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Kode Tiket</th>
                                <th>Klasifikasi / V-Drive</th>
                                <th>Instansi Target & Masalah</th>
                                <th>Bobot Ancaman</th>
                                <th>Status Matrix</th>
                                <th>Tanggapan Admin</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($patrols as $patrol)
                            <tr>
                                <td class="align-middle">
                                    <span class="badge bg-dark font-monospace px-2.5 py-1.5">{{ $patrol->log_code }}</span>
                                </td>
                                <td class="align-middle">
                                    <small class="d-block text-muted fw-bold">{{ $patrol->rumpun_kategori ?? 'Umum' }}</small>
                                    <span class="badge bg-secondary bg-opacity-75 font-monospace mt-1" style="font-size: 0.68rem;">[{{ $patrol->main_menu ?? 'N/A' }}]</span>
                                </td>
                                <td class="align-middle">
                                    <span class="fw-bold text-dark d-block mb-0.5" style="font-size: 0.88rem;">{{ $patrol->opd_sasaran }}</span>
                                    <span class="text-muted small d-block mb-1">{{ $patrol->kategori_insiden }}</span>
                                    @if(!empty($patrol->target_url))
                                        <a href="{{ $patrol->target_url }}" target="_blank" class="small text-truncate text-decoration-none d-inline-flex align-items-center text-primary" style="max-width: 180px;">
                                            <i class="fas fa-external-link-alt me-1" style="font-size: 0.7rem;"></i>Tautan Target
                                        </a>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ $patrol->threat_badge_color ?? 'secondary' }} px-2.5 py-1.5 fw-semibold">
                                        {{ $patrol->threat_level ?? 'Normal' }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    @if(in_array($patrol->status, ['Verified', 'Approved', 'Disetujui Admin']))
                                        <span class="badge badge-status-verified px-2.5 py-1.5 rounded-2 fw-bold d-inline-flex align-items-center">
                                            <i class="fas fa-check-circle me-1"></i> {{ $patrol->status }}
                                        </span>
                                    @elseif(in_array($patrol->status, ['Perlu Perbaikan', 'Revision', 'Rejection']))
                                        <span class="badge badge-status-revision px-2.5 py-1.5 rounded-2 fw-bold d-inline-flex align-items-center">
                                            <i class="fas fa-exclamation-triangle me-1"></i> {{ $patrol->status }}
                                        </span>
                                    @else
                                        <span class="badge badge-status-pending px-2.5 py-1.5 rounded-2 fw-bold d-inline-flex align-items-center">
                                            <i class="fas fa-hourglass-half me-1"></i> {{ $patrol->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if(in_array($patrol->status, ['Perlu Perbaikan', 'Revision', 'Rejection']))
                                        <div class="p-2 border border-danger border-opacity-30 rounded-2 bg-danger bg-opacity-10 text-danger fw-bold small">
                                            <i class="fas fa-exclamation-circle me-1"></i> {{ Str::limit($patrol->admin_correction ?? 'Perlu perbaikan data.', 35) }}
                                        </div>
                                    @elseif(in_array($patrol->status, ['Verified', 'Approved', 'Disetujui Admin']))
                                        <span class="text-success fw-bold small"><i class="fas fa-check-circle me-1"></i> Disetujui</span>
                                    @else
                                        <span class="text-muted small fst-italic"><i class="far fa-hourglass me-1 animate-pulse"></i> Menunggu review...</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <button type="button" class="btn btn-sm btn-outline-dark rounded-2" data-bs-toggle="modal" data-bs-target="#detailModal{{ $patrol->id }}" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal Detail Laporan -->
                            <div class="modal fade" id="detailModal{{ $patrol->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 shadow-lg">
                                        <div class="modal-header bg-dark text-white p-3">
                                            <h6 class="modal-title fw-bold">
                                                <i class="fas fa-shield-alt me-2 text-warning"></i> Detail Record Tiket: {{ $patrol->log_code }}
                                            </h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="text-muted small d-block">Instansi / OPD Target</label>
                                                    <strong class="text-dark">{{ $patrol->opd_sasaran }}</strong>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="text-muted small d-block">Kategori Insiden</label>
                                                    <strong class="text-dark">{{ $patrol->kategori_insiden }}</strong>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="text-muted small d-block">Tautan/URL Sasaran</label>
                                                    <a href="{{ $patrol->target_url }}" target="_blank" class="text-break">{{ $patrol->target_url ?? 'Tidak Ada Link' }}</a>
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="text-muted small d-block">Catatan & Respon Verifikator Admin</label>
                                                    <div class="p-3 bg-light border rounded-2 mt-1 text-dark">
                                                        {{ $patrol->admin_correction ?? 'Belum ada catatan perbaikan tambahan dari Admin.' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light p-2">
                                            <button type="button" class="btn btn-sm btn-secondary fw-bold px-3" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                                    <p class="mb-0 fw-semibold">Belum ada rekaman log data laporan siber dalam sistem.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <small class="text-muted">Menampilkan data {{ $patrols->firstItem() ?? 0 }} - {{ $patrols->lastItem() ?? 0 }} dari {{ $patrols->total() }} total records</small>
                    <div>
                        {{ $patrols->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Bootstrap Tooltip Initialization
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Realtime Clock Display Update
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const clockElement = document.getElementById('liveDigitalClock');
            if (clockElement) {
                clockElement.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    });
</script>
@endpush