@extends('admin.admin-layout')

@section('title', 'Distribusi Laporan SMTP')

@section('page_heading', 'Distribusi Laporan (SMTP Mailer)')

@section('content')

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0" style="font-size: 13px;">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Home</a></li>
        <li class="breadcrumb-item text-muted">Manajemen Log</li>
        <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">Distribusi Laporan</li>
    </ol>
</nav>

{{-- Notifikasi Sukses / Sistem --}}
@if(session('success'))
<div class="alert alert-success border-0 shadow-sm rounded-4 d-flex align-items-center mb-4 py-3 px-4" style="background-color: #E8F8F0; color: #0F5132;" role="alert">
    <i class="fas fa-circle-check fs-5 me-3 text-success"></i>
    <div class="fw-medium" style="font-size: 14px;">
        {{ session('success') }}
    </div>
</div>
@else
{{-- Dummy Alert Statis Sesuai Gambar Contoh --}}
<div class="alert border-0 shadow-sm rounded-4 d-flex align-items-center mb-4 py-3 px-4" style="background-color: #E8F8F0; color: #0F5132;" role="alert">
    <i class="fas fa-circle-check fs-5 me-3 text-success"></i>
    <div class="fw-medium" style="font-size: 14px;">
        Notifikasi Sistem: Laporan LOG-001 berhasil dikirim otomatis ke bappeda@lampungprov.go.id via SMTP Mailer.
    </div>
</div>
@endif

{{-- Tabel Distribusi Laporan --}}
<div class="card border-0 shadow-sm rounded-4 p-4">
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light text-uppercase text-muted" style="font-size: 11px; letter-spacing: 0.5px;">
                <tr>
                    <th class="py-3 ps-3">ID Laporan</th>
                    <th class="py-3">OPD Tujuan</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 text-center" style="width: 220px;">Aksi Eksekusi</th>
                </tr>
            </thead>
            <tbody>
                {{-- Baris Data 1 (Contoh Sesuai Gambar) --}}
                <tr>
                    <td class="py-3 ps-3 fw-bold text-dark">LOG-001</td>
                    <td class="py-3 fw-semibold text-secondary">BAPPEDA Provinsi Lampung</td>
                    <td class="py-3">
                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #D1F8E8; color: #0F5132; font-size: 12px;">Terkirim</span>
                    </td>
                    <td class="py-3 text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 13px;">
                                <i class="fas fa-file-lines"></i> Cetak PDF
                            </a>
                            <a href="#" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 13px; background-color: #2F6FED; border-color: #2F6FED;">
                                <i class="fas fa-paper-plane"></i> Kirim SMTP
                            </a>
                        </div>
                    </td>
                </tr>

                {{-- Baris Data 2 (Contoh Sesuai Gambar) --}}
                <tr>
                    <td class="py-3 ps-3 fw-bold text-dark">LOG-002</td>
                    <td class="py-3 fw-semibold text-secondary">Dinas Kesehatan Provinsi Lampung</td>
                    <td class="py-3">
                        <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #FFF3CD; color: #856404; font-size: 12px;">Antrean</span>
                    </td>
                    <td class="py-3 text-center">
                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 13px;">
                                <i class="fas fa-file-lines"></i> Cetak PDF
                            </a>
                            <a href="#" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 13px; background-color: #2F6FED; border-color: #2F6FED;">
                                <i class="fas fa-paper-plane"></i> Kirim SMTP
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection