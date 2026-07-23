@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <a href="{{ route('admin.archives.folders') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Kembali ke Folder Virtual
            </a>
            <h3 class="fw-bold mb-0">Isi Folder: {{ $folderTitle }}</h3>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID Log</th>
                            <th>Petugas</th>
                            <th>OPD Sasaran</th>
                            <th>Kategori Insiden</th>
                            <th>Tanggal Buat</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($patrols as $patrol)
                            <tr>
                                <td><span class="badge bg-secondary">{{ $patrol->id_log }}</span></td>
                                <td>{{ $patrol->user->name ?? 'System' }}</td>
                                <td>{{ $patrol->opd_sasaran }}</td>
                                <td>{{ $patrol->kategori_insiden }}</td>
                                <td>{{ $patrol->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ $patrol->status == 'Verified' ? 'success' : ($patrol->status == 'Perlu Perbaikan' ? 'warning' : 'danger') }}">
                                        {{ $patrol->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.patrol.show', $patrol->id) }}" class="btn btn-sm btn-info text-white">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Tidak ada dokumen arsip pada folder ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($patrols->hasPages())
            <div class="card-footer bg-white">
                {{ $patrols->links() }}
            </div>
        @endif
    </div>
</div>
@endsection