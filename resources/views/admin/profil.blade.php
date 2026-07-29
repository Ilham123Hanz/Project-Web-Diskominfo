@extends('admin.admin-layout')

@section('title', 'Profil Admin')

@section('page_heading', 'Profil Admin')

@section('breadcrumb')
<nav aria-label="breadcrumb" class="mt-1">
    <ol class="breadcrumb mb-0" style="font-size: 13px; --bs-breadcrumb-divider: '>';">
        <li class="breadcrumb-item">
            <a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Home</a>
        </li>
        <li class="breadcrumb-item text-muted">Pengaturan</li>
        <li class="breadcrumb-item active text-dark fw-bold" aria-current="page" style="color: #000000 !important;">Profil Admin</li>
    </ol>
</nav>
@endsection

@section('content')

{{-- Error Validation Alert --}}
@if($errors->any())
    <div class="alert alert-danger border-0 rounded-4 shadow-sm mb-4 alert-dismissible fade show" role="alert" style="font-size: 13px;">
        <i class="fas fa-exclamation-circle me-2"></i> Terdapat kesalahan pada pengisian form:
        <ul class="mb-0 mt-1 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<form action="{{ route('admin.profil.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="row g-4">
        {{-- Kartu Kiri: Ringkasan Akun --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 text-center mb-4">
                <div class="d-flex justify-content-center mb-3">
                    <div class="rounded-circle bg-dark text-white d-flex align-items-center justify-content-center fw-bold fs-3" style="width: 80px; height: 80px;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted small mb-3">
                    <i class="fas fa-envelope me-1"></i> {{ auth()->user()->email }}
                </p>
                <div>
                    <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2 rounded-pill text-uppercase" style="font-size: 11px;">
                        <i class="fas fa-shield-alt me-1"></i> ROLE: {{ auth()->user()->role ?? 'ADMINISTRATOR' }}
                    </span>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h6 class="fw-bold text-dark mb-2 d-flex align-items-center gap-2">
                    <i class="fas fa-user-shield text-dark"></i> Akses Administrator
                </h6>
                <p class="text-muted small mb-0" style="font-size: 12px; line-height: 1.5;">
                    Pastikan data profil Anda selalu mutakhir. Alamat email yang terdaftar digunakan untuk notifikasi sistem krusial dan laporan insiden keamanan. Jaga kerahasiaan password Anda.
                </p>
            </div>
        </div>

        {{-- Kartu Kanan: Form Edit Data --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4">
                <h5 class="fw-bold text-dark mb-1">Informasi Akun</h5>
                <p class="text-muted small mb-4" style="font-size: 13px;">Perbarui detail personal, kontak operasional, dan pengaturan keamanan Anda.</p>

                <div class="row g-3">
                    {{-- Nama Lengkap --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">NAMA LENGKAP</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', auth()->user()->name) }}" style="font-size: 13px;" required>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">ALAMAT EMAIL INSTANSI</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', auth()->user()->email) }}" style="font-size: 13px;" required>
                    </div>

                    {{-- WhatsApp / No HP --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-secondary">NOMOR WHATSAPP / KONTAK OPERASIONAL</label>
                        <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', auth()->user()->no_hp ?? auth()->user()->whatsapp) }}" placeholder="081234567890" style="font-size: 13px;">
                    </div>

                    {{-- Alamat --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold small text-secondary">ALAMAT DOMISILI</label>
                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap..." style="font-size: 13px;">{{ old('alamat', auth()->user()->alamat) }}</textarea>
                    </div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <h6 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2" style="font-size: 13px;">
                    <i class="fas fa-lock text-dark"></i> UBAH PASSWORD <span class="text-muted fw-normal">(KOSONGKAN JIKA TIDAK DIGANTI)</span>
                </h6>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">PASSWORD BARU</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" style="font-size: 13px;" autocomplete="new-password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small text-secondary">KONFIRMASI PASSWORD BARU</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru" style="font-size: 13px;" autocomplete="new-password">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4 pt-2">
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm d-inline-flex align-items-center gap-2" style="font-size: 13px; background-color: #0F172A; border-color: #0F172A;">
                        <i class="fas fa-save"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection