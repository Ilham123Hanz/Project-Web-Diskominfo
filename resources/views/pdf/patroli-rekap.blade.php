<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Laporan Patroli - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 9pt;
            line-height: 1.5;
            color: #1a1a2e;
        }
        .header-section {
            border-bottom: 3px solid #0B1D33;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .logo-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #0052A3, #00D2FF);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        .header-text h1 {
            font-size: 18px;
            font-weight: 800;
            color: #06111E;
            margin: 0;
            letter-spacing: -0.3px;
        }
        .header-text p {
            margin: 2px 0 0;
            color: #64748B;
            font-size: 10px;
            font-weight: 500;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px 15px;
            margin-bottom: 15px;
        }
        .meta-item {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 6px;
            padding: 8px 12px;
        }
        .meta-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748B;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .meta-value {
            font-size: 10px;
            font-weight: 600;
            color: #0F172A;
            word-break: break-word;
        }
        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #0B1D33;
            border-bottom: 2px solid #0052A3;
            padding-bottom: 4px;
            margin: 20px 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table-responsive {
            margin-top: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        th {
            background: #F1F5F9;
            color: #334155;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 7pt;
            letter-spacing: 0.06em;
            border-bottom: 2px solid #CBD5E1;
            padding: 8px 6px;
            white-space: nowrap;
        }
        td {
            padding: 6px 6px;
            vertical-align: middle;
            font-size: 8pt;
            border-bottom: 1px solid #F1F5F9;
        }
        tr:hover td {
            background-color: #F8FAFC;
        }
        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 6.5pt;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .badge-verified { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }
        .badge-pending { background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; }
        .badge-revision { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }
        .threat-critical { background: #FECACA; color: #991B1B; }
        .threat-high { background: #FED7AA; color: #9A3412; }
        .threat-medium { background: #DBEAFE; color: #1E40AF; }
        .threat-low { background: #F1F5F9; color: #475569; }
        .evidence-img {
            max-width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 4px;
            border: 1px solid #E2E8F0;
        }
        .footer-note {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #E2E8F0;
            font-size: 8pt;
            color: #94A3B8;
            text-align: center;
        }
        .badge-status { padding: 3px 8px; border-radius: 20px; font-size: 6.5pt; font-weight: 700; letter-spacing: 0.3px; display: inline-flex; align-items: center; gap: 4px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="logo-title">
            <div class="logo-icon"><i class="fas fa-shield-halved"></i></div>
            <div class="header-text">
                <h1>SIP-O-SIBER</h1>
                <p>Sistem Informasi Pencatatan & Monitoring Keamanan Siber | Dinas Kominfo Provinsi Lampung</p>
            </div>
        </div>
        <div style="margin-top: 10px; display: flex; justify-content: space-between; align-items: center;">
            <span style="background: #dbeafe; color: #1e40af; padding: 4px 10px; border-radius: 20px; font-size: 9pt; font-weight: 700; letter-spacing: 0.3px;">REKAP LAPORAN PATROLI SI BER</span>
            <span style="font-size: 9pt; color: #64748B;">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</span>
        </div>
    </div>

    <!-- Meta Grid -->
    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-filter me-1"></i> Filter Tanggal</div>
            <div class="meta-value">
                @if($request->filled('start_date') && $request->filled('end_date'))
                    {{ \Carbon\Carbon::parse($request->start_date)->translatedFormat('d M Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->translatedFormat('d M Y') }}
                @else
                    Semua Waktu
                @endif
            </div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-list me-1"></i> Total Data</div>
            <div class="meta-value">{{ $laporans->count() }} Laporan</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-check-circle me-1"></i> Disetujui</div>
            <div class="meta-value">{{ $laporans->whereIn('status', ['Verified', 'Approved', 'Disetujui Admin'])->count() }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-clock me-1"></i> Menunggu</div>
            <div class="meta-value">{{ $laporans->whereIn('status', ['Pending', 'Menunggu Validasi'])->count() }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-exclamation-triangle me-1"></i> Revisi</div>
            <div class="meta-value">{{ $laporans->whereIn('status', ['Perlu Perbaikan', 'Revision', 'Rejection'])->count() }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-user-shield me-1"></i> Dicetak Oleh</div>
            <div class="meta-value">{{ auth()->user()->name }}</div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="section-title"><i class="fas fa-table me-1"></i> Daftar Laporan Patroli</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tgl / Kode Log</th>
                    <th style="width: 18%;">OPD Sasaran</th>
                    <th style="width: 15%;">Kategori & Threat</th>
                    <th style="width: 12%;">Pelapor</th>
                    <th style="width: 12%;" class="text-center">Status</th>
                    <th style="width: 14%;">Tgl Laporan</th>
                    <th style="width: 12%;" class="text-center">Bukti</th>
                </tr>
            </thead>
            <tbody>
                @forelse($laporans as $index => $laporan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <strong class="text-dark d-block">{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d M Y') }}</strong>
                        <small class="text-muted font-monospace" style="font-size: 7pt;">{{ $laporan->log_code }}</small>
                    </td>
                    <td>
                        <span class="fw-bold text-dark d-block mb-0.5" style="font-size: 8pt;">{{ $laporan->opd_sasaran }}</span>
                        <small class="text-muted" style="font-size: 7pt;">{{ $laporan->kategori_insiden }}</small>
                        <br>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 rounded-pill fw-normal ms-1" style="font-size: 6.5pt;">{{ $laporan->main_menu }} ({{ $laporan->rumpun_kategori }})</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-1 mb-1">
                            @php
                                $threat = strtolower($laporan->threat_level ?? 'medium');
                                $threatClass = match($threat) {
                                    'critical' => 'threat-critical',
                                    'high'     => 'threat-high',
                                    'medium'   => 'threat-medium',
                                    'low'      => 'threat-low',
                                    default    => 'threat-medium',
                                };
                            @endphp
                            <span class="badge-status {{ $threatClass }}">{{ $laporan->threat_level }}</span>
                        </div>
                        <small class="text-dark fw-semibold d-block text-truncate" style="max-width: 150px; font-size: 7.5pt;">{{ $laporan->target_url }}</small>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 24px; height: 24px; font-size: 9px;">
                                {{ strtoupper(substr($laporan->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <span class="fw-semibold text-dark" style="font-size: 8.5pt;">{{ $laporan->user->name ?? 'Petugas Lapangan' }}</span>
                        </div>
                    </td>
                    <td class="text-center">
                        @if(strtolower($laporan->status) === 'pending')
                            <span class="badge-status bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-2 py-1 fw-semibold" style="font-size: 8.5pt;"><i class="fas fa-spinner fa-spin me-1 text-warning"></i> Pending</span>
                        @elseif(in_array($laporan->status, ['Verified', 'Approved', 'Disetujui Admin']))
                            <span class="badge-status bg-success bg-opacity-10 text-success px-2 py-1 fw-semibold" style="font-size: 8.5pt;"><i class="fas fa-check-circle me-1"></i> Verified</span>
                        @elseif(in_array($laporan->status, ['Perlu Perbaikan', 'Revision', 'Revisi']))
                            <span class="badge-status bg-danger bg-opacity-10 text-danger px-2 py-1 fw-semibold" style="font-size: 8.5pt;"><i class="fas fa-exclamation-triangle me-1"></i> Perlu Perbaikan</span>
                        @else
                            <span class="badge-status bg-secondary bg-opacity-10 text-secondary px-2 py-1 fw-semibold" style="font-size: 8.5pt;">{{ $laporan->status }}</span>
                        @endif
                    </td>
                    <td class="text-center" style="font-size: 8.5pt;">
                        {{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d M Y H:i') }} WIB
                    </td>
                    <td class="text-center">
                        @if($laporan->file_evidence)
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1 fw-semibold" style="font-size: 7.5pt;"><i class="fas fa-paperclip me-1"></i> Ada</span>
                        @else
                            <span class="text-muted fst-italic" style="font-size: 7.5pt;">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary opacity-50"></i>
                        Belum ada data laporan patroli.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer-note">
        <strong>SIP-O-SIBER</strong> - Sistem Informasi Pencatatan & Monitoring Keamanan Siber<br>
        Dinas Komunikasi, Informatika dan Statistik Provinsi Lampung<br>
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB<br>
        <em>Dokumen ini adalah cetakan otomatis dari sistem, tidak memerlukan tanda tangan basah.</        </div>
    </div>
</body>
</html>