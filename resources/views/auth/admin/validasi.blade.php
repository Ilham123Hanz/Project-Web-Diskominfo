@extends('layouts.admin_layout')
@section('title', 'Validasi Dokumen Masuk')
@section('page_title', 'Validasi & Validasi Berkas Lapangan')

@section('content')
<div class="card card-gov border-0 shadow-sm overflow-hidden">
    <div class="card-gov-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-table me-2 text-warning"></i>Daftar Laporan Masuk dari Petugas Lapangan</span>
        <form action="{{ route('admin.validasi') }}" method="GET" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Instansi...">
            <button type="submit" class="btn btn-dark btn-sm px-3">Filter</button>
        </form>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover table-gov align-middle mb-0">
            <thead>
                <tr>
                    <th>Folder & Waktu</th>
                    <th>Instansi & Sub Isu</th>
                    <th>Dokumen</th>
                    <th>Status Validasi</th>
                    <th class="text-center" style="width: 180px;">Tindakan Admin</th>
                </tr>
            </thead>
            <tbody>
                @forelse($patrols as $p)
                <tr>
                    <td>
                        <span class="badge bg-secondary font-monospace p-2">[{{ $p->main_menu }}]</span><br>
                        <small class="text-muted d-block mt-1"><i class="far fa-clock me-1"></i>{{ $p->created_at->format('d/m/Y H:i') }}</small>
                    </td>
                    <td>
                        <strong class="text-dark">{{ $p->agency_name }}</strong><br>
                        <span class="text-muted small">{{ $p->category }}</span>
                        @if($p->admin_correction)
                            <div class="mt-2 text-danger small bg-danger bg-opacity-10 p-2 rounded border border-danger border-opacity-25">
                                <strong>Catatan Koreksi Anda:</strong> {{ $p->admin_correction }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($p->file_evidence)
                            <a href="{{ asset('storage/bukti_files/' . $p->file_evidence) }}" target="_blank" class="btn btn-sm btn-outline-primary py-1 px-2 fw-bold"><i class="fas fa-eye me-1"></i> PRATINJAU</a>
                        @else
                            <span class="text-danger small fw-bold">Tidak Ada Berkas</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge {{ $p->status == 'Verified' ? 'bg-success' : ($p->status == 'Perlu Perbaikan' ? 'bg-danger' : 'bg-warning text-dark') }} p-2">
                            {{ $p->status }}
                        </span>
                    </td>
                    <td class="text-center">
                        <div class="d-flex gap-1 justify-content-center">
                            <form action="{{ route('admin.validasi.update', $p->id) }}" method="POST" onsubmit="return confirm('Setujui Laporan Ini?')">
                                @csrf
                                <input type="hidden" name="status" value="Verified">
                                <button type="submit" class="btn btn-success btn-sm" title="Approve"><i class="fas fa-check-circle"></i></button>
                            </form>
                            
                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $p->id }}" title="Minta Perbaikan">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <div class="modal fade" id="rejectModal{{ $p->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.validasi.update', $p->id) }}" method="POST" class="modal-content">
                            @csrf
                            <input type="hidden" name="status" value="Perlu Perbaikan">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold text-dark">Koreksi Log Transmisi - {{ $p->agency_name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <label class="form-label small fw-bold mb-1">Catatan/Alasan Penolakan Berkas Masuk</label>
                                <textarea name="admin_correction" class="form-control rounded shadow-none" rows="4" placeholder="Contoh: Lampiran bukti buram atau salah folder..." required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-danger btn-sm fw-bold">Kirim Koreksi</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">Tidak ada log laporan masuk yang memerlukan validasi tindakan saat ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection