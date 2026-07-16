<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operator Petugas Kompleks - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { 
            background-color: #f4f6f9; 
            color: #333333; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            font-size: 15px; 
        }
        .navbar-gov-petugas { 
            background-color: #0f3057; 
            border-bottom: 4px solid #ffc000; 
        }
        .card-gov { 
            background: #ffffff; 
            border: 1px solid #ccd6e0; 
            border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
            transition: all 0.3s;
        }
        .card-gov:hover {
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
        }
        .card-gov-header { 
            background-color: #f8fafc; 
            color: #0f3057; 
            font-weight: 700; 
            font-size: 1.1rem; 
            border-bottom: 1px solid #ccd6e0; 
            padding: 15px;
        }
        .input-gov { 
            background-color: #ffffff !important; 
            color: #333333 !important; 
            border: 1px solid #ccd6e0 !important; 
            font-size: 0.95rem; 
            padding: 10px 12px;
        }
        .input-gov:focus { 
            border-color: #0f3057 !important; 
            box-shadow: 0 0 0 3px rgba(15, 48, 87, 0.15) !important; 
        }
        .btn-gov-petugas { 
            background-color: #0f3057; 
            border: none; 
            color: #ffffff !important; 
            font-weight: 600; 
            font-size: 0.95rem;
            padding: 12px;
            transition: background-color 0.2s;
        }
        .btn-gov-petugas:hover { 
            background-color: #002060; 
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
        .badge-status {
            font-weight: bold;
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 4px;
        }
        
        /* Wizard Custom Style Steps */
        .wizard-step { display: none; }
        .wizard-step.active { display: block; animation: fadeIn 0.4s ease-in-out; }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e2e8f0;
            z-index: 1;
            transform: translateY(-50%);
        }
        .step-dot {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #64748b;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        .step-dot.active {
            background-color: #0f3057;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(15, 48, 87, 0.2);
        }
        .step-dot.completed {
            background-color: #198754;
            color: #fff;
        }

        /* File Preview Area */
        .preview-container {
            border: 2px dashed #ccd6e0;
            border-radius: 6px;
            padding: 15px;
            background-color: #fafbfc;
            display: none;
            margin-top: 10px;
        }
        .preview-image {
            max-height: 120px;
            object-fit: contain;
            border-radius: 4px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-pulse {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-gov-petugas p-3 shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-white" style="font-size: 1.3rem;">
                <i class="fas fa-shield-alt text-warning me-2"></i> SIP-O-SIBER - ADVANCED OPERATOR PANEL
            </span>
            <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm fw-bold px-3">
                <i class="fas fa-power-off me-1 text-danger"></i> KELUAR
            </a>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        <div class="p-3 bg-white border rounded mb-4 shadow-sm d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center">
                <h5 class="fw-bold mb-0 text-dark" style="color: #0f3057;">
                    <i class="fas fa-user-circle me-2 text-muted"></i>Operator Aktif: {{ Auth::user()->name }}
                </h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary px-3 py-2 fw-bold text-uppercase"><i class="fas fa-id-badge me-1"></i> Petugas Lapangan</span>
                <span class="badge bg-dark px-3 py-2 fw-bold font-monospace"><i class="fas fa-network-wired me-1"></i> IP: {{ request()->ip() }}</span>
            </div>
        </div>
        
        @if(session('success')) 
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold p-3 mb-4 rounded shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div> 
        @endif
        @if($errors->any()) 
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-bold p-3 mb-4 rounded shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div> 
        @endif

        <div class="row">
            <div class="col-xl-4 col-lg-5 mb-4">
                
                <div class="card card-gov mb-4 border-0 shadow-sm">
                    <div class="card-gov-header"><i class="fas fa-clock me-2 text-muted"></i> Status Presensi Harian</div>
                    <div class="card-body p-4">
                        @if($todayAttendance)
                            <div class="alert border-0 bg-light text-dark mb-3 p-3 shadow-sm rounded">
    <div class="mb-2 d-flex align-items-center">
        <i class="fas fa-circle text-success me-2 animate-pulse"></i> 
        Status Presensi: <span class="text-success fw-bold ms-1">AKTIF / REGULER</span>
    </div>
    <div class="mb-2">
        <i class="fas fa-sign-in-alt me-2 text-muted"></i>Clock In: 
        <strong>{{ $todayAttendance->time_in ? $todayAttendance->time_in . ' WIB' : '-' }}</strong>
    </div>
    <div class="mb-2">
        <i class="fas fa-sign-out-alt me-2 text-muted"></i>Clock Out: 
        <strong>{{ $todayAttendance->time_out ? $todayAttendance->time_out . ' WIB' : 'Belum Absen Pulang' }}</strong>
    </div>
    <div class="mb-0">
        <i class="fas fa-hourglass-half me-2 text-muted"></i>Durasi Kerja: 
        <span class="badge bg-dark">{{ $todayAttendance->duration }}</span>
    </div>
    </div>

                            @if(!$hasPulang)
                                <div class="border-top pt-3 mt-2">
                                    <p class="small text-muted mb-2"><i class="fas fa-info-circle me-1"></i> Selesai piket? Laporkan waktu kepulangan Anda:</p>
                                    <form action="{{ route('petugas.attendance.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="action_type" value="clock_out">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Jam Pulang (Opsional / Bypass)</label>
                                            <input type="time" name="manual_time" class="form-control input-gov rounded" placeholder="Kosongkan jika absen real-time">
                                        </div>
                                        <button type="submit" class="btn btn-warning w-100 rounded fw-bold text-dark"><i class="fas fa-sign-out-alt me-2"></i>Kirim Presensi Pulang (Clock Out)</button>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-info text-center border-0 small fw-bold mt-2 mb-0 py-2">
                                    <i class="fas fa-check-double me-1"></i> Sesi kerja hari ini telah berakhir secara resmi.
                                </div>
                            @endif
                        @else
                            <div class="text-center py-3">
                                <a href="{{ route('petugas.attendance.form') }}" class="btn btn-danger btn-sm fw-bold w-100 py-2"><i class="fas fa-exclamation-circle me-2"></i>Buka Gerbang Presensi Masuk</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card card-gov border-0 shadow-sm">
                    <div class="card-gov-header"><i class="fas fa-folder-open me-2 text-muted"></i> Form Laporan Kerja (Multi-Step Smart Wizard)</div>
                    <div class="card-body p-4">
                        @if(!$todayAttendance)
                            <div class="text-center py-4">
                                <i class="fas fa-lock fa-2x mb-3 text-secondary"></i><br>
                                <span class="text-dark d-block fw-bold mb-1">[AKSES DIREKAM KUNCI]</span>
                                <small class="text-muted">Formulir terkunci. Harap lakukan pengisian presensi masuk terlebih dahulu untuk mengaktifkan pelaporan.</small>
                            </div>
                        @else
                            <div class="step-indicator">
                                <div class="step-dot active" id="dot-1">1</div>
                                <div class="step-dot" id="dot-2">2</div>
                                <div class="step-dot" id="dot-3">3</div>
                            </div>

                            <form id="wizardPatrolForm" action="{{ route('petugas.patrol.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                                @csrf
                                
                                <div class="wizard-step active" id="step-1">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-layer-group me-1"></i> Langkah 1: Klasifikasi Kerja</h6>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Rumpun Kategori Kerja <span class="text-danger">*</span></label>
                                        <select name="rumpun_kategori" id="rumpun_kategori" class="form-select input-gov rounded" required>
                                            <option value="" disabled selected>-- Pilih Rumpun --</option>
                                            <option value="Patroli Harian">Patroli Harian</option>
                                            <option value="Advanced Assessment">Advanced Assessment</option>
                                        </select>
                                        <div class="invalid-feedback">Rumpun kategori kerja wajib dipilih.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Kategori Folder Google Drive <span class="text-danger">*</span></label>
                                        <select name="main_menu" id="main_menu" class="form-select input-gov rounded" required>
                                            <option value="" disabled selected>-- Pilih Folder V-Drive --</option>
                                            <option value="Patroli Siber">Patroli Siber</option>
                                            <option value="Bug Hunter">Bug Hunter</option>
                                            <option value="CTI">CTI</option>
                                            <option value="Laporan Insiden">Laporan Insiden</option>
                                            <option value="Sosial Media">Sosial Media</option>
                                            <option value="Vul/Pen Test">Vul/Pen Test</option>
                                        </select>
                                        <div class="invalid-feedback">Folder virtual drive tujuan wajib ditentukan.</div>
                                    </div>
                                    <button type="button" class="btn btn-secondary btn-sm w-100 mt-2 py-2" onclick="validateAndNext(1, 2)">Lanjut ke Langkah 2 <i class="fas fa-arrow-right ms-1"></i></button>
                                </div>

                                <div class="wizard-step" id="step-2">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-university me-1"></i> Langkah 2: Target & Ruang Lingkup</h6>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Nama Perangkat Daerah / Instansi <span class="text-danger">*</span></label>
                                        <input type="text" id="opdSearch" class="form-control form-control-sm mb-2 rounded" placeholder="🔍 Ketik untuk filter instansi...">
                                        <select name="agency_name" id="agencySelect" class="form-select input-gov rounded" required onchange="toggleManualInput('agency')">
                                            <option value="" disabled selected>-- Pilih Instansi Terdaftar --</option>
                                            @foreach($listOPD as $opd)
                                                <option value="{{ $opd }}">{{ $opd }}</option>
                                            @endforeach
                                            <option value="Lainnya">-- Perangkat Daerah Tidak Ada (Input Manual) --</option>
                                        </select>
                                        <input type="text" name="agency_name_manual" id="agencyManual" class="form-control input-gov rounded mt-2 d-none" placeholder="Masukkan nama instansi baru...">
                                        <div class="invalid-feedback">Nama instansi wajib ditentukan.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Judul Kegiatan / Isu Insiden <span class="text-danger">*</span></label>
                                        <select name="category" id="categorySelect" class="form-select input-gov rounded" required onchange="toggleManualInput('category')">
                                            <option value="" disabled selected>-- Pilih Isu Makro --</option>
                                            @foreach($listKategori as $kat)
                                                <option value="{{ $kat }}">{{ $kat }}</option>
                                            @endforeach
                                            <option value="Lainnya">-- Isu/Kategori Insiden Baru (Input Manual) --</option>
                                        </select>
                                        <input type="text" name="category_manual" id="categoryManual" class="form-control input-gov rounded mt-2 d-none" placeholder="Masukkan kategori insiden baru...">
                                        <div class="invalid-feedback">Kategori atau Isu insiden tidak boleh kosong.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Target URL Aplikasi / Website (Opsional)</label>
                                        <input type="url" name="target_url" class="form-control input-gov rounded" placeholder="https://example.go.id">
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="button" class="btn btn-light btn-sm w-50 border" onclick="prevStep(1)"><i class="fas fa-arrow-left me-1"></i> Kembali</button>
                                        <button type="button" class="btn btn-secondary btn-sm w-50" onclick="validateAndNext(2, 3)">Lanjut <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>

                                <div class="wizard-step" id="step-3">
                                    <h6 class="fw-bold mb-3 text-primary"><i class="fas fa-biohazard me-1"></i> Langkah 3: Matriks Risiko & Unggah Berkas</h6>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Tingkat Ancaman / Dampak Risiko <span class="text-danger">*</span></label>
                                        <select name="threat_level" id="threat_level" class="form-select input-gov rounded" required>
                                            <option value="Low">Low (Rendah)</option>
                                            <option value="Medium">Medium (Sedang)</option>
                                            <option value="High">High (Tinggi)</option>
                                            <option value="Critical">Critical (Sangat Kritis)</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Deskripsi Ringkas Aktivitas Kerja <span class="text-danger">*</span></label>
                                        <textarea name="description" id="description" class="form-control input-gov rounded" rows="2" required placeholder="Tulis rincian pengerjaan dokumen teknis..."></textarea>
                                        <div class="invalid-feedback">Deskripsi log pengerjaan wajib diisi.</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Catatan Koordinasi Instansi (Opsional)</label>
                                        <textarea name="coordination_note" class="form-control input-gov rounded" rows="2" placeholder="Tulis catatan disposisi jika butuh tindak lanjut..."></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark">Unggah Bukti Dukung (PDF/DOCX/XLSX/PNG/JPG) <span class="text-danger">* Maks 2MB</span></label>
                                        <input type="file" name="file_evidence" id="fileEvidenceInput" class="form-control input-gov rounded" accept=".pdf,.docx,.xlsx,.jpg,.png" required onchange="previewFile()">
                                        <div class="invalid-feedback" id="fileErrorFeedback">Berkas lampiran utama wajib dilampirkan.</div>
                                        
                                        <div class="preview-container" id="filePreviewContainer">
                                            <div class="d-flex align-items-center gap-3">
                                                <div id="visualPreviewArea"></div>
                                                <div class="text-truncate">
                                                    <span class="fw-bold d-block small text-dark" id="previewFileName">-</span>
                                                    <small class="text-muted font-monospace" id="previewFileSize">-</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-light btn-sm w-50 border" onclick="prevStep(2)"><i class="fas fa-arrow-left me-1"></i> Kembali</button>
                                        <button type="submit" class="btn btn-gov-petugas btn-sm w-50 rounded"><i class="fas fa-cloud-upload-alt me-1"></i> Kirim Laporan</button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card card-gov border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-2" style="color: #0f3057;"><i class="fas fa-history me-2 text-muted"></i> Histori Transmisi Laporan Anda</h5>
                    <p class="text-muted small mb-4">Daftar rekaman log digital pengerjaan berkas insiden siber yang terdaftar atas nama kredensial Anda.</p>

                    <div class="row g-2 mb-4">
                        <div class="col-sm-3">
                            <div class="p-3 bg-light border rounded text-center">
                                <small class="text-muted fw-bold d-block">TOTAL LOG</small>
                                <span class="h4 fw-bold text-dark">{{ $patrols->total() }}</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="p-3 bg-success bg-opacity-10 border border-success border-opacity-20 rounded text-center">
                                <small class="text-success fw-bold d-block">VERIFIED</small>
                                <span class="h4 fw-bold text-success">
                                    {{ $patrols->where('status', 'Verified')->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-20 rounded text-center">
                                <small class="text-warning fw-bold d-block">PENDING</small>
                                <span class="h4 fw-bold text-warning">
                                    {{ $patrols->where('status', 'Pending')->count() }}
                                </span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-20 rounded text-center">
                                <small class="text-danger fw-bold d-block">REVISI</small>
                                <span class="h4 fw-bold text-danger">
                                    {{ $patrols->where('status', 'Perlu Perbaikan')->count() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('petugas.dashboard') }}" method="GET" class="row g-2 mb-4">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control input-gov rounded" placeholder="Cari sasaran OPD, isu atau kode tiket..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="folder" class="form-select input-gov rounded">
                                <option value="">-- Semua Kategori Folder --</option>
                                <option value="Patroli Siber" {{ request('folder') == 'Patroli Siber' ? 'selected' : '' }}>Patroli Siber</option>
                                <option value="Bug Hunter" {{ request('folder') == 'Bug Hunter' ? 'selected' : '' }}>Bug Hunter</option>
                                <option value="CTI" {{ request('folder') == 'CTI' ? 'selected' : '' }}>CTI</option>
                                <option value="Laporan Insiden" {{ request('folder') == 'Laporan Insiden' ? 'selected' : '' }}>Laporan Insiden</option>
                                <option value="Sosial Media" {{ request('folder') == 'Sosial Media' ? 'selected' : '' }}>Sosial Media</option>
                                <option value="Vul/Pen Test" {{ request('folder') == 'Vul/Pen Test' ? 'selected' : '' }}>Vul/Pen Test</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-gov-petugas w-100 fw-bold rounded shadow-sm"><i class="fas fa-filter"></i> SYNCHRONIZE</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-gov align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Tiket</th>
                                    <th>Klasifikasi / V-Drive</th>
                                    <th>Instansi Target & Masalah</th>
                                    <th>Bobot</th>
                                    <th>Validasi Matrix</th>
                                    <th>Tanggapan Administrator</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patrols as $patrol)
                                <tr>
                                    <td><span class="badge bg-dark font-monospace">{{ $patrol->log_code }}</span></td>
                                    <td>
                                        <small class="d-block text-muted fw-semibold">{{ $patrol->rumpun_kategori }}</small>
                                        <span class="badge bg-secondary font-monospace p-1" style="font-size: 0.75rem;">[{{ $patrol->main_menu }}]</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark" style="font-size: 1.01rem;">{{ $patrol->opd_sasaran }}</span><br>
                                        <span class="text-muted small d-block mb-1">{{ $patrol->kategori_insiden }}</span>
                                        @if($patrol->target_url)
                                            <a href="{{ $patrol->target_url }}" target="_blank" class="small text-truncate text-decoration-none d-inline-block" style="max-width: 180px;">
                                                <i class="fas fa-external-link-alt me-1 text-primary"></i>Buka Link Target
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $patrol->threat_badge_color }}">{{ $patrol->threat_level }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status {{ $patrol->status == 'Verified' ? 'bg-success text-white' : ($patrol->status == 'Perlu Perbaikan' ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                                            {{ $patrol->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($patrol->status == 'Perlu Perbaikan')
                                            <div class="p-2 border border-danger border-opacity-30 rounded bg-danger bg-opacity-10 text-danger fw-bold small">
                                                <i class="fas fa-exclamation-circle me-1"></i> Koreksi: {{ $patrol->admin_correction }}
                                            </div>
                                        @elseif($patrol->status == 'Verified')
                                            <span class="text-success fw-bold small"><i class="fas fa-check-circle me-1"></i> Arsip Sukses Di-approve</span>
                                        @else
                                            <span class="text-muted small italic"><i class="far fa-hourglass me-1 animate-pulse"></i> Menunggu review dokumen...</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-database fa-2x mb-2 d-block text-secondary"></i>
                                        Belum ada rekaman log data laporan siber dari sesi Anda dalam sistem database.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        {{ $patrols->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Realtime Dropdown Filter Instansi (Custom Search Engine)
        document.getElementById('opdSearch')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const select = document.getElementById('agencySelect');
            const options = select.options;

            for (let i = 0; i < options.length; i++) {
                const text = options[i].text.toLowerCase();
                if (options[i].value === "Lainnya" || options[i].value === "") {
                    options[i].style.display = ""; // Tetap tampilkan opsi 'Lainnya' dan default
                    continue;
                }
                options[i].style.display = text.includes(query) ? "" : "none";
            }
        });

        // Smart Form Multi-Step Client Validation
        function validateAndNext(currentStep, targetStep) {
            const stepElement = document.getElementById('step-' + currentStep);
            const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value || input.value.trim() === "") {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                    input.classList.add('is-valid');
                }
            });

            // Jika validasi lolos, ijinkan pindah step
            if (isValid) {
                nextStep(targetStep);
            }
        }

        function nextStep(step) {
            document.querySelectorAll('.wizard-step').forEach(el => el.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');
            updateIndicators(step);
        }

        function prevStep(step) {
            document.querySelectorAll('.wizard-step').forEach(el => el.classList.remove('active'));
            document.getElementById('step-' + step).classList.add('active');
            updateIndicators(step);
        }

        function updateIndicators(currentStep) {
            for (let i = 1; i <= 3; i++) {
                let dot = document.getElementById('dot-' + i);
                if (i < currentStep) {
                    dot.className = "step-dot completed";
                    dot.innerHTML = '<i class="fas fa-check"></i>';
                } else if (i === currentStep) {
                    dot.className = "step-dot active";
                    dot.innerHTML = i;
                } else {
                    dot.className = "step-dot";
                    dot.innerHTML = i;
                }
            }
        }

        // Handle Input Manual Untuk Kategori Instansi & Isu Baru
        function toggleManualInput(type) {
            if (type === 'agency') {
                const select = document.getElementById('agencySelect');
                const manualInput = document.getElementById('agencyManual');
                if (select.value === 'Lainnya') {
                    manualInput.classList.remove('d-none');
                    manualInput.setAttribute('required', 'required');
                } else {
                    manualInput.classList.add('d-none');
                    manualInput.removeAttribute('required');
                    manualInput.classList.remove('is-invalid');
                }
            } else if (type === 'category') {
                const select = document.getElementById('categorySelect');
                const manualInput = document.getElementById('categoryManual');
                if (select.value === 'Lainnya') {
                    manualInput.classList.remove('d-none');
                    manualInput.setAttribute('required', 'required');
                } else {
                    manualInput.classList.add('d-none');
                    manualInput.removeAttribute('required');
                    manualInput.classList.remove('is-invalid');
                }
            }
        }

        // Live Image File Preview & Size Validation Engine
        function previewFile() {
            const fileInput = document.getElementById('fileEvidenceInput');
            const previewContainer = document.getElementById('filePreviewContainer');
            const visualArea = document.getElementById('visualPreviewArea');
            const nameLabel = document.getElementById('previewFileName');
            const sizeLabel = document.getElementById('previewFileSize');
            const errorFeedback = document.getElementById('fileErrorFeedback');
            
            const file = fileInput.files[0];
            
            if (file) {
                // Batasan 2MB (2 * 1024 * 1024 bytes)
                if (file.size > 2097152) {
                    fileInput.classList.add('is-invalid');
                    errorFeedback.innerText = "Ukuran berkas terlalu besar! Maksimal berkas yang diizinkan adalah 2 MB.";
                    fileInput.value = ""; // reset input
                    previewContainer.style.display = "none";
                    return;
                } else {
                    fileInput.classList.remove('is-invalid');
                    fileInput.classList.add('is-valid');
                }

                nameLabel.innerText = file.name;
                sizeLabel.innerText = (file.size / 1024).toFixed(2) + " KB";
                
                // Cek tipe berkas untuk pratinjau visual
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        visualArea.innerHTML = `<img src="${e.target.result}" class="preview-image border">`;
                    }
                    reader.readAsDataURL(file);
                } else {
                    // Berkas non-gambar (pdf, docx, xlsx)
                    let iconClass = "fa-file-alt text-secondary";
                    if (file.name.endsWith('.pdf')) iconClass = "fa-file-pdf text-danger";
                    else if (file.name.endsWith('.xlsx')) iconClass = "fa-file-excel text-success";
                    else if (file.name.endsWith('.docx')) iconClass = "fa-file-word text-primary";
                    
                    visualArea.innerHTML = `<i class="fas ${iconClass} fa-3x p-2"></i>`;
                }
                
                previewContainer.style.display = "block";
            } else {
                previewContainer.style.display = "none";
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>