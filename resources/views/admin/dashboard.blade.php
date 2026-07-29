@extends('admin.admin-layout')

@section('title','Dashboard Admin')

@section('page_heading', 'Dashboard Admin SIP-O-SIBER')

@section('breadcrumb')
 Home > Menu Utama > <span class="text-dark">Dashboard Admin</span>
@endsection

@section('content')

{{-- =========================
SUMMARY CARD (4 Sejajar ke Samping)
========================= --}}
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-primary border-4">
            <div class="card-body p-4">
                <div class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">TOTAL INSIDEN</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalInsiden ?? 2 }}</h2>
                    <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-3">
                        <i class="fas fa-shield-virus"></i>
                    </div>
                </div>
                <div class="text-success small mt-3">
                    <i class="fas fa-arrow-trend-up me-1"></i> +12% dari bulan lalu
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-danger border-4">
            <div class="card-body p-4">
                <div class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">JUDI ONLINE (JUDOL)</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalJudol ?? 0 }}</h2>
                    <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-3">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>
                </div>
                <div class="text-danger small mt-3">Prioritas Tinggi</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-warning border-4">
            <div class="card-body p-4">
                <div class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">WEB DEFACEMENT</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalDefacement ?? 0 }}</h2>
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-3">
                        <i class="fas fa-globe"></i>
                    </div>
                </div>
                <div class="text-warning small mt-3">Kerentanan Aktif</div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-top border-info border-4">
            <div class="card-body p-4">
                <div class="text-muted fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">MALWARE INJECTION</div>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalMalware ?? 0 }}</h2>
                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-3">
                        <i class="fas fa-bug"></i>
                    </div>
                </div>
                <div class="text-muted small mt-3">Dalam Pemantauan</div>
            </div>
        </div>
    </div>
</div>

{{-- =========================
GRAFIK
========================= --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="overflow: visible !important;">
    <div class="card-body p-4" style="overflow: visible !important;">
        {{-- Header Card & Dropdown --}}
        <div class="d-flex justify-content-between align-items-center mb-4 position-relative" style="z-index: 1050;">
            <div>
                <h4 class="fw-bold mb-1">Statistik Serangan Siber</h4>
                <small class="text-muted">Tren laporan insiden selama tahun <span id="selectedYearLabel">{{ request('year', now()->year) }}</span></small>
            </div>
            
            {{-- Dropdown Pilih Tahun --}}
            <div>
                <form method="GET" action="" id="yearForm">
                    <select name="year" id="yearSelect" class="form-select form-select-sm fw-bold text-primary bg-light border-0 px-3 py-2 rounded-3 shadow-sm" style="cursor: pointer; min-width: 90px;" onchange="document.getElementById('yearForm').submit()">
                        @php
                            $currentYear = now()->year;
                        @endphp
                        @for($y = $currentYear; $y >= $currentYear - 4; $y--)
                            <option value="{{ $y }}" {{ (request('year', $currentYear) == $y) ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endfor
                    </select>
                </form>
            </div>
        </div>
        
        {{-- Container Grafik --}}
        <div style="position: relative; height: 320px; width: 100%;" class="pt-2">
            <canvas id="dashboardChart"></canvas>
        </div>
    </div>
</div>

{{-- =========================
AKTIVITAS TERBARU (MELEBAR PENUH KE SAMPING)
========================= --}}
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Aktivitas Terbaru</h5>
                <a href="{{ route('admin.validasi') }}" class="small text-decoration-none fw-semibold">Lihat Semua</a>
            </div>

            @isset($patrols)
                @forelse($patrols->take(5) as $p)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            {{-- Bulatan Biru Sempurna (Tidak Lonjong) --}}
                            <div class="bg-primary rounded-circle" style="width: 12px; height: 12px; min-width: 12px;"></div>
                            <div>
                                <div class="fw-semibold text-dark">{{ $p->user->name ?? 'Petugas Lapangan' }}</div>
                                <div class="text-muted small">{{ $p->kategori_insiden ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="text-end small text-muted">
                            {{ method_exists($p->created_at, 'diffForHumans') ? $p->created_at->diffForHumans() : $p->created_at }}
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">Belum ada aktivitas terbaru.</div>
                @endforelse
            @endisset
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('dashboardChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
                datasets: [{
                label: 'Statistik Insiden',
                data: @json($chartData),
                borderColor: '#2F6FED',
                backgroundColor: 'rgba(47,111,237,0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: '#2F6FED'
            }]  
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10
                    }
                },
                plugins: { 
                    legend: { display: false } 
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        suggestedMax: 3, 
                        grid: { color: '#EEF2F7' },
                        ticks: { stepSize: 1 }
                    },
                    x: { 
                        grid: { display: false } 
                    }
                }
            }
        });
    }
});
</script>
@endpush