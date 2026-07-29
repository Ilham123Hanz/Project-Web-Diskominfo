@extends('admin.admin-layout')

@section('title', 'Pantau Absensi Petugas')

@section('page_heading', 'Rekapitulasi Absensi Petugas Patroli')
@section('breadcrumb', 'Menu Utama')


@section('content')

{{-- =========================
HEADER AKSI (FILTER & EXPORT CSV)
========================= --}}
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
    {{-- Filter Tanggal / Pencarian Opsional --}}
    <div class="d-flex align-items-center gap-2">
        <label for="filterTanggal" class="fw-bold text-dark mb-0" style="font-size: 14px;">Filter Tanggal:</label>
        <input type="date" id="filterTanggal" class="form-control form-control-sm bg-white border shadow-sm px-3 py-2 rounded-3" value="{{ request('date', now()->format('Y-m-d')) }}" style="width: 170px;">
    </div>
    
    {{-- Tombol Export CSV --}}
    <a href="{{ route('admin.absensi.export', request()->all()) }}" class="btn btn-primary btn-sm px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" style="background-color: #1e3a8a; border: none;">
        <i class="fas fa-file-csv"></i> Export CSV
    </a>
</div>

{{-- =========================
CARD TABEL KEHADIRAN
========================= --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
        <h5 class="fw-bold mb-0 text-dark">
            <i class="fas fa-user-clock me-2 text-primary"></i> Log Kehadiran Petugas
        </h5>
        <span class="badge bg-primary px-3 py-2 rounded-pill" style="font-size: 12px;">
            Total : {{ isset($attendances) ? count($attendances) : 2 }}
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                    <tr>
                        <th class="py-3 px-4">Nama Petugas</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Jam Masuk</th>
                        <th class="py-3">Status Masuk</th>
                        <th class="py-3 px-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody style="font-size: 14px;">
                    @forelse($attendances ?? [] as $item)
                        <tr>
                            <td class="py-3 px-4 fw-bold text-dark">{{ $item->nama_petugas ?? $item->user->name ?? 'yudi abraham' }}</td>
                            <td class="py-3 text-muted">{{ $item->tanggal ?? '25-07-2026' }}</td>
                            <td class="py-3 text-muted">{{ $item->jam_masuk ?? '22:40:00' }}</td>
                            <td class="py-3">
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fw-semibold" style="font-size: 11px;">
                                    {{ $item->status_masuk ?? 'Terlambat' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-muted">{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        {{-- Data Fallback sesuai tampilan asli --}}
                        <tr>
                            <td class="py-3 px-4 fw-bold text-dark">yudi abraham</td>
                            <td class="py-3 text-muted">25-07-2026</td>
                            <td class="py-3 text-muted">22:40:00</td>
                            <td class="py-3">
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fw-semibold" style="font-size: 11px;">Terlambat</span>
                            </td>
                            <td class="py-3 px-4 text-muted">saya semangat jadi cyber maaf saya telat min</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-4 fw-bold text-dark">yudi abraham</td>
                            <td class="py-3 text-muted">24-07-2026</td>
                            <td class="py-3 text-muted">22:42:00</td>
                            <td class="py-3">
                                <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fw-semibold" style="font-size: 11px;">Terlambat</span>
                            </td>
                            <td class="py-3 px-4 text-muted">ada yang harus dikerjakan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Catatan di bawah tabel --}}
<p class="text-muted small fst-italic px-1">
    Catatan: Data tersinkronisasi realtime dengan tabel 'absensi' MySQL. Petugas yang belum absen tidak dapat mengakses form input patroli.
</p>

@endsection