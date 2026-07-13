<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Operator Petugas - SIP-O-SIBER</title>
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
    </style>
</head>
<body>
    <nav class="navbar navbar-dark navbar-gov-petugas p-3 shadow-sm">
        <div class="container-fluid px-4">
            <span class="navbar-brand fw-bold text-white" style="font-size: 1.3rem;"><i class="fas fa-desktop text-warning me-2"></i> PANEL UTAMA PETUGAS</span>
            <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm fw-bold px-3"><i class="fas fa-power-off me-1 text-danger"></i> KELUAR</a>
        </div>
    </nav>

    <div class="container-fluid px-4 mt-4">
        <div class="p-3 bg-white border rounded mb-4 shadow-sm d-flex align-items-center justify-content-between">
            <h5 class="fw-bold mb-0" style="color: #0f3057;"><i class="fas fa-user-circle me-2 text-muted"></i>Operator Aktif: {{ Auth::user()->name }}</h5>
            <span class="badge bg-primary px-3 py-2 fw-bold text-uppercase"><i class="fas fa-id-badge me-1"></i> Petugas Magang</span>
        </div>
        
        @if(session('success')) 
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold p-3 mb-4 rounded shadow-sm">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            </div> 
        @endif
        @if(session('error')) 
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-bold p-3 mb-4 rounded shadow-sm">
                <i class="fas fa-exclamation-triangle me-1"></i> {{ session('error') }}
            </div> 
        @endif

        <div class="row">
            <div class="col-xl-4 col-lg-5 mb-4">
                
                <div class="card card-gov mb-4 border-0 shadow-sm">
                    <div class="card-gov-header"><i class="fas fa-clock me-2 text-muted"></i> Status Presensi Harian Anda</div>
                    <div class="card-body p-4">
                        @if($todayAttendance)
                            <div class="alert border-0 bg-light text-dark mb-3 p-3 shadow-sm rounded">
                                <div class="mb-2 d-flex align-items-center">
                                    <i class="fas fa-circle text-success me-2"></i> 
                                    Status Sesi Kerja: <span class="text-success fw-bold ms-1">AKTIF / ONLINE</span>
                                </div>
                                <div class="mb-2"><i class="fas fa-stopwatch me-2 text-muted"></i>Jam Terinput: <strong>{{ \Carbon\Carbon::parse($todayAttendance->time_in)->format('H:i') }} WIB</strong></div>
                                <div class="mb-2"><i class="fas fa-exchange-alt me-2 text-muted"></i>Kategori Aktif: <strong>Presensi {{ $todayAttendance->shift }}</strong></div>
                                <div class="mb-2"><i class="fas fa-star me-2 text-muted"></i>Evaluasi Sistem: <span class="badge bg-dark">{{ $todayAttendance->status }}</span></div>
                                <hr class="my-2 text-muted">
                                <div class="small"><i class="fas fa-clipboard-list me-2 text-muted"></i>Agenda/Kegiatan: <br><span class="text-secondary fw-semibold">{{ $todayAttendance->notes }}</span></div>
                            </div>

                            @if(!$hasPulang)
                                <div class="border-top pt-3 mt-2">
                                    <p class="small text-muted mb-2"><i class="fas fa-info-circle me-1"></i> Sudah selesai bertugas? Kirim presensi pulang di bawah ini:</p>
                                    <form action="{{ route('petugas.attendance.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="attendance_info" value="Pulang">
                                        
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Jam Pulang (Manual)</label>
                                            <input type="time" name="manual_time" value="{{ \Carbon\Carbon::now()->format('H:i') }}" class="form-control input-gov rounded" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold text-dark mb-1">Keterangan Hasil Kegiatan / Berkas Kerja Sore</label>
                                            <textarea name="notes" class="form-control input-gov rounded" rows="2" placeholder="Contoh: Selesai melakukan patroli siber harian..." required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-warning w-100 rounded fw-bold text-dark"><i class="fas fa-sign-out-alt me-2"></i>Kirim Presensi Pulang</button>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-info text-center border-0 small fw-bold mt-2 mb-0 py-2">
                                    <i class="fas fa-check-double me-1"></i> Sesi kerja hari ini telah selesai (Absen Masuk & Pulang Terpenuhi).
                                </div>
                            @endif
                        @else
                            <div class="text-center py-3">
                                <a href="{{ route('petugas.attendance.form') }}" class="btn btn-danger btn-sm fw-bold w-100"><i class="fas fa-exclamation-circle me-2"></i>Isi Presensi Awal Harian</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card card-gov border-0 shadow-sm">
                    <div class="card-gov-header"><i class="fas fa-folder-open me-2 text-muted"></i> Transmisi Berkas Laporan Kerja</div>
                    <div class="card-body p-4">
                        @if(!$todayAttendance)
                            <div class="text-center py-4">
                                <i class="fas fa-lock fa-2x mb-3 text-secondary"></i><br>
                                <span class="text-dark d-block fw-bold mb-1">[AKSES FORM DITUTUP]</span>
                                <small class="text-muted">Silakan lakukan pengisian presensi harian terlebih dahulu untuk membuka form pelaporan ini.</small>
                            </div>
                        @else
                            <form action="{{ route('petugas.patrol.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="attendance_info" value="{{ $todayAttendance->status }}">

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Kategori Folder Google Drive</label>
                                    <select name="main_menu" class="form-select input-gov rounded" required>
                                        <option value="Bug Hunter">Bug Hunter</option>
                                        <option value="CTI">CTI</option>
                                        <option value="Laporan Insiden">Laporan Insiden</option>
                                        <option value="Patroli Siber">Patroli Siber</option>
                                        <option value="Sosial Media">Sosial Media</option>
                                        <option value="Vul/Pen Test">Vul/Pen Test</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Nama Perangkat Daerah / Instansi</label>
                                    <input type="text" name="agency_name" class="form-control input-gov rounded" required placeholder="Contoh: Dinas Kesehatan">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Judul Kegiatan / Isu Berkas</label>
                                    <input type="text" name="category" class="form-control input-gov rounded" required placeholder="Contoh: Laporan Uji Kerentanan Aplikasi">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Tingkat Ancaman Siber</label>
                                    <select name="threat_level" class="form-select input-gov rounded" required>
                                        <option value="Low">Low</option>
                                        <option value="Medium">Medium</option>
                                        <option value="High">High</option>
                                        <option value="Critical">Critical</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-dark">Deskripsi Ringkas Aktivitas Kerja</label>
                                    <textarea name="description" class="form-control input-gov rounded" rows="3" required placeholder="Tulis rincian pengerjaan dokumen..."></textarea>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-dark">Unggah Lampiran Berkas (PDF / DOCX / XLSX)</label>
                                    <input type="file" name="bukti_file" class="form-control input-gov rounded" accept=".pdf,.docx,.xlsx" required>
                                </div>
                                <button type="submit" class="btn btn-gov-petugas w-100 rounded fw-bold shadow-sm"><i class="fas fa-cloud-upload-alt me-2"></i>Unggah Berkas Laporan</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-xl-8 col-lg-7 mb-4">
                <div class="card card-gov border-0 shadow-sm p-4">
                    <h5 class="fw-bold mb-4" style="color: #0f3057;"><i class="fas fa-history me-2 text-muted"></i> Histori Data Transmisi Laporan Saya</h5>

                    <form action="{{ route('petugas.dashboard') }}" method="GET" class="row g-2 mb-4">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control input-gov rounded" placeholder="Cari nama instansi..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <select name="folder" class="form-select input-gov rounded">
                                <option value="">-- Semua Kategori Folder --</option>
                                <option value="Bug Hunter" {{ request('folder') == 'Bug Hunter' ? 'selected' : '' }}>Bug Hunter</option>
                                <option value="CTI" {{ request('folder') == 'CTI' ? 'selected' : '' }}>CTI</option>
                                <option value="Laporan Insiden" {{ request('folder') == 'Laporan Insiden' ? 'selected' : '' }}>Laporan Insiden</option>
                                <option value="Patroli Siber" {{ request('folder') == 'Patroli Siber' ? 'selected' : '' }}>Patroli Siber</option>
                                <option value="Sosial Media" {{ request('folder') == 'Sosial Media' ? 'selected' : '' }}>Sosial Media</option>
                                <option value="Vul/Pen Test" {{ request('folder') == 'Vul/Pen Test' ? 'selected' : '' }}>Vul/Pen Test</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-gov-petugas w-100 fw-bold rounded shadow-sm"><i class="fas fa-search"></i> CARI</button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover table-gov align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Folder Tujuan</th>
                                    <th>Instansi & Judul Dokumen</th>
                                    <th>Validasi Matrix</th>
                                    <th>Catatan / Tanggapan Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patrols as $patrol)
                                <tr>
                                    <td><strong class="text-dark">{{ $patrol->created_at->format('d/m/Y') }}</strong></td>
                                    <td><span class="badge bg-secondary font-monospace p-2">[{{ $patrol->main_menu }}]</span></td>
                                    <td>
                                        <span class="fw-bold text-dark" style="font-size: 1.02rem;">{{ $patrol->agency_name }}</span><br>
                                        <span class="text-muted small">{{ $patrol->category }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-status {{ $patrol->status == 'Verified' ? 'bg-success text-white' : ($patrol->status == 'Perlu Perbaikan' ? 'bg-danger text-white' : 'bg-warning text-dark') }}">
                                            {{ $patrol->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($patrol->status == 'Perlu Perbaikan')
                                            <div class="p-2 border border-danger border-opacity-50 rounded bg-danger bg-opacity-10 text-danger fw-bold small">
                                                <i class="fas fa-exclamation-circle me-1"></i> Koreksi: {{ $patrol->admin_correction }}
                                            </div>
                                        @elseif($patrol->status == 'Verified')
                                            <span class="text-success fw-bold small"><i class="fas fa-check-circle me-1"></i> Sukses Masuk Google Drive</span>
                                        @else
                                            <span class="text-muted small italic"><i class="far fa-hourglass me-1"></i> Menunggu review dokumen...</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada rekaman transmisi data laporan dari Anda dalam sistem database.</td>
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