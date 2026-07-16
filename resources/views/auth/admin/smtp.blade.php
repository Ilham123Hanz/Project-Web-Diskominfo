@extends('layouts.admin_layout')
@section('title', 'Distribusi Laporan (SMTP Mailer)')
@section('page_heading', 'Distribusi Laporan (SMTP Mailer)')
@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Manajemen Log</li>
    <li class="breadcrumb-item active">Distribusi Laporan</li>
@endsection

@section('content')
<!-- Notifikasi Alert System -->
<div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-medium p-3 mb-4 rounded d-flex align-items-center gap-2 small shadow-sm" role="alert">
    <i class="fas fa-check-circle fa-lg"></i>
    <div>Notifikasi Sistem: Laporan <strong>LOG-001</strong> berhasil dikirim otomatis ke <strong>bappeda@lampungprov.go.id</strong> via SMTP Mailer.</div>
</div>

<div class="card card-custom p-4">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light text-muted small text-uppercase">
                <tr>
                    <th style="width: 120px;">ID LAPORAN</th>
                    <th>OPD TUJUAN</th>
                    <th style="width: 140px;">STATUS</th>
                    <th style="width: 220px;" class="text-center">AKSI EKSEKUSI</th>
                </tr>
            </thead>
            <tbody class="small fw-bold">
                <tr>
                    <td class="text-dark">LOG-001</td>
                    <td class="text-muted fw-medium">BAPPEDA Provinsi Lampung</td>
                    <td><span class="badge bg-success bg-opacity-10 text-success px-2 py-1 rounded">Terkirim</span></td>
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-light btn-sm border text-muted"><i class="fas fa-file-pdf me-1 text-danger"></i> Cetak PDF</button>
                            <button class="btn btn-primary btn-sm px-3"><i class="fas fa-paper-plane me-1"></i> Kirim SMTP</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="text-dark">LOG-002</td>
                    <td class="text-muted fw-medium">Dinas Kesehatan Provinsi Lampung</td>
                    <td><span class="badge bg-warning bg-opacity-10 text-warning text-dark px-2 py-1 rounded">Antrean</span></td>
                    <td class="text-center">
                        <div class="d-flex gap-2 justify-content-center">
                            <button class="btn btn-light btn-sm border text-muted"><i class="fas fa-file-pdf me-1 text-danger"></i> Cetak PDF</button>
                            <button class="btn btn-primary btn-sm px-3"><i class="fas fa-paper-plane me-1"></i> Kirim SMTP</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection