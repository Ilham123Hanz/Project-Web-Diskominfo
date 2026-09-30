@extends('admin.admin-layout')

@section('page_heading', 'Rekapitulasi Absensi Petugas Patroli')

{{-- Sesuaikan Breadcrumb agar sesuai --}}
@section('breadcrumb')
Home > Menu Utama > <span class="text-dark">Pantau Absensi</span>
@endsection

@section('content')
<div class="container-fluid px-0">
    {{-- Form Filter Harian & Bulanan --}}
    <form action="{{ route('admin.attendance.index') }}" method="GET" id="formFilter" class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- Filter Harian -->
            <div class="d-flex align-items-center gap-2">
                <label for="filterTanggal" class="fw-bold text-dark mb-0" style="font-size: 14px;">Filter Tanggal:</label>
                <input type="date" name="date" id="filterTanggal" class="form-control form-control-sm bg-white border shadow-sm px-2 py-1" value="{{ request('date') }}" style="width: 160px;" onchange="document.getElementById('formFilter').submit()">
            </div>

            <span class="text-muted mx-1">Atau</span>

            <!-- Filter Bulanan -->
            <div class="d-flex align-items-center gap-2">
                <label for="filterBulan" class="fw-bold text-dark mb-0" style="font-size: 14px;">Bulan:</label>
                <input type="month" name="month" id="filterBulan" class="form-control form-control-sm bg-white border shadow-sm px-2 py-1" value="{{ request('month') }}" style="width: 160px;" onchange="document.getElementById('formFilter').submit()">
            </div>
            
            @if(request('date') || request('month'))
                <a href="{{ route('admin.attendance.index') }}" class="btn btn-outline-secondary btn-sm px-2 py-1" style="font-size: 12px;">Reset Filter</a>
            @endif
        </div>
        
        {{-- Tombol Export CSV Dinamis --}}
        <div class="d-flex gap-2">
            <a href="#" id="btnExportCsv" class="btn btn-primary btn-sm px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2 text-decoration-none" style="background-color: #1e3a8a; border: none;">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
        </div>
    </form>

    {{-- Tabel Data Presensi dengan Garis Pembatas (Bordered & Striped) --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle mb-0" style="border-color: #e9ecef;">
                    <thead class="table-light text-dark">
                        <tr>
                            <th class="py-3 px-3 text-center" style="width: 5%;">NO</th>
                            <th class="py-3 px-3">TANGGAL</th>
                            <th class="py-3 px-3">NAMA PETUGAS</th>
                            <th class="py-3 px-3">JAM MASUK</th>
                            <th class="py-3 px-3">JAM PULANG</th>
                            <th class="py-3 px-3 text-center">STATUS MASUK</th>
                            <th class="py-3 px-3 text-center">STATUS PULANG</th>
                            <th class="py-3 px-3 text-center">STATUS KEHADIRAN</th>
                            <th class="py-3 px-3">DURASI KERJA</th>
                            <th class="py-3 px-3">CATATAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $item)
                        <tr>
                            <td class="px-3 text-center">{{ $attendances->firstItem() + $index }}</td>
                            <td class="px-3">
                                <strong class="text-dark d-block" style="font-size: 14px;">{{ $item->tanggal_presensi ? \Carbon\Carbon::parse($item->tanggal_presensi)->translatedFormat('d M Y') : '-' }}</strong>
                                <small class="text-muted" style="font-size: 11px;">({{ $item->tanggal_presensi ? \Carbon\Carbon::parse($item->tanggal_presensi)->isoFormat('dddd') : '-' }})</small>
                            </td>
                            <td class="px-3 fw-semibold">{{ $item->user->name ?? '-' }}</td>
                            <td class="px-3">{{ $item->jam_masuk ? $item->jam_masuk . ' WIB' : '-' }}</td>
                            <td class="px-3">{{ $item->jam_pulang ? $item->jam_pulang . ' WIB' : 'Belum Pulang' }}</td>
                            <td class="px-3 text-center">
                                @if($item->status_masuk === 'Terlambat')
                                    <span class="badge rounded-pill bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-clock me-1 text-warning"></i> Terlambat
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-check-circle me-1"></i> Tepat Waktu
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 text-center">
                                @if($item->status_pulang === 'Pulang Cepat')
                                    <span class="badge rounded-pill bg-warning bg-opacity-15 text-dark border border-warning border-opacity-25 px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-sign-out-alt me-1"></i> Pulang Cepat
                                    </span>
                                @elseif($item->status_pulang === 'Selesai')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-check-circle me-1"></i> Selesai
                                    </span>
                                @elseif(empty($item->jam_pulang))
                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        Belum Pulang
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2 fw-semibold" style="font-size: 11px;">{{ $item->status_pulang }}</span>
                                @endif
                            </td>
                            <td class="px-3 text-center">
                                @if($item->status_kehadiran === 'Hadir')
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-check-circle me-1"></i> Hadir
                                    </span>
                                @elseif($item->status_kehadiran === 'Izin')
                                    <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-file-alt me-1"></i> Izin
                                    </span>
                                @elseif($item->status_kehadiran === 'Sakit')
                                    <span class="badge rounded-pill bg-purple bg-opacity-10 text-purple px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-thermometer-half me-1"></i> Sakit
                                    </span>
                                @elseif($item->status_kehadiran === 'Dinas Luar')
                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning px-3 py-2 fw-semibold" style="font-size: 11px;">
                                        <i class="fas fa-briefcase me-1"></i> Dinas Luar
                                    </span>
                                @else
                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2 fw-semibold" style="font-size: 11px;">{{ $item->status_kehadiran }}</span>
                                @endif
                            </td>
                            <td class="px-3">
                                <span class="badge bg-dark font-monospace fw-normal">{{ $item->durasi_kerja_formatted ?? ($item->durasi_kerja ? ($item->durasi_kerja . ' Menit') : '0 Menit') }}</span>
                            </td>
                            <td class="px-3">
                                <small class="text-muted" style="max-width: 200px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $item->catatan_masuk ?? $item->catatan_pulang ?? '-' }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">Belum ada data presensi untuk periode ini.</                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Catatan di Bawah Tabel --}}
    <div class="mt-3 text-muted" style="font-style: italic; font-size: 13px;">
        Catatan: Data tersinkronisasi realtime dengan tabel 'presensi' MySQL. Petugas yang belum absen tidak dapat mengakses form input patroli.
    </div>

    <div class="mt-3">
        {{ $attendances->links() }}
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterTanggal = document.getElementById('filterTanggal');
        const filterBulan = document.getElementById('filterBulan');
        const btnExportCsv = document.getElementById('btnExportCsv');

        function updateExportUrl() {
            let url = "{{ route('admin.attendance.export') }}?";
            if (filterBulan.value) {
                url += "month=" + filterBulan.value;
            } else if (filterTanggal.value) {
                url += "date=" + filterTanggal.value;
            }
            btnExportCsv.href = url;
        }

        updateExportUrl();

        filterTanggal.addEventListener('change', function() {
            if(this.value) filterBulan.value = '';
            updateExportUrl();
        });

        filterBulan.addEventListener('change', function() {
            if(this.value) filterTanggal.value = '';
            updateExportUrl();
        });
    });
</script>
@endpush
@endsection