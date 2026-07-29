@extends('admin.admin-layout')

@section('title', 'Pusat Manajemen Master Data')

@section('breadcrumb')
    <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-secondary">Home</a>
    <span class="mx-1">&gt;</span>
    <span class="text-secondary">Manajemen Data</span>
    <span class="mx-1">&gt;</span>
    <span class="text-dark fw-bold">Master Data OPD</span>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
@endpush

@section('content')
<div class="container-fluid px-4 py-2">

    {{-- Alert Success --}}
    @if(session('success'))
    <div id="alert-success" class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    {{-- Alert Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <strong class="d-block mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i> Gagal Menyimpan Data:</strong>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- NAV TABS -->
    <ul class="nav nav-tabs custom-tabs mb-4 border-bottom" id="opdTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold text-dark px-4 py-3 d-inline-flex align-items-center" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-tab-pane" type="button" role="tab">
                <i class="bi bi-envelope me-2 text-primary fs-5"></i> Email Kontak OPD
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold text-dark px-4 py-3 d-inline-flex align-items-center" id="sosmed-tab" data-bs-toggle="tab" data-bs-target="#sosmed-tab-pane" type="button" role="tab">
                <i class="bi bi-share me-2 text-primary fs-5"></i> Media Sosial
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold text-dark px-4 py-3 d-inline-flex align-items-center" id="aplikasi-tab" data-bs-toggle="tab" data-bs-target="#aplikasi-tab-pane" type="button" role="tab">
                <i class="bi bi-display me-2 text-primary fs-5"></i> Aplikasi Pemprov
            </button>
        </li>
    </ul>   

    <!-- TAB CONTENT -->
    <div class="tab-content" id="opdTabContent">

        <!-- TAB 1: EMAIL KONTAK OPD -->
        <div class="tab-pane fade show active" id="email-tab-pane" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h5 class="fw-bold mb-0">Direktori Email Resmi OPD</h5>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group search-box" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchEmail" class="form-control bg-light border-start-0 ps-0" placeholder="Cari OPD / Email...">
                        </div>

                        <a href="{{ route('admin.master-opd.email.export') }}" class="btn btn-outline-secondary px-3 rounded-3 fw-semibold d-flex align-items-center gap-2">
                            <i class="bi bi-download"></i> Export CSV
                        </a>
                        <button class="btn btn-primary px-3 rounded-3 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahEmail">
                            <i class="bi bi-plus-lg"></i> Tambah Data Email
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">
                            <tr>
                                <th width="80" class="text-center">NO</th>
                                <th>NAMA OPD / INSTANSI</th>
                                <th>ALAMAT EMAIL RESMI</th>
                                <th>KETERANGAN / PIC</th>
                                <th width="150" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyEmail">
                            @forelse($opdEmails as $key => $item)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $key + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $item->masterOpd->nama_opd ?? '-' }}</td>
                                    <td class="text-muted">
                                        @if($item->alamat_email)
                                            <a href="mailto:{{ $item->alamat_email }}" class="text-decoration-none text-dark fw-medium">
                                                <i class="bi bi-envelope-at me-1 text-primary"></i>{{ $item->alamat_email }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-muted border fw-normal">Belum diisi</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-link text-primary text-decoration-none fw-semibold btn-edit-email" 
                                            data-id="{{ $item->id }}" 
                                            data-opd="{{ $item->opd_id }}" 
                                            data-email="{{ $item->alamat_email }}"
                                            data-keterangan="{{ $item->keterangan }}">Edit</button>
                                        <span class="text-muted">|</span>
                                        <form action="{{ route('admin.master-opd.email.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none fw-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-data">
                                    <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan. Silakan tambah data baru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: MEDIA SOSIAL -->
        <div class="tab-pane fade" id="sosmed-tab-pane" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h5 class="fw-bold mb-0">Direktori Media Sosial Resmi OPD</h5>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group search-box" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchSosmed" class="form-control bg-light border-start-0 ps-0" placeholder="Cari OPD / Sosmed...">
                        </div>

                        <a href="{{ route('admin.master-opd.sosmed.export') }}" class="btn btn-outline-secondary px-3 rounded-3 fw-semibold d-flex align-items-center gap-2">
                            <i class="bi bi-download"></i> Export CSV
                        </a>
                        <button class="btn btn-primary px-3 rounded-3 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahSosmed">
                            <i class="bi bi-plus-lg"></i> Tambah Akun Media Sosial
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">
                            <tr>
                                <th width="80" class="text-center">NO</th>
                                <th>NAMA OPD / INSTANSI</th>
                                <th>INSTAGRAM</th>
                                <th>KETERANGAN</th>
                                <th width="150" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="tbodySosmed">
                            @forelse($opdSosmeds as $key => $item)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $key + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $item->masterOpd->nama_opd ?? '-' }}</td>
                                    <td class="text-primary fw-semibold">
                                        @if($item->nama_akun_ig)
                                            <a href="https://instagram.com/{{ ltrim($item->nama_akun_ig, '@') }}" target="_blank" class="text-decoration-none text-primary">
                                                <i class="bi bi-instagram me-1"></i>{{ '@' . ltrim($item->nama_akun_ig, '@') }}
                                            </a>
                                        @else
                                            <span class="badge bg-light text-muted border fw-normal">Belum diisi</span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $item->keterangan ?? '-' }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-link text-primary text-decoration-none fw-semibold btn-edit-sosmed" 
                                            data-id="{{ $item->id }}" 
                                            data-opd="{{ $item->opd_id }}" 
                                            data-instagram="{{ $item->nama_akun_ig }}"
                                            data-keterangan="{{ $item->keterangan }}">Edit</button>
                                        <span class="text-muted">|</span>
                                        <form action="{{ route('admin.master-opd.sosmed.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none fw-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-data">
                                    <td colspan="5" class="text-center py-4 text-muted">Data tidak ditemukan. Silakan tambah data baru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: APLIKASI PEMPROV -->
        <div class="tab-pane fade" id="aplikasi-tab-pane" role="tabpanel">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <h5 class="fw-bold mb-0">Direktori Aplikasi Pemprov</h5>
                    
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div class="input-group search-box" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="searchAplikasi" class="form-control bg-light border-start-0 ps-0" placeholder="Cari Aplikasi / URL / OPD...">
                        </div>

                        <a href="{{ route('admin.master-opd.aplikasi.export') }}" class="btn btn-outline-secondary px-3 rounded-3 fw-semibold d-flex align-items-center gap-2">
                            <i class="bi bi-download"></i> Export CSV
                        </a>
                        <button class="btn btn-primary px-3 rounded-3 fw-semibold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalTambahAplikasi">
                            <i class="bi bi-plus-lg"></i> Tambah Data Aplikasi
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light text-uppercase text-secondary" style="font-size: 12px; letter-spacing: 0.5px;">
                            <tr>
                                <th width="60" class="text-center">NO</th>
                                <th>NAMA SISTEM ELEKTRONIK / ASET</th>
                                <th>KODE ASET</th>
                                <th>DOMAIN / URL</th>
                                <th>OPD PEMILIK</th>
                                <th>STATUS</th>
                                <th width="150" class="text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyAplikasi">
                            @forelse($opdAplikasis as $key => $item)
                                <tr>
                                    <td class="text-center text-muted fw-semibold">{{ $key + 1 }}</td>
                                    <td class="fw-bold text-dark">{{ $item->nama_sistem ?? '-' }}</td>
                                    <td class="text-muted">{{ $item->kode_aset ?? '-' }}</td>
                                    <td>
                                        @if($item->domain_url)
                                            <a href="{{ Str::startsWith($item->domain_url, ['http://', 'https://']) ? $item->domain_url : 'https://' . $item->domain_url }}" target="_blank" class="text-primary text-decoration-none">
                                                {{ $item->domain_url }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->masterOpd->nama_opd ?? '-' }}</td>
                                    <td>
                                        @php
                                            $status = strtolower($item->status_operasional ?? '');
                                            $badgeClass = match($status) {
                                                'aktif'        => 'bg-success-subtle text-success border-success-subtle',
                                                'tidak aktif', 'non-aktif' => 'bg-danger-subtle text-danger border-danger-subtle',
                                                'pemeliharaan', 'pengembangan' => 'bg-warning-subtle text-warning border-warning-subtle',
                                                default        => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }} border px-2 py-1 rounded-2 text-capitalize">
                                            {{ $item->status_operasional ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-link text-primary text-decoration-none fw-semibold btn-edit-aplikasi" 
                                            data-id="{{ $item->id }}" 
                                            data-nama="{{ $item->nama_sistem }}"
                                            data-kode="{{ $item->kode_aset }}"
                                            data-url="{{ $item->domain_url }}"
                                            data-opd="{{ $item->opd_id }}"
                                            data-status="{{ $item->status_operasional }}">Edit</button>
                                        <span class="text-muted">|</span>
                                        <form action="{{ route('admin.master-opd.aplikasi.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger text-decoration-none fw-semibold">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr class="no-data">
                                    <td colspan="7" class="text-center py-4 text-muted">Data tidak ditemukan. Silakan tambah data baru.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ================= MODAL TAMBAH EMAIL ================= -->
<div class="modal fade" id="modalTambahEmail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.master-opd.email.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Data Email OPD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Instansi / OPD</label>
                        <select name="master_opd_id" class="form-select" required>
                            <option value="" selected disabled>Pilih Instansi...</option>
                            @foreach($masterOpd as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email Resmi</label>
                        <input type="email" name="email" class="form-control" placeholder="admin@opd.go.id" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / PIC</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT EMAIL ================= -->
<div class="modal fade" id="modalEditEmail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="formEditEmail" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Data Email OPD</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pilih Instansi / OPD</label>
                        <select name="master_opd_id" id="edit_email_opd_id" class="form-select" required>
                            @foreach($masterOpd as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alamat Email Resmi</label>
                        <input type="email" name="email" id="edit_email_val" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan / PIC</label>
                        <textarea name="keterangan" id="edit_email_ket" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL TAMBAH SOSMED ================= -->
<div class="modal fade" id="modalTambahSosmed" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.master-opd.sosmed.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Akun Media Sosial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Perangkat Daerah</label>
                        <select name="master_opd_id" class="form-select" required>
                            <option value="" selected disabled>Pilih Perangkat Daerah...</option>
                            @foreach($masterOpd as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Akun Instagram</label>
                        <!-- PENTING: name="instagram" agar sesuai dengan $request->instagram di Controller -->
                        <input type="text" name="instagram" class="form-control" placeholder="username_opd" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT SOSMED ================= -->
<div class="modal fade" id="modalEditSosmed" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="formEditSosmed" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Akun Media Sosial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Perangkat Daerah</label>
                        <select name="master_opd_id" id="edit_sosmed_opd_id" class="form-select" required>
                            @foreach($masterOpd as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Akun Instagram</label>
                        <!-- PENTING: name="instagram" agar sesuai dengan $request->instagram di Controller -->
                        <input type="text" name="instagram" id="edit_sosmed_ig" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" id="edit_sosmed_ket" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL TAMBAH APLIKASI ================= -->
<div class="modal fade" id="modalTambahAplikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form action="{{ route('admin.master-opd.aplikasi.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Tambah Data Aplikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Sistem Elektronik / Aset</label>
                        <input type="text" name="nama_aplikasi" class="form-control" placeholder="Contoh: SIMPEG" required>
                    </div>  
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kode Aset</label>
                            <input type="text" name="kode_aset" class="form-control" placeholder="Opsional">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Domain / URL</label>
                            <input type="text" name="url" class="form-control" placeholder="https://..." required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">OPD Pemilik</label>
                        <select name="master_opd_id" class="form-select" required>
                            <option value="" selected disabled>Pilih OPD...</option>
                            @foreach($masterOpd as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Operasional</label>
                        <select name="status" class="form-select" required>
                            <option value="Aktif" selected>Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                            <option value="Pemeliharaan">Pemeliharaan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT APLIKASI ================= -->
<div class="modal fade" id="modalEditAplikasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <form id="formEditAplikasi" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold">Edit Data Aplikasi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Sistem Elektronik / Aset</label>
                        <input type="text" name="nama_aplikasi" id="edit_app_nama" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kode Aset</label>
                            <input type="text" name="kode_aset" id="edit_app_kode" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Domain / URL</label>
                            <input type="text" name="url" id="edit_app_url" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">OPD Pemilik</label>
                        <select name="master_opd_id" id="edit_app_opd" class="form-select" required>
                            @foreach($masterOpd as $opd)
                                <option value="{{ $opd->id }}">{{ $opd->nama_opd }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status Operasional</label>
                        <select name="status" id="edit_app_status" class="form-select" required>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                            <option value="Pemeliharaan">Pemeliharaan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- 1. OTOMASI LIVE SEARCH TABEL ---
    function setupTableSearch(inputId, tbodyId) {
        const searchInput = document.getElementById(inputId);
        const tbody = document.getElementById(tbodyId);

        if (!searchInput || !tbody) return;

        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase().trim();
            const rows = tbody.querySelectorAll('tr:not(.no-data)');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }

    setupTableSearch('searchEmail', 'tbodyEmail');
    setupTableSearch('searchSosmed', 'tbodySosmed');
    setupTableSearch('searchAplikasi', 'tbodyAplikasi');


    // --- 2. MODAL EDIT EMAIL HANDLER ---
    document.querySelectorAll('.btn-edit-email').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const opd = this.getAttribute('data-opd');
            const email = this.getAttribute('data-email');
            const ket = this.getAttribute('data-keterangan');

            const form = document.getElementById('formEditEmail');
            form.action = "{{ url('admin/master-opd/email') }}/" + id;

            document.getElementById('edit_email_opd_id').value = opd;
            document.getElementById('edit_email_val').value = email || '';
            document.getElementById('edit_email_ket').value = ket || '';

            const modal = new bootstrap.Modal(document.getElementById('modalEditEmail'));
            modal.show();
        });
    });


    // --- 3. MODAL EDIT SOSMED HANDLER ---
    document.querySelectorAll('.btn-edit-sosmed').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const opd = this.getAttribute('data-opd');
            const instagram = this.getAttribute('data-instagram');
            const ket = this.getAttribute('data-keterangan');

            const form = document.getElementById('formEditSosmed');
            form.action = "{{ url('admin/master-opd/sosmed') }}/" + id;

            document.getElementById('edit_sosmed_opd_id').value = opd;
            document.getElementById('edit_sosmed_ig').value = instagram || '';
            document.getElementById('edit_sosmed_ket').value = ket || '';

            const modal = new bootstrap.Modal(document.getElementById('modalEditSosmed'));
            modal.show();
        });
    });


    // --- 4. MODAL EDIT APLIKASI HANDLER ---
    document.querySelectorAll('.btn-edit-aplikasi').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const kode = this.getAttribute('data-kode');
            const url = this.getAttribute('data-url');
            const opd = this.getAttribute('data-opd');
            const status = this.getAttribute('data-status');

            const form = document.getElementById('formEditAplikasi');
            form.action = "{{ url('admin/master-opd/aplikasi') }}/" + id;

            document.getElementById('edit_app_nama').value = nama || '';
            document.getElementById('edit_app_kode').value = (kode && kode !== 'null') ? kode : '';
            document.getElementById('edit_app_url').value = (url && url !== 'null') ? url : '';
            document.getElementById('edit_app_opd').value = opd;
            document.getElementById('edit_app_status').value = status || 'Aktif';

            const modal = new bootstrap.Modal(document.getElementById('modalEditAplikasi'));
            modal.show();
        });
    });

});
</script>
@endpush