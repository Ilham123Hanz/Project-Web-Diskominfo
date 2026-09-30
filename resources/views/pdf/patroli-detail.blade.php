<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Insiden Siber - {{ $laporan->log_code }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            margin: 20mm;
            size: A4;
        }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1a1a2e;
        }
        .header-section {
            border-bottom: 3px solid #0B1D33;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .logo-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .logo-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #0052A3, #00D2FF);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }
        .header-text h1 {
            font-size: 22px;
            font-weight: 800;
            color: #06111E;
            margin: 0;
            letter-spacing: -0.3px;
        }
        .header-text p {
            margin: 4px 0 0;
            color: #64748B;
            font-size: 12px;
            font-weight: 500;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px 20px;
            margin-bottom: 20px;
        }
        .meta-item {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 12px 16px;
        }
        .meta-label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #64748B;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .meta-value {
            font-size: 13px;
            font-weight: 600;
            color: #0F172A;
            word-break: break-word;
        }
        .section-title {
            font-size: 14px;
            font-weight: 800;
            color: #0B1D33;
            border-bottom: 2px solid #0052A3;
            padding-bottom: 6px;
            margin: 25px 0 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .description-box {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 18px;
            white-space: pre-wrap;
            font-size: 12px;
            line-height: 1.7;
            color: #334155;
        }
        .evidence-section {
            margin-top: 20px;
        }
        .evidence-img {
            max-width: 100%;
            max-height: 300px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
        }
        .footer-note {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #E2E8F0;
            font-size: 10px;
            color: #94A3B8;
            text-align: center;
        }
        .badge-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        .coordination-box {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            border-radius: 8px;
            padding: 16px;
            color: #14532D;
        }
        .correction-box {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            border-radius: 8px;
            padding: 16px;
            color: #7F1D1D;
        }
        .attachment-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: #F1F5F9;
            border-radius: 8px;
            color: #0052A3;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header-section">
        <div class="logo-title">
            <div class="logo-icon"><i class="fas fa-shield-halved"></i></div>
            <div class="header-text">
                <h1>SIP-O-SIBER</h1>
                <p>Sistem Informasi Pencatatan & Monitoring Keamanan Siber</p>
            </div>
        </div>
        <div style="margin-top: 15px; display: flex; justify-content: space-between; align-items: center;">
            <span class="badge-status" style="background: #dbeafe; color: #1e40af;">LAPORAN PATROLI SI BER</span>
            <span class="badge-status" style="background: {{ match($laporan->threat_level) { 'Critical' => '#fecaca', 'High' => '#fed7aa', 'Medium' => '#dbeafe', default => '#f1f5f9' } }}; color: {{ match($laporan->threat_level) { 'Critical' => '#991b1b', 'High' => '#9a3412', 'Medium' => '#1e40af', default => '#475569' } }};">{{ $laporan->threat_level }}</span>
        </div>
    </div>

    <!-- Kode Log & Status -->
    <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
        <div>
            <div style="font-size: 10px; text-transform: uppercase; color: #64748B; font-weight: 700; letter-spacing: 0.8px;">Kode Log</div>
            <div style="font-family: 'JetBrains Mono', monospace; font-size: 18px; font-weight: 700; color: #0052A3;">{{ $laporan->log_code }}</div>
        </div>
        <div style="text-align: right;">
            @php
                $statusStyle = match($laporan->status) {
                    'Approved', 'Verified', 'Disetujui Admin' => 'background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0;',
                    'Pending', 'Menunggu Validasi' => 'background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;',
                    'Perlu Perbaikan', 'Revision', 'Rejection' => 'background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5;',
                    default => 'background: #F1F5F9; color: #475569;'
                };
            @endphp
            <span style="padding: 6px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; {{ $statusStyle }}">{{ $laporan->status }}</span>
        </div>
    </div>

    <!-- Meta Grid -->
    <div class="meta-grid">
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-building me-1"></i> OPD / Instansi Sasaran</div>
            <div class="meta-value">{{ $laporan->opd_sasaran }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-tag me-1"></i> Kategori Insiden</div>
            <div class="meta-value">{{ $laporan->kategori_insiden }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-folder me-1"></i> Klasifikasi (Rumpun / Menu)</div>
            <div class="meta-value">{{ $laporan->rumpun_kategori }} / {{ $laporan->main_menu }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-link me-1"></i> URL Target / Subdomain</div>
            <div class="meta-value">
                @if($laporan->target_url)
                    {{ $laporan->target_url }}
                @else
                    <span style="color: #94A3B8;">Tidak ada</span>
                @endif
            </div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-user me-1"></i> Pelapor</div>
            <div class="meta-value">{{ $laporan->user->name ?? 'Petugas Lapangan' }}</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-calendar-alt me-1"></i> Tanggal Laporan</div>
            <div class="meta-value">{{ \Carbon\Carbon::parse($laporan->created_at)->translatedFormat('d F Y H:i') }} WIB</div>
        </div>
        <div class="meta-item">
            <div class="meta-label"><i class="fas fa-clock me-1"></i> Waktu Verifikasi</div>
            <div class="meta-value">
                @if($laporan->verified_at)
                    {{ \Carbon\Carbon::parse($laporan->verified_at)->translatedFormat('d F Y H:i') }} WIB
                    @if($laporan->verifier)
                        (Oleh: {{ $laporan->verifier->name }})
                    @endif
                @else
                    <span style="color: #94A3B8;">Belum diverifikasi</span>
                @endif
            </div>
        </div>
    </div>

    <!-- Kronologi & Deskripsi -->
    <div class="section-title"><i class="fas fa-file-alt me-1"></i> Kronologi & Deskripsi Temuan</div>
    <div class="description-box">{{ $laporan->description ?? 'Tidak ada deskripsi.' }}</div>

    <!-- Catatan Koordinasi -->
    @if($laporan->coordination_note)
    <div class="section-title"><i class="fas fa-comments me-1"></i> Catatan Koordinasi / Disposisi</div>
    <div class="coordination-box">{{ $laporan->coordination_note }}</div>
    @endif

    <!-- Catatan Koreksi Admin -->
    @if($laporan->admin_correction)
    <div class="section-title" style="color: #DC2626;"><i class="fas fa-exclamation-triangle me-1"></i> Catatan Koreksi / Revisi dari Admin</div>
    <div class="correction-box">{{ $laporan->admin_correction }}</div>
    @endif

    <!-- Bukti Dukung (Evidence) -->
    @if($laporan->file_evidence)
    <div class="section-title"><i class="fas fa-paperclip me-1"></i> Lampiran Bukti Dukung</div>
    <div class="evidence-section">
        @php
            $ext = strtolower(pathinfo($laporan->file_evidence, PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg','jpeg','png','webp','gif']);
            $year = \Carbon\Carbon::parse($laporan->created_at)->year;
            $catName = $laporan->kategori_insiden ?? 'General';
            $cleanCategory = preg_replace('/[^A-Za-z0-9_\\\\-]/', '_', $catName);
            $evidenceUrl = asset('storage/bukti_files/' . $year . '/' . $cleanCategory . '/' . $laporan->file_evidence);
        @endphp
        @if($isImage)
            <img src="{{ $evidenceUrl }}" alt="Bukti Dukung" class="evidence-img">
        @else
            <a href="{{ $evidenceUrl }}" target="_blank" class="attachment-link">
                <i class="fas fa-file-alt"></i> {{ $laporan->file_evidence }} ({{ strtoupper($ext) }})
                <i class="fas fa-external-link-alt"></i>
            </a>
        @endif
    </div>
    @endif

    <!-- Footer -->
    <div class="footer-note">
        <strong>SIP-O-SIBER</strong> - Sistem Informasi Pencatatan & Monitoring Keamanan Siber<br>
        Dinas Komunikasi, Informatika dan Statistik Provinsi Lampung<br>
        Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB<br>
        <em>Dokumen ini adalah cetakan otomatis dari sistem, tidak memerlukan tanda tangan basah.</em>
    </div>
</body>
</html>