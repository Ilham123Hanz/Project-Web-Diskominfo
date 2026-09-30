@extends('admin.admin-layout')

@section('title', 'Rekapitulasi Laporan Patroli')
@section('page_heading', 'Rekapitulasi Laporan Patroli')

@section('breadcrumb')
Home > Manajemen Log > <span class="text-dark">Rekapitulasi Laporan</span>
@endsection

@section('content')

<!-- Filter Form -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.laporan.patroli.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm bg-white border shadow-sm rounded-3">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold text-dark small">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm bg-white border shadow-sm rounded-3">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm px-4 rounded-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>
                @if(request('start_date') || request('end_date'))
                    <a href="{{ route('admin.laporan.patroli.index') }}" class="btn btn-outline-secondary btn-sm px-4 rounded-3 fw-bold ms-2">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Export Buttons -->
<div class="d-flex justify-content-end gap-2 mb-4">
    <a href="{{ route('admin.laporan.patroli.excel') . '?' . request()->getQueryString() }}" class="btn btn-success btn-sm px-4 rounded-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
        <i class="fas fa-file-csv me-1"></i> Export CSV
    </a>
    <a href="{{ route('admin.laporan.patroli.pdf') . '?' . request()->getQueryString() }}" target="_blank" class="btn btn-danger btn-sm px-4 rounded-3 fw-bold d-inline-flex align-items-center gap-1 shadow-sm">
        <i class="fas fa-file-pdf me-1"></i> Export PDF (Rekap)
    </a>
</div>

