@extends('admin.admin-layout')

@section('title', 'Manajemen Folder Virtual Arsip')

@section('page_heading', 'Manajemen Folder Virtual Arsip')

{{-- Breadcrumb ditaruh di section khusus agar menyatu di bawah Judul Header --}}
@section('breadcrumb')
<nav aria-label="breadcrumb" class="mt-1">
    <ol class="breadcrumb mb-0" style="font-size: 13px; --bs-breadcrumb-divider: '>';">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Home</a>
        </li>
        <li class="breadcrumb-item text-muted">Manajemen Data</li>
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page" style="color: #000000 !important;">Folder Virtual</li>
    </ol>
</nav>
@endsection

@section('content')

{{-- Notifikasi Berhasil / Gagal --}}
@if(session('success'))
    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="font-size: 13px;">
        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="font-size: 13px;">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

{{-- Header Action Bar (Pencarian & Tombol Aksi) --}}
<div class="row align-items-center mb-4 g-3">
    <div class="col-md-5">
        <form method="GET" action="{{ route('admin.folders.index') }}">
            <div class="input-group shadow-sm rounded-3 bg-white">
                <span class="input-group-text bg-white border-end-0 text-muted ps-3"><i class="fas fa-search"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0 ps-0 shadow-none" placeholder="Cari nama folder arsip..." style="font-size: 13px;">
            </div>
        </form>
    </div>
    <div class="col-md-7 text-md-end">
        <div class="d-flex justify-content-md-end gap-2">
            <button type="button" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBuatFolder" style="font-size: 13px; background-color: #1E3A8A; border-color: #1E3A8A;">
                <i class="fas fa-plus"></i> Buat Folder Baru
            </button>
        </div>
    </div>
</div>

{{-- Grid Folder Kartu --}}
<div class="row g-4 mb-4">
    @forelse($folders as $folder)
        <div class="col-xxl-3 col-xl-4 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 position-relative folder-card transition-hover">
                
                {{-- Tombol Hapus Folder di Pojok Kanan Atas Kartu --}}
                <form action="{{ route('admin.folders.destroy', $folder->id) }}" method="POST" class="position-absolute top-0 end-0 m-2 style-delete-form" onsubmit="return confirm('Apakah Anda yakin ingin menghapus folder {{ $folder->name }} beserta isinya?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-light text-danger border-0 rounded-circle p-2 shadow-2xs" title="Hapus Folder" style="z-index: 5; width: 32px; height: 32px; line-height: 1;">
                        <i class="fas fa-trash-alt" style="font-size: 12px;"></i>
                    </button>
                </form>

                <div class="mb-3 pt-2">
                    <i class="fas fa-folder text-warning" style="font-size: 54px;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-1 text-truncate" style="font-size: 15px;" title="{{ $folder->name }}">{{ $folder->name }}</h6>
                <p class="text-muted small mb-0" style="font-size: 12px;">{{ $folder->description ?? 'Tidak ada deskripsi' }}</p>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="py-4">
                <i class="fas fa-folder-open text-muted opacity-50 mb-3" style="font-size: 48px;"></i>
                <h6 class="fw-bold text-muted">Belum ada folder virtual</h6>
                <p class="text-muted small mb-0">Klik tombol "Buat Folder Baru" di atas untuk menambahkan folder pertama.</p>
            </div>
        </div>
    @endforelse
</div>

{{-- ================= MODAL: BUAT FOLDER BARU ================= --}}
<div class="modal fade" id="modalBuatFolder" tabindex="-1" aria-labelledby="modalBuatFolderLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4 p-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark fs-6 d-flex align-items-center gap-2">
                    <i class="fas fa-folder-plus text-warning fs-5"></i> Buat Folder Virtual Baru
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.folders.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-secondary">Nama Folder <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="fas fa-folder"></i></span>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Arsip_Keuangan_2026" style="font-size: 13px;" required autofocus>
                        </div>
                        <div class="form-text text-muted" style="font-size: 11px;">Gunakan garis bawah (_) untuk spasi nama folder agar konsisten.</div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small text-secondary">Keterangan / Deskripsi Singkat</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Tuliskan deskripsi isi atau peruntukan folder ini..." style="font-size: 13px;"></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-3 fw-semibold" data-bs-dismiss="modal" style="font-size: 13px;">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm" style="font-size: 13px; background-color: #1E3A8A; border-color: #1E3A8A;">
                            <i class="fas fa-save me-1"></i> Buat Folder
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .folder-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background-color: #ffffff;
    }
    .folder-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08) !important;
    }
    .style-delete-form {
        z-index: 10;
    }
</style>
@endpush