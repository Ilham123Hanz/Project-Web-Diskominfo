<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Rekap Absensi Admin - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background-color: #f4f6f9; 
            color: #333333; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 15px; 
        }
        .navbar-gov-admin { 
            background-color: #0f3057; 
            border-bottom: 4px solid #ffc000; 
        }
        .card-gov { 
            background: #ffffff; 
            border: 1px solid #ccd6e0; 
            border-radius: 6px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
        }
        .table-gov th { 
            background-color: #0f3057 !important; 
            color: #ffffff !important; 
            font-weight: 600; 
            font-size: 0.9rem; 
            border-bottom: 2px solid #ffc000 !important; 
            padding: 14px 12px;
            white-space: nowrap;
        }
        .table-gov td { 
            color: #333333 !important; 
            font-size: 0.9rem; 
            background-color: #ffffff !important; 
            border-color: #e2e8f0 !important; 
            padding: 14px 12px;
        }
        .filter-panel {
            background-color: #ffffff;
            border-radius: 6px;
            border: 1px solid #ccd6e0;
        }
        .text-notes {
            font-size: 0.82rem;
            max-width: 200px;
            display: inline-block;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-gov-admin p-3 shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-white" style="font-size: 1.3rem;">
                <i class="fas fa-clipboard-list text-warning me-2"></i> REKAP MASTER ABSENSI REGULER
            </span>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm fw-bold px-3">
                    <i class="fas fa-arrow-left me-1"></i> KEMBALI KE DASHBOARD
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        {{-- Judul Modul --}}
        <div class="p-3 bg-white border rounded mb-3 shadow-sm d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0" style="color: #0f3057;">
                <i class="fas fa-history text-muted me-2"></i>Log Monitor Presensi Seluruh Personel
            </h4>
            <span class="badge bg-secondary py-2 px-3 fw-bold">Jam Kerja: 07:30 - 16:00 WIB</span>
        </div>

        {{-- Panel Filter Data Terintegrasi --}}
        <div class="filter-panel p-4 mb-4 shadow-sm">
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Tanggal Mulai</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Tanggal Selesai</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted text-uppercase">Status Ketepatan Masuk</label>
                    <select name="status_in" class="form-select form-select-sm">
                        <option value="">-- Semua Status --</option>
                        <option value="Tepat Waktu" {{ request('status_in') === 'Tepat Waktu' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="Terlambat" {{ request('status_in') === 'Terlambat' ? 'selected' : '' }}>Terlambat</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill fw-bold">
                        <i class="fas fa-filter me-1"></i> Filter Data
                    </button>
                    <a href="{{ route('admin.attendance.index') }}" class="btn btn-secondary btn-sm flex-fill fw-bold">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Tabel Data Rekap --}}
        <div class="card card-gov border-0 shadow-sm overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover table-gov align-middle mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Nama Petugas</th>
                            <th><i class="fas fa-calendar-alt me-2"></i>Tanggal</th>
                            <th><i class="fas fa-sign-in-alt me-2"></i>Jam Masuk</th>
                            <th><i class="fas fa-toggle-on me-2"></i>Status Masuk</th>
                            <th><i class="fas fa-comment-alt me-2"></i>Catatan Masuk</th>
                            <th><i class="fas fa-sign-out-alt me-2"></i>Jam Pulang</th>
                            <th><i class="fas fa-toggle-off me-2"></i>Status Pulang</th>
                            <th><i class="fas fa-comment-dots me-2"></i>Catatan Pulang</th>
                            <th><i class="fas fa-hourglass-half me-2"></i>Durasi Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark">{{ $attendance->user->name }}</span>
                            </td>
                            <td>
                                <span class="text-secondary fw-semibold">
                                    {{ $attendance->attendance_date ? $attendance->attendance_date->format('d/m/Y') : '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                    {{ $attendance->time_in ? $attendance->time_in . ' WIB' : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($attendance->status_in === 'Terlambat')
                                    <span class="badge bg-danger px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i>Terlambat</span>
                                @else
                                    <span class="badge bg-success px-2 py-1"><i class="fas fa-check me-1"></i>Tepat Waktu</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-notes text-muted" title="{{ $attendance->notes_in }}">
                                    {{ $attendance->notes_in ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-bold">
                                    {{ $attendance->time_out ? $attendance->time_out . ' WIB' : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($attendance->status_out === 'Pulang Awal')
                                    <span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-running me-1"></i>Pulang Awal</span>
                                @elseif($attendance->status_out === 'Selesai')
                                    <span class="badge bg-success px-2 py-1"><i class="fas fa-check-circle me-1"></i>Selesai</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-notes text-muted" title="{{ $attendance->notes_out }}">
                                    {{ $attendance->notes_out ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-primary">{{ $attendance->duration }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-3 text-secondary d-block"></i>
                                Belum ada data log rekap presensi personel yang terfilter atau terekam ke database.
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Render Link Pagination --}}
        <div class="d-flex justify-content-center mt-3">
            {{ $attendances->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>