@extends('admin.admin-layout')

@section('title', 'Detail Folder Arsip Virtual - {{ $folder->name }}')

@section('page_heading', 'Detail Folder Arsip Virtual')

@section('breadcrumb')
Home > Manajemen Data > <span class="text-dark">Folder Virtual</span> > <span class="text-primary fw-semibold">{{ $folder->name }}</span>
@endsection

@section('content')

<div class="folder-detail-container">

{{-- Header Folder --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #1E3A8A 0%, #2F6FED 100%);">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-3 bg-white bg-opacity-20 text-white p-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="fas fa-folder fa-2x"></i>
                </div>
                <div>
                    <h4 class="fw-bold text-white mb-1">{{ $folder->name }}</h4>
                    <div class="d-flex flex-wrap gap-3 text-white-50 small">
                        <span><i class="fas fa-calendar me-1"></i> Tahun: <strong>{{ $folder->year }}</strong></span>
                        <span><i class="fas fa-layer-group me-1"></i> Kategori: <strong>{{ $folder->main_menu ?? 'Umum' }}</strong></span>
                        <span><i class="fas fa-file me-1"></i> {{ $folder->virtualFiles->count() }} File</span>
                        <span><i class="fas fa-hdd me-1"></i> {{ $folder->virtualFiles->sum('size') > 0 ? \App\Models\VirtualFile::formatBytes($folder->virtualFiles->sum('size')) : '0 B' }}</span>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalUploadFile">
                    <i class="fas fa-upload"></i> Unggah File
                </button>
                <button type="button" class="btn btn-outline-light px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalImportLaporan">
                    <i class="fas fa-file-import"></i> Import Laporan
                </button>
                <a href="{{ route('admin.folders.index') }}" class="btn btn-outline-light px-4 py-2 rounded-3 fw-semibold border-white">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>
        
        @if($folder->description)
        <div class="mt-3 p-3 bg-white bg-opacity-15 rounded-3 border-white border-opacity-25">
            <small class="text-white"><i class="fas fa-info-circle me-1"></i> <strong>Deskripsi:</strong> {{ $folder->description }}</small>
        </div>
        @endif
    </div>
</div>

{{-- Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
            <div class="text-primary fw-bold fs-4">{{ $folder->virtualFiles->where('source', 'patroli')->count() }}</div>
            <div class="text-muted small">Dari Patroli</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
            <div class="text-secondary fw-bold fs-4">{{ $folder->virtualFiles->where('source', 'manual')->count() }}</div>
            <div class="text-muted small">Upload Manual</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
            <div class="text-success fw-bold fs-4">{{ $folder->virtualFiles->where('source', 'patroli')->whereNotNull('laporan_id')->count() }}</div>
            <div class="text-muted small">Dengan Laporan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 text-center h-100">
            <div class="text-info fw-bold fs-4">{{ $importableLaporan->count() }}</div>
            <div class="text-muted small">Tersedia Import</div>
        </div>
    </div>
</div>

{{-- Tab Navigasi --}}
<ul class="nav nav-tabs nav-tabs-bordered mb-4" id="folderTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab">
            <i class="fas fa-files me-1"></i> File di Folder ({{ $folder->virtualFiles->count() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="import-tab" data-bs-toggle="tab" data-bs-target="#import" type="button" role="tab">
            <i class="fas fa-file-import me-1"></i> Import Laporan ({{ $importableLaporan->count() }} tersedia)
        </button>
    </li>
</ul>

<div class="tab-content" id="folderTabsContent">
    
    {{-- Tab File --}}
    <div class="tab-pane fade show active" id="files" role="tabpanel">
        @if($folder->virtualFiles->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-folder-open fa-4x text-muted opacity-30"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Folder Masih Kosong</h5>
                <p class="text-muted mb-4">Belum ada file atau laporan yang diimpor ke folder ini.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2 shadow-sm" style="background-color: #1E3A8A; border-color: #1E3A8A;" data-bs-toggle="modal" data-bs-target="#modalUploadFile">
                        <i class="fas fa-upload"></i> Unggah File
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#modalImportLaporan">
                        <i class="fas fa-file-import"></i> Import Laporan
                    </button>
                </div>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fas fa-list me-1"></i> Daftar File ({{ $folder->virtualFiles->count() }})
                    </h6>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 250px;">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search"></i></span>
                            <input type="text" id="fileSearch" class="form-control border-start-0 shadow-none" placeholder="Cari file..." style="font-size: 12px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive" style="min-width: 800px;">
                    <table class="table table-hover align-middle mb-0" id="filesTable" style="min-width: 800px;">
                        <thead class="table-light text-muted border-bottom-0" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px;">
                            <tr>
                                <th class="py-3 ps-4">FILE</th>
                                <th class="py-3">SUMBER</th>
                                <th class="py-3" style="width: 100px;">UKURAN</th>
                                <th class="py-3" style="width: 150px;">TANGGAL</th>
                                <th class="py-3" style="width: 130px;">DIUNGGAH OLEH</th>
                                <th class="py-3 pe-4 text-center" style="width: 120px;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody style="font-size: 12.5px;">
                            @foreach($folder->virtualFiles as $file)
                            <tr class="file-row" data-name="{{ strtolower($file->original_name) }}" data-source="{{ $file->source }}">
                                <td class="py-3 ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-2 bg-{{ $file->is_image ? 'info' : ($file->is_pdf ? 'danger' : 'secondary') }} bg-opacity-10 text-{{ $file->is_image ? 'info' : ($file->is_pdf ? 'danger' : 'secondary') }} d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="{{ $file->file_icon }} fa-lg"></i>
                                        </div>
                                        <div class="text-truncate" style="max-width: 350px;">
                                            <strong class="text-dark d-block">{{ $file->original_name }}</div>
                                            @if($file->source === 'patroli' && $file->laporan)
                                            <small class="text-muted d-block" style="font-size: 10px;">
                                                <i class="fas fa-shield-alt me-1"></i> Laporan: {{ $file->laporan->log_code }} ({{ $file->laporan->opd_sasaran }})
                                            </small>
                                            @elseif($file->source === 'patroli')
                                            <small class="text-muted d-block" style="font-size: 10px;">
                                                <i class="fas fa-shield-alt me-1"></i> Laporan Patroli (tanpa file bukti)
                                            </small>
                                            @else
                                            <small class="text-muted d-block" style="font-size: 10px;">
                                                <i class="fas fa-user me-1"></i> Upload Manual
                                            </small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    @if($file->source === 'patroli')
                                    <span class="badge rounded-pill px-2 py-1 bg-info bg-opacity-10 text-info border border-info border-opacity-20" style="font-size: 9px;">
                                        <i class="fas fa-shield-alt me-1"></i> Patroli
                                    </span>
                                    @else
                                    <span class="badge rounded-pill px-2 py-1 bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20" style="font-size: 9px;">
                                        <i class="fas fa-upload me-1"></i> Manual
                                    </span>
                                    @endif
                                </td>
                                <td class="py-3 fw-semibold text-dark">{{ $file->human_size }}</td>
                                <td class="py-3 text-muted small">{{ $file->created_at->format('d M Y H:i') }}</td>
                                <td class="py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 26px; height: 26px; font-size: 10px;">
                                            {{ strtoupper(substr($file->uploader->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="fw-semibold text-dark small">{{ $file->uploader->name ?? 'Unknown' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 pe-4 text-center">
                                    <div class="d-inline-flex gap-1">
                                        @if($file->is_image || $file->is_pdf)
                                        <a href="{{ asset('storage/' . $file->path) }}" target="_blank" class="btn btn-sm btn-light text-primary border-0 rounded-2 shadow-2xs py-1 px-2" title="Lihat File">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endif
                                        <a href="{{ asset('storage/' . $file->path) }}" download class="btn btn-sm btn-light text-success border-0 rounded-2 shadow-2xs py-1 px-2" title="Unduh File">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <form action="{{ route('admin.folders.delete-file', [$folder->id, $file->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus file ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light text-danger border-0 rounded-2 shadow-2xs py-1 px-2" title="Hapus File">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>

    {{-- Tab Import Laporan --}}
    <div class="tab-pane fade" id="import" role="tabpanel">
        @if($importableLaporan->isEmpty())
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-check-circle fa-4x text-success opacity-50"></i>
                </div>
                <h5 class="fw-bold text-dark mb-2">Semua Laporan Sudah Diimpor</h5>
                <p class="text-muted mb-4">Tidak ada laporan baru yang bisa diimpor ke folder ini.</p>
            </div>
        </div>
        @else
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-transparent border-bottom py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fas fa-file-import me-1"></i> Pilih Laporan untuk Diimpor
                    </h6>
                    <small class="text-muted">Tahun: {{ $folder->year }} | Kategori: {{ $folder->main_menu }}</small>
                </div>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('admin.folders.import-laporan', $folder->id) }}" method="POST" id="formImportLaporan">
                    @csrf
                    <div class="table-responsive" style="min-width: 800px;">
                        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                            <thead class="table-light text-muted border-bottom-0" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.6px;">
                                <tr>
                                    <th class="py-3 ps-4" style="width: 50px;"><input type="checkbox" id="selectAllLaporan" class="form-check-input"></th>
                                    <th class="py-3">KODE LOG</th>
                                    <th class="py-3">OPD SASARAN</th>
                                    <th class="py-3">KATEGORI</th>
                                    <th class="py-3" style="width: 100px;">THREAT</th>
                                    <th class="py-3" style="width: 130px;">TANGGAL</th>
                                    <th class="py-3" style="width: 80px;">BUKTI</th>
                                    <th class="py-3" style="width: 130px;">PELAPOR</th>
                                    <th class="py-3" style="width: 100px;">STATUS</th>
                                </tr>
                            </thead>
                            <tbody style="font-size: 12.5px;">
                                @foreach($importableLaporan as $laporan)
                                <tr>
                                    <td class="py-3 ps-4">
                                        <input type="checkbox" name="laporan_ids[]" value="{{ $laporan->id }}" class="form-check-input laporan-checkbox">
                                    </td>
                                    <td class="py-3">
                                        <strong class="text-dark d-block fw-mono" style="font-size: 11px;">{{ $laporan->log_code }}</strong>
                                    </td>
                                    <td class="py-3 fw-semibold text-dark">{{ $laporan->opd_sasaran }}</td>
                                    <td class="py-3">
                                        <small class="text-muted d-block">{{ $laporan->kategori_insiden }}</small>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 rounded-pill fw-normal" style="font-size: 8px;">{{ $laporan->main_menu }} ({{ $laporan->rumpun_kategori }})</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @php
                                            $threatClass = match(strtolower($laporan->threat_level ?? 'medium')) {
                                                'critical' => 'bg-danger bg-opacity-10 text-danger',
                                                'high'     => 'bg-warning bg-opacity-10 text-warning',
                                                'medium'   => 'bg-info bg-opacity-10 text-info',
                                                'low'      => 'bg-secondary bg-opacity-10 text-secondary',
                                                default    => 'bg-secondary bg-opacity-10 text-secondary',
                                            };
                                        @endphp
                                        <span class="badge rounded-pill px-2 py-1 {{ $threatClass }}" style="font-size: 9px;">{{ $laporan->threat_level }}</span>
                                    </td>
                                    <td class="py-3 text-center text-muted small">{{ $laporan->created_at->format('d M Y') }}</td>
                                    <td class="py-3 text-center">
                                        @if($laporan->file_evidence)
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1" style="font-size: 9px;"><i class="fas fa-paperclip me-1"></i> Ada</span>
                                        @else
                                        <span class="text-muted fst-italic small">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 24px; height: 24px; font-size: 10px;">
                                                {{ strtoupper(substr($laporan->user->name ?? 'P', 0, 1)) }}
                                            </div>
                                            <span class="fw-semibold text-dark small">{{ $laporan->user->name ?? 'Petugas' }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if(strtolower($laporan->status) === 'pending')
                                        <span class="badge rounded-pill bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-2 py-1 fw-semibold" style="font-size: 9px;"><i class="fas fa-spinner fa-spin me-1 text-warning"></i> Pending</span>
                                        @elseif(in_array($laporan->status, ['Verified', 'Approved', 'Disetujui Admin']))
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-2 py-1 fw-semibold" style="font-size: 9px;"><i class="fas fa-check-circle me-1"></i> Verified</span>
                                        @elseif(in_array($laporan->status, ['Perlu Perbaikan', 'Revision', 'Revisi']))
                                        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-2 py-1 fw-semibold" style="font-size: 9px;"><i class="fas fa-exclamation-triangle me-1"></i> Revisi</span>
                                        @else
                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-2 py-1 fw-semibold" style="font-size: 9px;">{{ $laporan->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <span id="selectedCount">0</span> laporan dipilih dari {{ $importableLaporan->count() }} tersedia
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm" style="background-color: #1E3A8A; border-color: #1E3A8A;">
                                <i class="fas fa-file-import me-1"></i> Import Terpilih
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        @endif
    </div>

</div>

{{-- MODAL UNGGAH FILE --}}
<div class="modal fade" id="modalUploadFile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formUploadFile" action="{{ route('admin.folders.upload', $folder->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-15 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="fas fa-upload fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Unggah File Baru</h5>
                            <span class="text-muted small">Folder: {{ $folder->name }} ({{ $folder->year }})</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Pilih File <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control border-light-subtle rounded-3 shadow-none" required style="font-size: 13px;">
                        <div class="form-text text-muted" style="font-size: 12px;">Format: PDF, JPG, PNG, DOCX, XLSX, ZIP, RAR. Maksimal 20MB.</div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-3 btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 btn-sm fw-semibold text-dark shadow-sm" style="background-color: #1E3A8A; border-color: #1E3A8A;">
                        <i class="fas fa-upload me-1"></i> Unggah
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL IMPORT LAPORAN --}}
<div class="modal fade" id="modalImportLaporan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-info bg-opacity-15 text-info p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="fas fa-file-import fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0">Import Laporan ke Folder</h5>
                        <span class="text-muted small">Folder: {{ $folder->name }} ({{ $folder->year }})</span>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                @if($importableLaporan->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success opacity-50 mb-3"></i>
                    <h5 class="fw-bold text-dark">Semua Laporan Sudah Diimpor</h5>
                    <p class="text-muted mb-0">Tidak ada laporan baru yang bisa diimpor ke folder ini.</p>
                </div>
                @else
                <form action="{{ route('admin.folders.import-laporan', $folder->id) }}" method="POST" id="formImportLaporanModal">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark small">Pilih Laporan untuk Diimpor ({{ $importableLaporan->count() }} tersedia)</label>
                        <div class="table-responsive" style="max-height: 400px; min-width: 800px;">
                            <table class="table table-hover align-middle mb-0" style="font-size: 12px; min-width: 800px;">
                                <thead class="table-light text-muted border-bottom-0" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                                    <tr>
                                        <th class="py-2 ps-3" style="width: 40px;"><input type="checkbox" id="selectAllLaporanModal" class="form-check-input"></th>
                                        <th class="py-2">KODE LOG</th>
                                        <th class="py-2">OPD SASARAN</th>
                                        <th class="py-2">KATEGORI</th>
                                        <th class="py-2" style="width: 100px;">THREAT</th>
                                        <th class="py-2" style="width: 120px;">TANGGAL</th>
                                        <th class="py-2" style="width: 80px;">BUKTI</th>
                                        <th class="py-2" style="width: 120px;">PELAPOR</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 12px;">
                                    @foreach($importableLaporan as $laporan)
                                    <tr>
                                        <td class="py-2 ps-3">
                                            <input type="checkbox" name="laporan_ids[]" value="{{ $laporan->id }}" class="form-check-input laporan-checkbox-modal">
                                        </td>
                                        <td class="py-2"><strong class="text-dark fw-mono" style="font-size: 11px;">{{ $laporan->log_code }}</strong></td>
                                        <td class="py-2 fw-semibold text-dark small">{{ $laporan->opd_sasaran }}</td>
                                        <td class="py-2">
                                            <small class="text-muted d-block">{{ $laporan->kategori_insiden }}</small>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 rounded-pill fw-normal" style="font-size: 8px;">{{ $laporan->main_menu }} ({{ $laporan->rumpun_kategori }})</span>
                                        </td>
                                        <td class="py-2 text-center">
                                            @php
                                                $threatClass = match(strtolower($laporan->threat_level ?? 'medium')) {
                                                    'critical' => 'bg-danger bg-opacity-10 text-danger',
                                                    'high'     => 'bg-warning bg-opacity-10 text-warning',
                                                    'medium'   => 'bg-info bg-opacity-10 text-info',
                                                    'low'      => 'bg-secondary bg-opacity-10 text-secondary',
                                                    default    => 'bg-secondary bg-opacity-10 text-secondary',
                                                };
                                            @endphp
                                            <span class="badge rounded-pill px-2 py-1 {{ $threatClass }}" style="font-size: 9px;">{{ $laporan->threat_level }}</span>
                                        </td>
                                        <td class="py-2 text-center text-muted small">{{ $laporan->created_at->format('d M Y') }}</td>
                                        <td class="py-2 text-center">
                                            @if($laporan->file_evidence)
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 px-2 py-1" style="font-size: 9px;"><i class="fas fa-paperclip me-1"></i> Ada</span>
                                            @else
                                            <span class="text-muted fst-italic" style="font-size: 10px;">-</span>
                                            @endif
                                        </td>
                                        <td class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 22px; height: 22px; font-size: 9px;">
                                                    {{ strtoupper(substr($laporan->user->name ?? 'P', 0, 1)) }}
                                                </div>
                                                <span class="fw-semibold text-dark" style="font-size: 11px;">{{ $laporan->user->name ?? 'Petugas' }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-3 px-3 btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-info rounded-3 px-4 btn-sm fw-semibold text-dark shadow-sm">
                            <i class="fas fa-file-import me-1"></i> Import Terpilih
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkboxes
    const selectAllLaporan = document.getElementById('selectAllLaporan');
    const selectAllLaporanModal = document.getElementById('selectAllLaporanModal');
    const laporanCheckboxes = document.querySelectorAll('.laporan-checkbox');
    const laporanCheckboxesModal = document.querySelectorAll('.laporan-checkbox-modal');
    const selectedCount = document.getElementById('selectedCount');
    const formImportLaporan = document.getElementById('formImportLaporan');
    const formImportLaporanModal = document.getElementById('formImportLaporanModal');
    const formUploadFile = document.getElementById('formUploadFile');

    function updateSelectedCount(checkboxes, countElement) {
        const checked = document.querySelectorAll(checkboxes).length;
        if (countElement) countElement.textContent = checked;
    }

    if (selectAllLaporan) {
        selectAllLaporan.addEventListener('change', function() {
            laporanCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount('.laporan-checkbox:checked', selectedCount);
        });
    }

    if (selectAllLaporanModal) {
        selectAllLaporanModal.addEventListener('change', function() {
            laporanCheckboxesModal.forEach(cb => cb.checked = this.checked);
        });
    }

    laporanCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            updateSelectedCount('.laporan-checkbox:checked', selectedCount);
            selectAllLaporan.checked = document.querySelectorAll('.laporan-checkbox:checked').length === laporanCheckboxes.length;
        });
    });

    laporanCheckboxesModal.forEach(cb => {
        cb.addEventListener('change', function() {
            selectAllLaporanModal.checked = document.querySelectorAll('.laporan-checkbox-modal:checked').length === laporanCheckboxesModal.length;
        });
    });

    // File search filter
    const fileSearch = document.getElementById('fileSearch');
    const fileRows = document.querySelectorAll('.file-row');
    
    if (fileSearch) {
        fileSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            fileRows.forEach(row => {
                const name = row.dataset.name || '';
                row.style.display = name.includes(query) ? '' : 'none';
            });
        });
    }

    // File upload AJAX
    if (formUploadFile) {
        formUploadFile.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengunggah...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('modalUploadFile'));
                    modal.hide();
                    
                    // Reload page to show new file
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengunggah file.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Import laporan AJAX
    [formImportLaporan, formImportLaporanModal].forEach(form => {
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Memproses...';
                
                const formData = new FormData(this);
                
                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modals
                        document.querySelectorAll('.modal.show').forEach(modal => {
                            bootstrap.Modal.getInstance(modal)?.hide();
                        });
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengimpor laporan.');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    });

    // Delete file AJAX
    document.querySelectorAll('form[action*="/files/"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('Yakin menghapus file ini?')) return;
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Get the _method value if present (for DELETE)
            const formData = new FormData(this);
            const method = formData.get('_method') || 'POST';
            
            fetch(this.action, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('tr').remove();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus file.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHTML;
            });
        });
    });
});
</script>
</div>
@endsection
@push('scripts')