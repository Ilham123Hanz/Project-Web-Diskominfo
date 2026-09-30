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
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
            <thead class="table-light text-uppercase text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                <tr>
                    <th class="py-3 ps-3">ID Laporan</th>
                    <th class="py-3">OPD Tujuan</th>
                    <th class="py-3">Status</th>
                    <th class="py-3 text-center" style="width: 220px;">Aksi Eksekusi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patrols ?? [] as $log)
                <tr>
                    <td class="py-3 ps-3 fw-bold text-dark">{{ $log->log_code ?? 'LOG-00' . $log->id }}</td>
                    <td class="py-3 fw-semibold text-secondary">{{ $log->opd_sasaran }}</td>
                    <td class="py-3">
                        @if(strtolower($log->status) === 'pending')
                            <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #FFF3CD; color: #856404; font-size: 12px;">Antrean</span>
                        @elseif(in_array($log->status, ['Verified', 'Approved', 'Disetujui Admin']))
                            <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #D1F8E8; color: #0F5132; font-size: 12px;">Terkirim</span>
                        @elseif(in_array($log->status, ['Perlu Perbaikan', 'Revision', 'Revisi']))
                            <span class="badge rounded-pill px-3 py-2 fw-semibold" style="background-color: #FFF3CD; color: #856404; font-size: 12px;">Perlu Revisi</span>
                        @else
                            <span class="badge rounded-pill px-3 py-2 fw-semibold" style="font-size: 12px;">{{ $log->status }}</span>
                        @endif
                    </td>
                    <td class="py-3 text-center">
                        <div class="d-flex justify-content-center gap-2">
                            {{-- Tombol Cetak PDF --}}
                            <a href="{{ route('admin.laporan.patroli.pdf', $log->id) }}" target="_blank" class="btn btn-outline-secondary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-1" style="font-size: 13px;">
                                <i class="fas fa-file-lines"></i> Cetak PDF
                            </a>
                            <form action="{{ route('admin.patrol.distribute', $log->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Kirim notifikasi email untuk laporan ini?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 13px; background-color: #2F6FED; border-color: #2F6FED;">
                                    <i class="fas fa-paper-plane"></i> Kirim SMTP
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-2x mb-2 d-block text-secondary opacity-50"></i>
                        Belum ada data laporan patroli dari petugas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection