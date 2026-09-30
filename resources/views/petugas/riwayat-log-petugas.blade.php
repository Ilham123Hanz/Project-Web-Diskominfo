@extends('petugas.petugas-layout.petugas-layout')

@section('title', 'Riwayat Log Patroli Personal - SIP-O-SIBER')

@push('styles')
<style>
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
        background-color: #f8fafc;
    }

    .main-container-history {
        max-width: 1140px;
        margin: 24px auto;
    }

    .card-custom {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.01);
        padding: 28px;
    }

    /* Optimization for Tables */
    .table-container {
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-size: 0.72rem;
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
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
    }

    /* Highlight Row Status */
    .row-revisi {
        background-color: #fef2f2 !important;
    }
    .row-revisi:hover {
        background-color: #fee2e2 !important;
    }
    .row-rejected {
        background-color: #fbf2f2 !important;
    }

    /* Custom Badges */
    .badge-status {
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        letter-spacing: 0.01em;
    }

    .badge-threat {
        font-size: 0.65rem;
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
        padding: 16px;
    }

    .input-gov-sm {
        font-size: 0.85rem;
        border-color: #cbd5e1;
    }

    .input-gov-sm:focus {
        border-color: #0f3057;
        box-shadow: 0 0 0 0.2rem rgba(15, 48, 87, 0.15);
    }

    /* Buttons */
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

    /* Attachment Preview Styling */
    .evidence-preview-container {
        background: #f1f5f9;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
    }

    .evidence-img-preview {
        max-height: 250px;
        object-fit: contain;
        border-radius: 6px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="main-container-history">
        
        <!-- Header Navigasi -->
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

        <!-- Card Main -->
        <div class="card card-custom">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
                <div>
                    <h4 class="fw-bold mb-1" style="color: #0f3057;">Riwayat Log Patroli Personal</h4>
                    <p class="text-muted small mb-0">Pantau status laporan temuan insiden siber yang telah Anda kirimkan beserta bukti dukungnya.</p>
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
                            <input type="text" id="tableSearchInput" class="form-control input-gov-sm border-start-0 rounded-end-2" placeholder="Cari OPD Sasaran, Kode Log, Kategori, atau Main Menu...">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <select id="statusFilterSelect" class="form-select input-gov-sm rounded-2">
                            <option value="ALL">-- Semua Status Validasi --</option>
                            <option value="Approved">Disetujui Admin / Verified</option>
                            <option value="Pending">Menunggu Validasi</option>
                            <option value="Revision">Perlu Perbaikan (Revisi)</option>
                            <option value="Rejection">Ditolak (Rejection)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Log Patroli -->
            <div class="table-container">
                <table class="table table-hover align-middle" id="historyTable">
                    <thead>
                        <tr>
                            <th style="width: 16%;">Tgl / Kode Log</th>
                            <th style="width: 24%;">OPD Sasaran</th>
                            <th style="width: 22%;">Kategori & Threat Level</th>
                            <th style="width: 18%;" class="text-center">Status Validasi</th>
                            <th style="width: 20%;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporans as $log)
                            @php
                                $currentStatus = $log->status ?? 'Pending';
                                $statusBadge = '';
                                $rowClass = '';

                                switch($currentStatus) {
                                    case 'Approved':
                                    case 'Verified':
                                    case 'Disetujui Admin':
                                        $statusBadge = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 badge-status"><i class="fas fa-check-circle"></i> Disetujui Admin</span>';
                                        break;
                                    case 'Perlu Perbaikan':
                                    case 'Revision':
                                        $rowClass = 'row-revisi';
                                        $statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 badge-status" style="color: #856404 !important;"><i class="fas fa-exclamation-triangle"></i> Perlu Perbaikan</span>';
                                        break;
                                    case 'Rejection':
                                        $rowClass = 'row-rejected';
                                        $statusBadge = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 badge-status"><i class="fas fa-times-circle"></i> Ditolak</span>';
                                        break;
                                    default:
                                        $statusBadge = '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 badge-status"><i class="fas fa-hourglass-half"></i> Menunggu Validasi</span>';
                                        break;
                                }

                                $threatBadgeClass = match($log->threat_level ?? 'Medium') {
                                    'Critical' => 'bg-danger text-white',
                                    'High'     => 'bg-warning text-dark',
                                    'Medium'   => 'bg-info text-dark',
                                    'Low'      => 'bg-secondary text-white',
                                    default    => 'bg-secondary text-white',
                                };

                                // Asset Bukti File dari field `file_evidence`
                                $evidenceUrl = $log->file_evidence 
                                    ? asset('storage/bukti_files/' . $log->created_at->year . '/' . preg_replace('/[^A-Za-z0-9_\\-]/', '_', $log->kategori_insiden) . '/' . $log->file_evidence) 
                                    : null;
                                
                                // Penanganan Verifikator jika dipasang eager loading relation
                                $verifierName = $log->verifier->name ?? null;
                            @endphp
                            <tr class="{{ $rowClass }}" data-status="{{ $currentStatus }}">
                                <td>
                                    <strong class="text-dark d-block">{{ \Carbon\Carbon::parse($log->created_at ?? now())->translatedFormat('d M Y') }}</strong>
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">{{ $log->log_code }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark d-block">{{ $log->opd_sasaran }}</span>
                                    <small class="text-muted" style="font-size: 0.78rem;">
                                        <i class="fas fa-folder text-primary me-1"></i>{{ $log->main_menu }} 
                                        <span class="text-xs text-muted">({{ $log->rumpun_kategori }})</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1 mb-1">
                                        <span class="badge {{ $threatBadgeClass }} badge-threat">{{ $log->threat_level }}</span>
                                    </div>
                                    <small class="text-dark fw-semibold d-block text-truncate" style="max-width: 200px;">{{ $log->kategori_insiden }}</small>
                                </td>
                                <td class="text-center">
                                    {!! $statusBadge !!}
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-2 fw-semibold px-2 py-1 fs-7 btn-detail-trigger" 
                                                data-log='@json($log)'
                                                data-evidence-url="{{ $evidenceUrl }}"
                                                data-verifier-name="{{ $verifierName }}">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>

                                        @if(in_array($currentStatus, ['Revision', 'Perlu Perbaikan']))
                                            <a href="{{ route('petugas.patrol.edit', $log->id) }}" class="btn btn-warning btn-sm rounded-2 fw-bold px-2 py-1 fs-7 text-dark">
                                                <i class="fas fa-edit me-1"></i> Revisi
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    Belum ada riwayat laporan log patroli siber.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Laravel -->
            @if(isset($laporans) && method_exists($laporans, 'links'))
                <div class="mt-4 d-flex justify-content-end">
                    {{ $laporans->links() }}
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
                <h6 class="modal-header-title fw-bold mb-0" id="modalTitle">
                    <i class="fas fa-shield-alt me-1.5 text-info"></i> Detail Laporan Log Patroli Siber
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Kode Log</label>
                        <span class="fw-bold text-dark font-monospace fs-6" id="modalLogCode">-</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Status Validasi</label>
                        <span id="modalStatusBadge">-</span>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">OPD / Instansi Sasaran</label>
                        <span class="fw-semibold text-dark" id="modalOpdSasaran">-</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Klasifikasi Insiden</label>
                        <span class="fw-semibold text-dark" id="modalKategoriInsiden">-</span>
                    </div>

                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Rumpun & Main Menu</label>
                        <span class="fw-semibold text-dark" id="modalMenuInfo">-</span>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Ancaman (Threat Level)</label>
                        <span id="modalThreatLevel">-</span>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">URL Target / Subdomain Terdampak</label>
                        <a href="#" target="_blank" class="text-primary text-break fw-semibold" id="modalTargetUrl">-</a>
                    </div>

                    <div class="col-12">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Deskripsi & Kronologi Temuan</label>
                        <div class="p-3 bg-light rounded-2 border text-secondary small" id="modalDescription" style="white-space: pre-line; min-height: 70px;">-</div>
                    </div>

                    <div class="col-12" id="containerCoordination">
                        <label class="text-muted small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Catatan Koordinasi</label>
                        <div class="p-3 bg-light rounded-2 border text-secondary small" id="modalCoordinationNote">-</div>
                    </div>

                    <!-- Catatan Koreksi/Revisi dari Admin jika Ada -->
                    <div class="col-12 d-none" id="containerAdminCorrection">
                        <label class="text-danger small fw-bold d-block text-uppercase" style="font-size: 0.7rem;">Catatan Perbaikan / Koreksi Admin</label>
                        <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-2 text-danger small fw-medium" id="modalAdminCorrection">
                            -
                        </div>
                    </div>

                    <!-- Jejak Verifikasi Admin -->
                    <div class="col-12 d-none" id="containerVerifiedInfo">
                        <div class="p-2 bg-secondary bg-opacity-10 rounded-2 text-muted extra-small d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-user-check me-1"></i> Diverifikasi oleh: <strong id="modalVerifierName">-</strong></span>
                            <span><i class="fas fa-clock me-1"></i> Waktu: <strong id="modalVerifiedAt">-</strong></span>
                        </div>
                    </div>

                    <!-- File Bukti Dukung (file_evidence) -->
                    <div class="col-12">
                        <label class="text-muted small fw-bold d-block text-uppercase mb-1" style="font-size: 0.7rem;">File Bukti Dukung (Evidence)</label>
                        <div class="evidence-preview-container" id="modalEvidenceContainer">
                            <!-- Javascript akan menyuntikkan pratinjau bukti -->
                        </div>
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
        // Live Filter Search Bar & Dropdown Filter Status
        const searchInput = document.getElementById('tableSearchInput');
        const statusSelect = document.getElementById('statusFilterSelect');
        const tableRows = document.querySelectorAll('#historyTable tbody tr');

        function filterTable() {
            const query = searchInput ? searchInput.value.toLowerCase() : '';
            const selectedStatus = statusSelect ? statusSelect.value : 'ALL';

            tableRows.forEach(row => {
                // Abaikan baris kosong (empty state)
                if (!row.getAttribute('data-status')) return;

                const text = row.innerText.toLowerCase();
                const rowStatus = row.getAttribute('data-status') || '';

                let matchQuery = text.includes(query);
                let matchStatus = false;

                if (selectedStatus === 'ALL') {
                    matchStatus = true;
                } else if (selectedStatus === 'Approved') {
                    matchStatus = ['Approved', 'Verified', 'Disetujui Admin'].includes(rowStatus);
                } else if (selectedStatus === 'Pending') {
                    matchStatus = ['Pending', 'Menunggu Validasi'].includes(rowStatus);
                } else if (selectedStatus === 'Revision') {
                    matchStatus = ['Revision', 'Perlu Perbaikan'].includes(rowStatus);
                } else if (selectedStatus === 'Rejection') {
                    matchStatus = (rowStatus === 'Rejection');
                }

                row.style.display = (matchQuery && matchStatus) ? '' : 'none';
            });
        }

        searchInput?.addEventListener('input', filterTable);
        statusSelect?.addEventListener('change', filterTable);

        // Event Listener untuk Tombol Detail (Pencegahan masalah kuotasi & JSON parser error)
        document.querySelectorAll('.btn-detail-trigger').forEach(button => {
            button.addEventListener('click', function() {
                const logData = JSON.parse(this.getAttribute('data-log'));
                const evidenceUrl = this.getAttribute('data-evidence-url');
                const verifierName = this.getAttribute('data-verifier-name');
                
                showLogDetail(logData, evidenceUrl, verifierName);
            });
        });
    });

    // Helper Modal Log Detail untuk Objek dari Database Migration Baru
    function showLogDetail(log, evidenceUrl, verifierName) {
        document.getElementById('modalLogCode').innerText = log.log_code || ('ID-' + log.id);
        document.getElementById('modalOpdSasaran').innerText = log.opd_sasaran || '-';
        document.getElementById('modalKategoriInsiden').innerText = log.kategori_insiden || '-';
        document.getElementById('modalMenuInfo').innerText = (log.rumpun_kategori || '-') + ' / ' + (log.main_menu || '-');
        
        // Threat Level Display
        document.getElementById('modalThreatLevel').innerHTML = `<span class="badge bg-dark text-white">${log.threat_level || 'Medium'}</span>`;
        
        // Target URL
        const targetUrlElem = document.getElementById('modalTargetUrl');
        if (log.target_url) {
            targetUrlElem.innerText = log.target_url;
            targetUrlElem.href = log.target_url.startsWith('http') ? log.target_url : 'http://' + log.target_url;
        } else {
            targetUrlElem.innerText = '-';
            targetUrlElem.removeAttribute('href');
        }

        document.getElementById('modalDescription').innerText = log.description || 'Tidak ada deskripsi.';
        
        // Catatan Koordinasi
        const coordContainer = document.getElementById('containerCoordination');
        if (log.coordination_note) {
            document.getElementById('modalCoordinationNote').innerText = log.coordination_note;
            coordContainer.classList.remove('d-none');
        } else {
            coordContainer.classList.add('d-none');
        }

        // Status Badge Mapping
        let statusHtml = '';
        switch(log.status) {
            case 'Approved':
            case 'Verified':
            case 'Disetujui Admin':
                statusHtml = '<span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-20 badge-status"><i class="fas fa-check-circle"></i> Disetujui Admin</span>';
                break;
            case 'Perlu Perbaikan':
            case 'Revision':
                statusHtml = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 badge-status" style="color: #856404 !important;"><i class="fas fa-exclamation-triangle"></i> Perlu Perbaikan</span>';
                break;
            case 'Rejection':
                statusHtml = '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-20 badge-status"><i class="fas fa-times-circle"></i> Ditolak</span>';
                break;
            default:
                statusHtml = '<span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 badge-status"><i class="fas fa-hourglass-half"></i> Menunggu Validasi</span>';
                break;
        }
        document.getElementById('modalStatusBadge').innerHTML = statusHtml;

        // Display Catatan Koreksi Admin (admin_correction)
        const correctionContainer = document.getElementById('containerAdminCorrection');
        if (log.admin_correction) {
            document.getElementById('modalAdminCorrection').innerText = log.admin_correction;
            correctionContainer.classList.remove('d-none');
        } else {
            correctionContainer.classList.add('d-none');
        }

        // Informasi Verifikator (verified_by & verified_at)
        const verifiedContainer = document.getElementById('containerVerifiedInfo');
        if (log.verified_at) {
            document.getElementById('modalVerifierName').innerText = verifierName && verifierName !== 'null' ? verifierName : ('Admin ID #' + log.verified_by);
            document.getElementById('modalVerifiedAt').innerText = log.verified_at;
            verifiedContainer.classList.remove('d-none');
        } else {
            verifiedContainer.classList.add('d-none');
        }

        // Render Bukti File/Screenshot (file_evidence)
        renderEvidencePreview(evidenceUrl);

        const modal = new bootstrap.Modal(document.getElementById('modalLogDetail'));
        modal.show();
    }

    // Helper Renderer File Evidence
    function renderEvidencePreview(url) {
        const container = document.getElementById('modalEvidenceContainer');
        
        if (!url || url === 'null' || url === '' || url.endsWith('/storage/')) {
            container.innerHTML = `
                <div class="py-3 text-muted">
                    <i class="fas fa-paperclip fa-2x mb-2 d-block text-secondary"></i>
                    <span class="small fw-semibold">Tidak ada file bukti dukung (file_evidence) yang dilampirkan.</span>
                </div>
            `;
            return;
        }

        const ext = url.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);

        if (isImage) {
            container.innerHTML = `
                <div class="d-flex flex-column align-items-center">
                    <a href="${url}" target="_blank" title="Klik untuk memperbesar gambar">
                        <img src="${url}" alt="File Bukti Dukung" class="evidence-img-preview mb-2">
                    </a>
                    <a href="${url}" download target="_blank" class="btn btn-sm btn-outline-dark mt-1 fw-bold rounded-2">
                        <i class="fas fa-download me-1"></i> Unduh File Bukti
                    </a>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="py-2">
                    <i class="fas fa-file-alt fa-2x text-primary mb-2 d-block"></i>
                    <p class="small text-muted mb-2 fw-semibold">Dokumen Lampiran Bukti (${ext.toUpperCase()})</p>
                    <a href="${url}" target="_blank" class="btn btn-sm btn-primary fw-bold rounded-2 px-3">
                        <i class="fas fa-external-link-alt me-1"></i> Buka / Unduh Berkas Lampiran
                    </a>
                </div>
            `;
        }
    }
</script>
@endpush