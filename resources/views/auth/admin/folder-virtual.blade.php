@extends('layouts.admin_layout')
@section('title', 'Manajemen Folder Virtual Arsip')
@section('page_heading', 'Manajemen Folder Virtual Arsip')
@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Pengaturan</li>
    <li class="breadcrumb-item active">Folder Virtual</li>
@endsection

@section('content')
<div class="card card-custom p-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <input type="text" class="form-control form-control-sm w-auto" style="min-width: 250px;" placeholder="Cari nama folder arsip...">
        <div class="d-flex gap-2">
            <button class="btn btn-outline-danger btn-sm px-3">Hapus Folder</button>
            <button class="btn btn-primary btn-sm px-3 fw-bold">+ Buat Folder Baru</button>
        </div>
    </div>

    <!-- Layout Grid Card Item Virtual Folder -->
    <div class="row g-3">
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="p-4 border rounded text-center bg-white shadow-sm position-relative shadow-hover" style="border-radius: 12px !important;">
                <i class="fas fa-folder text-warning fa-4x mb-3"></i>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Arsip_Judol_2026</h6>
                <small class="text-muted font-monospace" style="font-size: 0.75rem;">15 File Bukti</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="p-4 border rounded text-center bg-white shadow-sm position-relative shadow-hover" style="border-radius: 12px !important;">
                <i class="fas fa-folder text-warning fa-4x mb-3"></i>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Arsip_Defacement_2026</h6>
                <small class="text-muted font-monospace" style="font-size: 0.75rem;">8 File Bukti</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="p-4 border rounded text-center bg-white shadow-sm position-relative shadow-hover" style="border-radius: 12px !important;">
                <i class="fas fa-folder text-warning fa-4x mb-3"></i>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Laporan_PDF_Final</h6>
                <small class="text-muted font-monospace" style="font-size: 0.75rem;">45 File Laporan</small>
            </div>
        </div>
        <div class="col-xl-3 col-md-4 col-sm-6">
            <div class="p-4 border rounded text-center bg-white shadow-sm position-relative shadow-hover" style="border-radius: 12px !important;">
                <i class="fas fa-folder text-warning fa-4x mb-3"></i>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">Backup_Database</h6>
                <small class="text-muted font-monospace" style="font-size: 0.75rem; color: #198754 !important;">System Auto-Backup</small>
            </div>
        </div>
    </div>
</div>
@endsection