<!-- Data Table -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted border-bottom-0" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.8px;">
                    <tr>
                        <th class="py-3 px-4" style="width: 140px;">TGL / ID LOG</th>
                        <th class="py-3">DETAIL KRONOLOGI & OPD</th>
                        <th class="py-3" style="width: 170px;">PELAPOR</th>
                        <th class="py-3" style="width: 140px;">STATUS</th>
                        <th class="py-3 px-4 text-center" style="width: 220px;">AKSI</th>
                    </tr>
                </thead>
                <tbody style="font-size: 13.5px;">
                    @forelse($laporans ?? [] as $log)
                        <tr>
                            <td class="py-3 px-4">
                                <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($log->created_at)->format('d M Y') }}</div>
                                <div class="text-muted fw-mono" style="font-size: 12px;">{{ $log->log_code ?? 'LOG-00' . $log->id }}</div>
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark mb-1">
                                    {{ $log->opd_sasaran ?? 'OPD Umum' }} 
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 rounded-pill fw-normal ms-1" style="font-size: 10px;">{{ $log->kategori_insiden }}</span>
                                </div>
                                <div class="text-muted text-truncate" style="max-width: 340px; font-size: 12.5px;">{{ $log->description }}</div>
                                @if($log->file_evidence)
                                    <a href="{{ asset('storage/bukti_files/' . $log->created_at->year . '/' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $log->kategori_insiden) . '/' . $log->file_evidence) }}" target="_blank" class="text-primary text-decoration-none mt-1 d-inline-block" style="font-size: 11.5px;">
                                        <i class="fas fa-paperclip me-1"></i> Lampiran Bukti
                                    </a>
                                @endif
                            </td>
                            <td class="py-3 text-dark">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-2 fw-bold" style="width: 30px; height: 30px; font-size: 12px;">
                                        {{ strtoupper(substr($log->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <span class="fw-semibold text-dark">{{ $log->user->name ?? 'Petugas Lapangan' }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                @if(strtolower($log->status) === 'pending')
                                    <span class="badge rounded-pill bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-spinner fa-spin me-1 text-warning"></i> Pending
                                    </span>
                                @elseif(in_array($log->status, ['Verified', 'Approved', 'Disetujui Admin']))
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-check-circle me-1"></i> Verified
                                    </span>
                                @elseif(in_array($log->status, ['Perlu Perbaikan', 'Revision', 'Revisi']))
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Perlu Perbaikan
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2 fw-semibold" style="font-size: 11px;">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="d-inline-flex gap-1 bg-light p-1 rounded-3 border">
                                    {{-- 1. Tombol Setuju --}}
                                    <form action="{{ route('admin.patrol.update-status', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Setujui laporan ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="Verified">
                                        <button type="submit" class="btn btn-sm btn-white text-success border-0 rounded-2 shadow-2xs py-1 px-2" title="Setujui Laporan">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>

                                    {{-- 2. Tombol Revisi --}}
                                    <button type="button" 
                                            class="btn btn-sm btn-white text-warning border-0 rounded-2 shadow-2xs py-1 px-2" 
                                            title="Minta Revisi"
                                            onclick="openRevisiModal('{{ route('admin.patrol.update-status', $log->id) }}', '{{ addslashes($log->admin_correction ?? '') }}')">
                                        <i class="fas fa-pen"></i>
                                    </button>

                                    {{-- 3. Tombol Detail --}}
                                    <button type="button" class="btn btn-sm btn-white text-dark border-0 rounded-2 shadow-2xs py-1 px-2" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $log->id }}" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    {{-- 4. Tombol Cetak PDF --}}
                                    <a href="{{ route('admin.laporan.patroli.pdf', $log->id) }}" target="_blank" class="btn btn-sm btn-white text-secondary border-0 rounded-2 shadow-2xs py-1 px-2" title="Cetak PDF">
                                        <i class="fas fa-print"></i>
                                    </a>

                                    {{-- 5. Tombol Hapus --}}
                                    <form action="{{ route('admin.patrol.delete', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus laporan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-white text-danger border-0 rounded-2 shadow-2xs py-1 px-2" title="Hapus Laporan">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- MODAL DETAIL LAPORAN -->
                        <div class="modal fade" id="modalDetail{{ $log->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                                    <div class="modal-header border-bottom-0 pb-0">
                                        <h5 class="modal-title fw-bold text-dark">Detail Laporan Patroli <span class="text-primary">#{{ $log->log_code ?? $log->id }}</span></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label class="text-muted small fw-semibold d-block">Pelapor / Petugas</label>
                                                <span class="fw-bold text-dark">{{ $log->user->name ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small fw-semibold d-block">OPD Target / Instansi</label>
                                                <span class="fw-bold text-dark">{{ $log->opd_sasaran ?? '-' }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small fw-semibold d-block">Kategori Insiden</label>
                                                <span class="badge bg-light text-dark border">{{ $log->kategori_insiden }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="text-muted small fw-semibold d-block">Target URL</label>
                                                <a href="{{ $log->target_url }}" target="_blank" class="text-primary text-break small">{{ $log->target_url }}</a>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="text-muted small fw-semibold d-block mb-1">Uraian Kronologi Laporan</label>
                                            <div class="p-3 bg-light rounded-3 text-dark border-0" style="white-space: pre-line; font-size: 13px;">{{ $log->description }}</div>
                                        </div>

                                        @if($log->admin_correction)
                                            <div class="alert alert-warning border-0 rounded-3 mb-3">
                                                <strong class="d-block mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Catatan Perbaikan Admin:</strong>
                                                <p class="mb-0 small">{{ $log->admin_correction }}</p>
                                            </div>
                                        @endif

                                        <div>
                                            <label class="text-muted small fw-semibold d-block mb-1">Lampiran File Bukti</label>
                                            @if($log->file_evidence)
                                                <a href="{{ asset('storage/bukti_files/' . $log->created_at->year . '/' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $log->kategori_insiden) . '/' . $log->file_evidence) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-2">
                                                    <i class="fas fa-download me-1"></i> Buka / Unduh Bukti Digital
                                                </a>
                                            @else
                                                <span class="text-muted fst-italic small">Tanpa Lampiran</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="modal-footer border-top-0 pt-0">
                                        <button type="button" class="btn btn-light px-4 rounded-3 btn-sm fw-semibold" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary opacity-50"></i>
                                Belum ada data laporan patroli dari petugas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(isset($laporans) && method_exists($laporans, 'links'))
        <div class="px-4 py-3 bg-white border-top">
            {{ $laporans->links() }}
        </div>
    @endif
</div>

<!-- MODAL REVISI BARU (MODERN & TEMPLATE CHIPS) -->
<div class="modal fade" id="modalRevisi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formRevisi" method="POST" action="" class="w-100">
            @csrf
            <input type="hidden" name="status" value="Perlu Perbaikan">
            <div class="modal-content border-0 shadow-lg rounded-4">
                
                {{-- Header Modern --}}
                <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-warning bg-opacity-15 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="fas fa-exclamation-triangle fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0">Catatan Revisi Laporan</h5>
                            <span class="text-muted small">Berikan instruksi perbaikan untuk petugas</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Opsi Cepat (Template) --}}
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-semibold">Pilih Alasan Cepat (Klik untuk isi):</label>
                        <div class="d-flex flex-wrap gap-1">
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" onclick="addQuickText('Lampiran bukti tangkapan layar tidak jelas/terpotong.')">+ Bukti Tidak Jelas</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" onclick="addQuickText('URL Target tidak dapat diakses atau salah.')">+ URL Error</button>
                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill" onclick="addQuickText('Deskripsi kronologi kejadian kurang mendetail.')">+ Kronologi Kurang</button>
                        </div>
                    </div>

                    {{-- Textarea Alasan --}}
                    <div class="mb-2">
                        <label for="admin_correction" class="form-label fw-semibold text-dark small">Rincian Perbaikan <span class="text-danger">*</span></label>
                        <textarea class="form-control border-light-subtle rounded-3 shadow-none p-3" id="admin_correction" name="admin_correction" rows="4" placeholder="Tuliskan detail perbaikan yang harus dilakukan petugas..." required style="font-size: 13px;"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light rounded-3 px-3 btn-sm fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-3 px-4 btn-sm fw-semibold text-dark shadow-sm">
                        <i class="fas fa-paper-plane me-1"></i> Kirim Revisi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Fungsi Buka Modal Revisi
function openRevisiModal(actionUrl, existingNote = '') {
    let form = document.getElementById('formRevisi');
    let textarea = document.getElementById('admin_correction');
    
    form.action = actionUrl;
    textarea.value = existingNote;
    
    var myModal = new bootstrap.Modal(document.getElementById('modalRevisi'));
    myModal.show();
}

// Fungsi Opsi Cepat Teks
function addQuickText(text) {
    let textarea = document.getElementById('admin_correction');
    if (textarea.value.trim() === '') {
        textarea.value = text;
    } else {
        textarea.value += ' ' + text;
    }
}
</script>
@endpush
@endsection