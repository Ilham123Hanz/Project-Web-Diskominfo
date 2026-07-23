@extends('petugas.petugas-layout.petugas-layout')

@section('title', 'Riwayat Log Patroli Personal - SIP-O-SIBER')

@push('styles')
<style>
    /* Tipografi Base System & Keterbacaan Visual */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }

    .main-container-history {
        max-width: 1120px;
        margin: 24px auto;
    }

    .card-custom {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.01);
        padding: 32px;
    }

    /* Table Optimization */
    .table {
        margin-bottom: 0;
    }
    .table th {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #475569;
        letter-spacing: 0.05em;
        font-weight: 700;
        background-color: #f8fafc;
        border-bottom: 2px solid #e2e8f0;
        padding: 12px 16px;
    }
    .table td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Highlight Row Revisi */
    .row-revisi {
        background-color: #fef2f2 !important;
    }
    .row-revisi:hover {
        background-color: #fee2e2 !important;
    }

    /* Custom Badges */
    .badge-status {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.01em;
    }
    .badge-threat {
        font-size: 0.68rem;
        padding: 3px 8px;
        border-radius: 4px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    /* Search & Filter Container */
    .search-filter-box {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 18px;
    }

    /* Form Inputs Optimization */
    .input-gov-sm {
        font-size: 0.85rem;
        border-color: #cbd5e1;
    }
    .input-gov-sm:focus {
        border-color: #0f3057;
        box-shadow: 0 0 0 0.2rem rgba(15, 48, 87, 0.15);
    }

    /* Button Styling */
    .btn-gov-petugas {
        background-color: #0f3057;
        color: #ffffff;
        border: none;
        transition: all 0.2s ease-in-out;
    }
    .btn-gov-petugas:hover {
        background-color: #0a213d;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(15, 48, 87, 0.25);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="main-container-history">
        
        <!-- Header Navigasi / Kembali ke Dashboard -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-bold px-3 py-1.5">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Dashboard
            </a>
            <span class="badge bg-dark font-monospace px-3 py-2" style="font-size: 0.75rem;">
                <i class="fas fa-network-wired me-1"></i> IP: {{ request()->ip() }}
            </span>
        </div>

        <!-- Flash Message Alerts -->
        @if(session('success'))
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-semibold p-3 mb-4 rounded-3 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle fa-lg me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-semibold p-3 mb-4 rounded-3 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-circle fa-lg me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card Utamanya -->
        <div class="card card-custom">
            <!-- Title & Search Bar Area -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color: #0f3057;">Riwayat Log Patroli Personal</h4>
                    <p class="text-muted small mb-0">Pantau status laporan temuan insiden siber yang telah Anda kirimkan dan lakukan perbaikan jika diminta oleh Admin.</p>
                </div>
                <div>
                    <a href="{{ route('petugas.patrol.create') }}" class="btn btn-gov-petugas btn-sm px-3 py-2 rounded-2 fw-bold d-inline-flex align-items-center">
                        <i class="fas fa-plus-circle me-1.5"></i> Input Log Baru
                    </a>
                </div>
            </div>

            <!-- Panel Filter Interaktif -->
            <div class="search-filter-box mb-4">
                <div class="row g-2">
                    <div class="col-md-7">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-search"></i></span>
                            <input type="text" id="tableSearchInput" class="form-control input-gov-sm border-start-0 rounded-end-2" placeholder="Cari OPD, ID Log, atau Kategori Insiden...">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="statusFilterSelect" class="form-select input-gov-sm rounded-2">
                            <option value="ALL">-- Semua Status Validasi --</option>
                            <option value="Approved">Disetujui Admin</option>
                            <option value="Pending">Menunggu Validasi</option>
                            <option value="Revision">Perlu Perbaikan (Revisi)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Log Patroli -->
            <div class="table-responsive rounded-2 border">
                <table class="table table-hover align-middle" id="historyTable">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Tgl / ID Log</th>
                            <th style="width: 25%;">OPD Sasaran</th>
                            <th style="width: 22%;">Kategori & Ancaman</th>
                            <th style="width: 20%;" class="text-center">Status Validasi</th>
                            <th style="width: 18%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs ?? [] as $log)
                            @php
                                $statusBadge = '';
                                $rowClass = '';

                                $currentStatus = $log->status ?? 'Pending';

                                if ($currentStatus === 'Approved' || $currentStatus === 'Disetujui Admin') {
                                    $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 badge-status"><i class="fas fa-check-circle"></i> Disetujui Admin</span>';
                                } elseif ($currentStatus === 'Revision' || $currentStatus === 'Perlu Perbaikan') {
                                    $rowClass = 'row-revisi';
                                    $statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 badge-status"><i class="fas fa-exclamation-triangle"></i> Perlu Perbaikan</span>';
                                } else {
                                    $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 badge-status" style="color: #856404 !important;"><i class="fas fa-hourglass-half"></i> Menunggu Validasi</span>';
                                }

                                $threatBadgeClass = match($log->threat_level ?? 'Medium') {
                                    'Critical' => 'bg-danger text-white',
                                    'High' => 'bg-warning text-dark',
                                    'Medium' => 'bg-info text-dark',
                                    default => 'bg-secondary text-white',
                                };
                            @endphp
                            <tr class="{{ $rowClass }}" data-status="{{ $currentStatus }}">
                                <td>
                                    <strong class="text-dark d-block">{{ \Carbon\Carbon::parse($log->created_at ?? now())->translatedFormat('d M Y') }}</strong>
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">{{ $log->log_code ?? ('LOG-' . sprintf('%03d', $log->id ?? 1)) }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $log->agency_name ?? 'N/A' }}</span>
                                    <small class="text-muted" style="font-size: 0.78rem;"><i class="fas fa-folder text-primary me-1"></i>{{ $log->main_menu ?? 'Patroli Siber' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 mb-1">
                                        <span class="badge {{ $threatBadgeClass }} badge-threat">{{ $log->threat_level ?? 'Medium' }}</span>
                                    </div>
                                    <small class="text-dark fw-semibold d-block text-truncate" style="max-width: 200px;">{{ $log->category ?? 'Web Defacement' }}</small>
                                </td>
                                <td class="text-center">
                                    {!! $statusBadge !!}
                                </td>
                                <td class="text-center">
                                    @if($currentStatus === 'Revision' || $currentStatus === 'Perlu Perbaikan')
                                        <a href="{{ route('petugas.patrol.edit', $log->id ?? 1) }}" class="btn btn-danger btn-sm px-3 fw-bold rounded-2">
                                            <i class="fas fa-edit me-1"></i> Edit Revisi
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary" onclick="showLogDetail({{ json_encode($log) }})">
                                            <i class="fas fa-eye me-1"></i> Lihat Detail
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <!-- Fallback Demo / Dummy Data jika DB masih kosong -->
                            <tr>
                                <td>
                                    <strong class="text-dark d-block">22 Jun 2026</strong>
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">LOG-045</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">BAPPEDA Lampung</span>
                                    <small class="text-muted" style="font-size: 0.78rem;"><i class="fas fa-folder text-primary me-1"></i>Patroli Siber</small>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark badge-threat mb-1">High</span>
                                    <small class="text-dark fw-semibold d-block">Web Defacement</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 badge-status"><i class="fas fa-check-circle"></i> Disetujui Admin</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary" onclick="showDummyDetail('LOG-045', 'BAPPEDA Lampung', 'Web Defacement', 'High', 'Disetujui Admin', 'https://bappeda.lampungprov.go.id', 'Ditemukan halaman terdeface pada direktori berita.')">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong class="text-dark d-block">23 Jun 2026</strong>
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">LOG-046</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">Dinas Kesehatan</span>
                                    <small class="text-muted" style="font-size: 0.78rem;"><i class="fas fa-folder text-primary me-1"></i>Patroli Siber</small>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark badge-threat mb-1">Medium</span>
                                    <small class="text-dark fw-semibold d-block">Judi Online (Judol)</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 badge-status" style="color: #856404 !important;"><i class="fas fa-hourglass-half"></i> Menunggu Validasi</span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-link text-decoration-none fw-bold text-primary" onclick="showDummyDetail('LOG-046', 'Dinas Kesehatan', 'Judi Online (Judol)', 'Medium', 'Menunggu Validasi', 'https://dinkes.lampungprov.go.id/slot', 'Subdomain mengarah ke situs judi online.')">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </button>
                                </td>
                            </tr>
                            <tr class="row-revisi">
                                <td>
                                    <strong class="text-dark d-block">24 Jun 2026</strong>
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">LOG-047</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">Dinas Pendidikan</span>
                                    <small class="text-muted" style="font-size: 0.78rem;"><i class="fas fa-folder text-primary me-1"></i>Patroli Siber</small>
                                </td>
                                <td>
                                    <span class="badge bg-danger text-white badge-threat mb-1">Critical</span>
                                    <small class="text-dark fw-semibold d-block">Malware Injection</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 badge-status"><i class="fas fa-exclamation-triangle"></i> Perlu Perbaikan</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('petugas.patrol.create') }}" class="btn btn-danger btn-sm px-3 fw-bold rounded-2">
                                        <i class="fas fa-edit me-1"></i> Edit Revisi
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Support -->
            @if(isset($logs) && method_exists($logs, 'links'))
                <div class="mt-4 d-flex justify-content-end">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Detail Log Patroli -->
<div class="modal fade" id="modalLogDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white p-3">
                <h6 class="modal-header-title fw-bold mb-0" id="modalTitle"><i class="fas fa-info-circle me-1.5 text-info"></i> Detail Log Patroli Siber</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">ID Log & Kode</label>
                        <span class="fw-bold text-dark font-monospace fs-6" id="modalLogCode">-</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Status Validasi</label>
                        <span id="modalStatusBadge">-</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">OPD / Instansi Target</label>
                        <span class="fw-semibold text-dark" id="modalAgency">-</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Kategori Insiden & Ancaman</label>
                        <span class="fw-semibold text-dark" id="modalCategory">-</span>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">URL Target</label>
                        <a href="#" target="_blank" class="text-primary text-break fw-semibold" id="modalUrl">-</a>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Deskripsi / Kronologi Temuan</label>
                        <div class="p-3 bg-light rounded-2 border text-secondary small" id="modalDescription" style="white-space: pre-line;">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-2.5">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-2 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // 1. Live Filter Search Bar & Filter Status Dropdown
        const searchInput = document.getElementById('tableSearchInput');
        const statusSelect = document.getElementById('statusFilterSelect');
        const tableRows = document.querySelectorAll('#historyTable tbody tr');

        function filterTable() {
            const query = searchInput ? searchInput.value.toLowerCase() : '';
            const selectedStatus = statusSelect ? statusSelect.value : 'ALL';

            tableRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const rowStatus = row.getAttribute('data-status') || '';

                let matchQuery = text.includes(query);
                let matchStatus = (selectedStatus === 'ALL') || 
                                  (selectedStatus === 'Approved' && (rowStatus.includes('Approved') || text.includes('disetujui'))) ||
                                  (selectedStatus === 'Pending' && (rowStatus.includes('Pending') || text.includes('menunggu'))) ||
                                  (selectedStatus === 'Revision' && (rowStatus.includes('Revision') || text.includes('perbaikan')));

                if (matchQuery && matchStatus) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchInput?.addEventListener('input', filterTable);
        statusSelect?.addEventListener('change', filterTable);
    });

    // 2. Modal Helper untuk Objek DB Nyata
    function showLogDetail(log) {
        document.getElementById('modalLogCode').innerText = log.log_code || ('LOG-' + log.id);
        document.getElementById('modalAgency').innerText = log.agency_name || 'N/A';
        document.getElementById('modalCategory').innerText = (log.category || 'N/A') + ' (' + (log.threat_level || 'Medium') + ')';
        document.getElementById('modalUrl').innerText = log.target_url || '-';
        document.getElementById('modalUrl').href = log.target_url || '#';
        document.getElementById('modalDescription').innerText = log.description || 'Tidak ada deskripsi.';
        
        let statusHtml = log.status || 'Menunggu Validasi';
        if (log.status === 'Approved' || log.status === 'Disetujui Admin') {
            statusHtml = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 badge-status"><i class="fas fa-check-circle"></i> Disetujui Admin</span>';
        } else if (log.status === 'Revision' || log.status === 'Perlu Perbaikan') {
            statusHtml = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 badge-status"><i class="fas fa-exclamation-triangle"></i> Perlu Perbaikan</span>';
        } else {
            statusHtml = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 badge-status" style="color: #856404 !important;"><i class="fas fa-hourglass-half"></i> Menunggu Validasi</span>';
        }
        document.getElementById('modalStatusBadge').innerHTML = statusHtml;

        const modal = new bootstrap.Modal(document.getElementById('modalLogDetail'));
        modal.show();
    }

    // 3. Modal Helper untuk Fallback Row Dummy
    function showDummyDetail(code, agency, category, threat, status, url, desc) {
        document.getElementById('modalLogCode').innerText = code;
        document.getElementById('modalAgency').innerText = agency;
        document.getElementById('modalCategory').innerText = category + ' (' + threat + ')';
        document.getElementById('modalUrl').innerText = url;
        document.getElementById('modalUrl').href = url;
        document.getElementById('modalDescription').innerText = desc;
        
        let statusHtml = status;
        if (status === 'Disetujui Admin') {
            statusHtml = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 badge-status"><i class="fas fa-check-circle"></i> Disetujui Admin</span>';
        } else if (status === 'Menunggu Validasi') {
            statusHtml = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 badge-status" style="color: #856404 !important;"><i class="fas fa-hourglass-half"></i> Menunggu Validasi</span>';
        }
        document.getElementById('modalStatusBadge').innerHTML = statusHtml;

        const modal = new bootstrap.Modal(document.getElementById('modalLogDetail'));
        modal.show();
    }
</script>
@endpush