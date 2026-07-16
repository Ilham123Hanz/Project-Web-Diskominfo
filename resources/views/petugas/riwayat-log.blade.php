<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Log Patroli - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; color: #333; font-family: 'Segoe UI', sans-serif; }
        .card-custom { background: #fff; border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px; }
        .table th { font-size: 0.75rem; text-uppercase; color: #7f8c8d; letter-spacing: 0.5px; font-weight: 700; }
        .row-revisi { background-color: #fff5f5 !important; }
        .badge-status { padding: 6px 12px; border-radius: 30px; font-size: 0.8rem; font-weight: 600; }
    </style>
</head>
<body>
<div class="container my-5">
    <div class="card card-custom">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">Riwayat Log Patroli Personal</h4>
                <p class="text-muted small mb-0">Pantau status laporan temuan insiden siber yang telah Anda kirimkan ke Admin.</p>
            </div>
            <div class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm bg-light" placeholder="Cari OPD atau ID Log..." style="width: 200px;">
                <select class="form-select form-select-sm bg-light" style="width: 140px;">
                    <option>Semua Status</option>
                    <option>Disetujui Admin</option>
                    <option>Menunggu Validasi</option>
                    <option>Perlu Perbaikan</option>
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl / ID Log</th>
                        <th>OPD Sasaran</th>
                        <th>Kategori Insiden</th>
                        <th class="text-center">Status Validasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>22 Jun 2026</strong><br><small class="text-muted">LOG-045</small></td>
                        <td class="fw-semibold text-secondary">BAPPEDA Lampung</td>
                        <td>Web Defacement</td>
                        <td class="text-center"><span class="badge bg-success-subtle text-success border border-success-subtle badge-status">Disetujui Admin</span></td>
                        <td class="text-center"><a href="#" class="btn btn-sm btn-link text-decoration-none fw-bold">Lihat Detail</a></td>
                    </tr>
                    <tr>
                        <td><strong>23 Jun 2026</strong><br><small class="text-muted">LOG-046</small></td>
                        <td class="fw-semibold text-secondary">Dinas Kesehatan</td>
                        <td>Judi Online (Judol)</td>
                        <td class="text-center"><span class="badge bg-warning-subtle text-warning border border-warning-subtle badge-status">Menunggu Validasi</span></td>
                        <td class="text-center"><a href="#" class="btn btn-sm btn-link text-decoration-none fw-bold">Lihat Detail</a></td>
                    </tr>
                    <tr class="row-revisi">
                        <td><strong>24 Jun 2026</strong><br><small class="text-muted">LOG-047</small></td>
                        <td class="fw-semibold text-secondary">Dinas Pendidikan</td>
                        <td>Malware Injection</td>
                        <td class="text-center"><span class="badge bg-danger-subtle text-danger border border-danger-subtle badge-status">Perlu Perbaikan</span></td>
                        <td class="text-center"><a href="#" class="btn btn-danger btn-sm px-3 fw-bold rounded-3">Edit Revisi</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 text-start">
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary btn-sm px-3"><i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>