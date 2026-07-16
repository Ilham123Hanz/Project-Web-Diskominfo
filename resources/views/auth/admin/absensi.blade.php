@extends('layouts.admin_layout')

@section('title', 'Log Rekap Absensi Petugas')
@section('page_title', 'Log Absensi Harian Seluruh Petugas')

@section('content')
<div class="card card-gov border-0 shadow-sm overflow-hidden">
    <div class="card-gov-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-history text-muted me-2"></i>Daftar Kehadiran Personel Masuk</span>
        <span class="badge bg-secondary font-monospace">Total Data: {{ count($attendances) }}</span>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-gov align-middle mb-0">
            <thead>
                <tr>
                    <th><i class="fas fa-user me-2"></i>Nama Petugas</th>
                    <th><i class="fas fa-calendar-alt me-2"></i>Tanggal Tugas</th>
                    <th><i class="fas fa-clock me-2"></i>Jam Masuk Absen</th>
                    <th><i class="fas fa-info-circle me-2"></i>Keterangan Shift / Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                <tr>
                    <td>
                        <span class="fw-bold text-dark" style="font-size: 1.02rem;">{{ $attendance->user->name }}</span>
                    </td>
                    <td>
                        <span class="text-secondary fw-semibold">
                            {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-success px-3 py-2 fw-bold" style="font-size: 0.85rem;">
                            <i class="far fa-clock me-1"></i> {{ $attendance->time_in ?? $attendance->manual_time }} WIB
                        </span>
                    </td>
                    <td>
                        <span class="text-dark fw-medium">{{ $attendance->shift ?? $attendance->status }}</span>
                        @if($attendance->notes)
                            <small class="d-block text-muted mt-1 font-monospace">Ket: {{ $attendance->notes }}</small>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="fas fa-folder-open fa-2x mb-3 text-secondary d-block"></i>
                        Belum ada data log presensi petugas yang terekam masuk ke dalam sistem harian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection