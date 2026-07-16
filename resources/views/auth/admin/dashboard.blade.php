@extends('layouts.admin-layout')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="mt-4 font-weight-bold text-dark">Pusat Kendali Operasional Siber</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">SIP-O-SIBER Realtime Monitoring & Audit System</li>
            </ol>
        </div>
        <div class="text-end text-muted small">
            <i class="fas fa-clock me-1"></i> Sistem Waktu Nyata Actived
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow h-100 py-2 border-left-primary">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.8;">Total Insiden Terdeteksi</div>
                            <div class="h2 mb-0 font-weight-bold">{{ $totalInsiden }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shield-alt fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-danger text-white shadow h-100 py-2 border-left-danger">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.8;">Darurat Judi Online</div>
                            <div class="h2 mb-0 font-weight-bold">{{ $totalJudol }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dice fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-dark shadow h-100 py-2 border-left-warning">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.8; font-weight: 700;">Web Defacement</div>
                            <div class="h2 mb-0 font-weight-bold text-dark">{{ $totalDefacement }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-dark-50" style="opacity: 0.3;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white shadow h-100 py-2 border-left-success">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity: 0.8;">Malware Infection</div>
                            <div class="h2 mb-0 font-weight-bold">{{ $totalMalware }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-virus fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-xl-12">
            <div class="card shadow mb-4 border-0">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white border-bottom">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-line me-2"></i>Grafik Tren Eskalasi Serangan Siber (Tahunan)</h6>
                </div>
                <div class="card-body">
                    <div style="height: 320px; width: 100%;">
                        <canvas id="siberTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white border-bottom">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-folder-open me-2"></i>Log Kendali Laporan Patroli Siber Terbaru</h6>
            
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex gap-2">
                <div class="input-group">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari OPD / Kategori..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" width="100%" cellspacing="0">
                    <thead class="table-dark text-uppercase small">
                        <tr>
                            <th style="width: 10%;">ID Log</th>
                            <th style="width: 18%;">Petugas Lapangan</th>
                            <th style="width: 22%;">OPD Sasaran</th>
                            <th style="width: 20%;">Klaster Kategori</th>
                            <th style="width: 15%;">Tanggal Penemuan</th>
                            <th style="width: 10%;">Status Matrix</th>
                            <th style="width: 5%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patrols as $patrol)
                            <tr>
                                <td><span class="badge bg-light text-dark border font-monospace">{{ $patrol->id_log }}</span></td>
                                <td><strong>{{ $patrol->user->name ?? 'Anonim' }}</strong></td>
                                <td>{{ $patrol->opd_sasaran }}</td>
                                <td><span class="text-muted small"><i class="fas fa-tag me-1 text-secondary"></i>{{ $patrol->kategori_insiden }}</span></td>
                                <td><span class="small text-secondary">{{ $patrol->created_at->format('d M Y H:i') }}</span></td>
                                <td>
                                    @if($patrol->status === 'Pending')
                                        <span class="badge bg-warning text-dark w-100"><i class="fas fa-hourglass-half me-1"></i>Pending</span>
                                    @elseif($patrol->status === 'Perlu Perbaikan')
                                        <span class="badge bg-info text-dark w-100"><i class="fas fa-exclamation-triangle me-1"></i>Revisi</span>
                                    @else
                                        <span class="badge bg-success w-100"><i class="fas fa-check me-1"></i>Verified</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-dark shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modalStatus{{ $patrol->id }}">
                                        Tinjau
                                    </button>

                                    <div class="modal fade text-start" id="modalStatus{{ $patrol->id }}" tabindex="-1" aria-labelledby="modalStatusLabel{{ $patrol->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <form action="{{ route('admin.patrol.update-status', $patrol->id) }}" method="POST" class="modal-content text-dark border-0 shadow-lg">
                                                @csrf
                                                <div class="modal-header bg-dark text-white">
                                                    <h5 class="modal-title" id="modalStatusLabel{{ $patrol->id }}"><i class="fas fa-shield-alt me-2"></i>Evaluasi Log #{{ $patrol->id_log }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body bg-light">
                                                    <div class="card p-3 mb-3 border-0 shadow-sm">
                                                        <div class="row small text-muted">
                                                            <div class="col-6 mb-2"><strong>Petugas:</strong> {{ $patrol->user->name ?? 'Anonim' }}</div>
                                                            <div class="col-6 mb-2"><strong>Sasaran:</strong> {{ $patrol->opd_sasaran }}</div>
                                                            <div class="col-12"><strong>URL Target:</strong> <a href="{{ $patrol->target_url }}" target="_blank" class="text-decoration-none font-monospace text-truncate d-inline-block style-max-url">{{ $patrol->target_url }}</a></div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label font-weight-bold fw-bold text-secondary">Ubah Status Matriks Keputusan</label>
                                                        <select name="status" class="form-select border-2" required>
                                                            <option value="Approved" {{ $patrol->status === 'Verified' ? 'selected' : '' }}>Setujui & Verifikasi Laporan</option>
                                                            <option value="Rejection" {{ $patrol->status === 'Perlu Perbaikan' ? 'selected' : '' }}>Tolak & Buka Jalur Revisi</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label font-weight-bold fw-bold text-secondary">Catatan Koreksi Tambahan (Wajib Jika Ditolak)</label>
                                                        <textarea name="admin_correction" class="form-control border-2" rows="3" placeholder="Tulis instruksi perbaikan detail untuk petugas lapangan..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-white">
                                                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary px-4">Simpan Keputusan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-folder-open d-block fa-2x mb-2 text-secondary" style="opacity: 0.4;"></i>
                                    Tidak ada data rekaman aktivitas patroli siber yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="small text-muted">
                    Menampilkan {{ $patrols->firstItem() ?? 0 }} sampai {{ $patrols->lastItem() ?? 0 }} dari {{ $patrols->total() }} entri log.
                </div>
                <div>
                    {{ $patrols->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .style-max-url { max-width: 380px; vertical-align: middle; }
    .border-left-primary { border-left: 5px solid #4e73df !important; }
    .border-left-danger { border-left: 5px solid #e74a3b !important; }
    .border-left-warning { border-left: 5px solid #f6c23e !important; }
    .border-left-success { border-left: 5px solid #1cc88a !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('siberTrendChart').getContext('2d');
        
        // Memetakan data 12 bulan dari Backend Laravel secara aman
        const serverData = @json($chartData);
        const dynamicMonthlyTotals = Array(12).fill(0);
        
        if (Array.isArray(serverData)) {
            serverData.forEach(item => {
                if(item.month >= 1 && item.month <= 12) {
                    dynamicMonthlyTotals[item.month - 1] = item.total;
                }
            });
        }

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'],
                datasets: [{
                    label: 'Jumlah Kasus Teridentifikasi',
                    data: dynamicMonthlyTotals,
                    borderColor: 'rgba(78, 115, 223, 1)',
                    backgroundColor: 'rgba(78, 115, 223, 0.05)',
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                    pointBorderColor: 'rgba(78, 115, 223, 1)',
                    pointHoverRadius: 6,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        ticks: { stepSize: 1, color: '#858796' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#858796' }
                    }
                }
            }
        });
    });
</script>
@endsection