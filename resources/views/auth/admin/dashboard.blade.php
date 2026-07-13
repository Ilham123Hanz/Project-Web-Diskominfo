<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Kontrol Petugas - SIP-O-SIBER</title>
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
            border-radius: 6px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
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
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-gov-petugas p-3 shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-white" style="font-size: 1.3rem;">
                <i class="fas fa-laptop-code text-warning me-2"></i> PANEL OPERASIONAL PETUGAS
            </span>
            <div>
                <span class="text-white me-3 d-none d-md-inline-block fw-semibold">
                    <i class="fas fa-user-circle text-warning me-1"></i> {{ Auth::user()->name }}
                </span>
                <a href="{{ route('logout') }}" class="btn btn-danger btn-sm fw-bold px-3">
                    <i class="fas fa-sign-out-alt me-1"></i> KELUAR
                </a>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        <div class="p-3 bg-white border rounded mb-4 shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="fw-bold mb-0" style="color: #0f3057;"><i class="fas fa-desktop me-2"></i>Ruang Kerja Siber Lapangan</h4>
            <span class="badge bg-dark p-2 font-monospace">Hak Akses: OPERATOR PETUGAS</span>
        </div>

        @if(session('success')) 
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold p-3 mb-4 rounded shadow-sm">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div> 
        @endif

        <div class="row g-4">
            <div class="col-lg-4">
                
                <div class="card card-gov border-0 shadow-sm overflow-hidden mb-4">
                    <div class="card-gov-header"><i class="fas fa-fingerprint me-2 text-warning"></i>Log Presensi Harian & Evaluasi</div>
                    <div class="card-body p-4">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="fw-bold text-muted w-50 small">Jam Masuk (Manual)</td>
                                <td class="fw-bold text-dark">: {{ $todayAttendance->manual_time ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted small">Evaluasi Masuk</td>
                                Kak, <td>: 
                                    @if(isset($todayAttendance))
                                        @if($todayAttendance->status === 'Terlambat')
                                            <span class="badge bg-danger text-white px-2 py-1 small">Terlambat</span>
                                        @else
                                            <span class="badge bg-success text-white px-2 py-1 small">Tepat Waktu</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        @if(!$hasValue = $hasPulang)
                            <div class="border-top pt-3 mt-3">
                                <p class="small text-muted mb-2 fw-semibold text-danger"><i class="fas fa-info-circle me-1"></i> Sesi kerja selesai? Kirim log presensi pulang berikut:</p>
                                <form action="{{ route('petugas.attendance.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="attendance_info" value="Pulang">
                                    
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold text-dark mb-1">Jam Pulang (Manual)</label>
                                        <input type="time" name="manual_time" class="form-control input-gov rounded" value="{{ \Carbon\Carbon::now()->format('H:i') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-dark mb-1">Keterangan Aktivitas Sore</label>
                                        <textarea name="notes" class="form-control input-gov rounded" rows="2" placeholder="Contoh: Selesai penyusunan berkas siber harian..." required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-warning w-100 rounded fw-bold text-dark py-2"><i class="fas fa-sign-out-alt me-2"></i>KIRIM ABSEN PULANG</button>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-success border-0 text-center small fw-bold mt-3 mb-0 py-2">
                                <i class="fas fa-check-double me-1"></i> Log Kerja Terpenuhi (Masuk & Pulang Selesai).
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card card-gov border-0 shadow-sm overflow-hidden">
                    <div class="card-gov-header"><i class="fas fa-cloud-upload-alt me-2 text-warning"></i>Transmisi Unggah Berkas Kerja</div>
                    <form action="{{ route('petugas.patrol.store') }}" method="POST" enctype="multipart/form-data" class="card-body p-4">
                        @csrf
                        <input type="hidden" name="attendance_info" value="Hadir">

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">Kategori Folder Drive</label>
                            <select name="main_menu" class="form-select input-gov rounded" required>
                                <option value="">-- Pilih Folder --</option>
                                <option value="Bug Hunter">Bug Hunter</option>
                                <option value="CTI">CTI</option>
                                <option value="Laporan Insiden">Laporan Insiden</option>
                                <option value="Patroli Siber">Patroli Siber</option>
                                <option value="Sosial Media">Sosial Media</option>
                                <option value="Vul/Pen Test">Vul/Pen Test</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">Nama Perangkat Daerah / Instansi</label>
                            <input type="text" name="agency_name" class="form-control input-gov rounded" placeholder="Contoh: Dinas Kominfo Kabupaten" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">Sub Kategori Isu Berkas</label>
                            <input type="text" name="category" class="form-control input-gov rounded" placeholder="Contoh: SQL Injection / Defacement Website" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">Tingkat Ancaman Matrix</label>
                            <select name="threat_level" class="form-select input-gov rounded" required>
                                <option value="Low">Low Threat</option>
                                <option value="Medium">Medium Threat</option>
                                <option value="High">High Threat</option>
                                <option value="Critical">Critical Threat</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">Tautan Domain Sasaran (Opsional)</label>
                            <input type="url" name="target_url" class="form-control input-gov rounded" placeholder="https://contoh-domain.go.id">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">Deskripsi & Rincian Temuan Lapangan</label>
                            <textarea name="description" class="form-control input-gov rounded" rows="3" placeholder="Tuliskan kronologi singkat analisis berkas..." required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark mb-1">Dokumen Lampiran Bukti (PDF/DOCX/XLSX - Max 10MB)</label>
                            <input type="file" name="bukti_file" class="form-control input-gov rounded" required>
                        </div>

                        <button type="submit" class="btn btn-gov-petugas w-100 p-2 rounded fw-bold shadow-sm mt-2"><i class="fas fa-paper-plane me-1"></i> KIRIM LAPORAN KE PUSAT</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-8">
                <form action="{{ route('petugas.dashboard') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <input type="text" name="search" class="form-control input-gov rounded" placeholder="Cari Instansi / Sub Isu..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="folder" class="form-select input-gov rounded">
                            <option value="">-- Semua Folder --</option>
                            <option value="Bug Hunter" {{ request('folder') == 'Bug Hunter' ? 'selected' : '' }}>Bug Hunter</option>
                            <option value="CTI" {{ request('folder') == 'CTI' ? 'selected' : '' }}>CTI</option>
                            <option value="Laporan Insiden" {{ request('folder') == 'Laporan Insiden' ? 'selected' : '' }}>Laporan Insiden</option>
                            <option value="Patroli Siber" {{ request('folder') == 'Patroli Siber' ? 'selected' : '' }}>Patroli Siber</option>
                            <option value="Sosial Media" {{ request('folder') == 'Sosial Media' ? 'selected' : '' }}>Sosial Media</option>
                            <option value="Vul/Pen Test" {{ request('folder') == 'Vul/Pen Test' ? 'selected' : '' }}>Vul/Pen Test</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-gov-petugas w-100 p-2 rounded fw-bold shadow-sm"><i class="fas fa-search me-1"></i> CARI DATA</button>
                    </div>
                </form>

                <div class="card card-gov border-0 shadow-sm overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover table-gov align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Folder & Waktu</th>
                                    <th>Instansi & Sub Isu</th>
                                    <th>Dokumen</th>
                                    <th>Status Validasi Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patrols as $p)
                                <tr>
                                    <td>
                                        <span class="badge bg-secondary font-monospace p-2">[{{ $p->main_menu }}]</span><br>
                                        <small class="text-muted d-block mt-1"><i class="far fa-clock me-1"></i>{{ $p->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong class="text-dark">{{ $p->agency_name }}</strong><br>
                                        <span class="text-muted small">{{ $p->category }}</span>
                                        @if($p->admin_correction)
                                            <div class="mt-2 text-danger small bg-danger bg-opacity-10 p-2 rounded border border-danger border-opacity-25">
                                                <strong>Koreksi Admin:</strong> {{ $p->admin_correction }}
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($p->file_evidence)
                                            <a href="{{ asset('storage/bukti_files/' . $p->file_evidence) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2 fw-bold rounded text-decoration-none"><i class="fas fa-eye me-1"></i> LIHAT</a>
                                        @else
                                            <span class="text-danger small fw-bold">No File</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-status {{ $p->status == 'Verified' ? 'bg-success text-white' : ($p->status == 'Perlu Perbaikan' ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                                            {{ $p->status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Anda belum mengunggah rekam transmisi berkas harian ke sistem pusat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>