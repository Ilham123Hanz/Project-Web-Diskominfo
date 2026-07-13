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
            font-size: 0.95rem; 
            border-bottom: 2px solid #ffc000 !important; 
            padding: 14px 12px;
        }
        .table-gov td { 
            color: #333333 !important; 
            font-size: 0.95rem; 
            background-color: #ffffff !important; 
            border-color: #e2e8f0 !important; 
            padding: 14px 12px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-gov-admin p-3 shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-white" style="font-size: 1.3rem;">
                <i class="fas fa-clipboard-list text-warning me-2"></i> REKAP ABSENSI KELOMPOK MAGANG
            </span>
            <div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-sm fw-bold px-3">
                    <i class="fas fa-arrow-left me-1"></i> KEMBALI KE DASHBOARD
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        <div class="p-3 bg-white border rounded mb-4 shadow-sm">
            <h4 class="fw-bold mb-0" style="color: #0f3057;">
                <i class="fas fa-history text-muted me-2"></i>Log Absensi Harian Seluruh Petugas
            </h4>
        </div>

        <div class="card card-gov border-0 shadow-sm overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover table-gov align-middle mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Nama Petugas</th>
                            <th><i class="fas fa-calendar-alt me-2"></i>Tanggal Tugas</th>
                            <th><i class="fas fa-clock me-2"></i>Jam Masuk Absen</th>
                            <th><i class="fas fa-info-circle me-2"></i>Keterangan Shift / Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <span class="fw-bold text-dark" style="font-size: 1.02rem;">{{ $attendance->user->name }}</span>
                            </td>
                            <td>
                                <span class="text-secondary fw-semibold">
                                    {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-success px-3 py-2 fw-bold" style="font-size: 0.85rem;">
                                    <i class="far fa-clock me-1"></i> {{ $attendance->time_in }} WIB
                                </span>
                            </td>
                            <td>
                                <span class="text-dark fw-medium">{{ $attendance->shift }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-3 text-secondary d-block"></i>
                                Belum ada data log presensi petugas yang terekam masuk ke dalam sistem harian.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>