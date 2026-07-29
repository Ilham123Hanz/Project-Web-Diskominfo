@extends('admin.admin-layout')

{{-- Ubah Judul Halaman Utama di Layout --}}
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
                            <th class="py-3 px-3 text-center" style="width: 60px;">NO</th>
                            <th class="py-3 px-3">NAMA PETUGAS PATROLI</th>
                            <th class="py-3 px-3">SHIFT PENUGASAN</th>
                            <th class="py-3 px-3">TANGGAL PRESENSI</th>
                            <th class="py-3 px-3">WAKTU KEHADIRAN</th>
                            <th class="py-3 px-3 text-center">STATUS KEHADIRAN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $index => $item)
                        <tr>
                            <td class="px-3 text-center">{{ $attendances->firstItem() + $index }}</td>
                            <td class="px-3 fw-semibold">{{ $item->user->name ?? '-' }}</td>
                            <td class="px-3">{{ $item->shift ?? 'Shift Pagi' }}</td>
                            <td class="px-3">{{ $item->tanggal_presensi ? \Carbon\Carbon::parse($item->tanggal_presensi)->format('d-m-Y') : '-' }}</td>
                            <td class="px-3">{{ $item->jam_masuk ? $item->jam_masuk . ' WIB' : '-' }}</td>
                            <td class="px-3 text-center">
                                <span class="badge bg-success-subtle text-success px-2 py-1 rounded-pill">
                                    {{ $item->status_kehadiran ?? 'Hadir' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data presensi untuk periode ini.</td>
                        </tr>
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