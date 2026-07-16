@extends('layouts.admin_layout')
@section('title', 'Pusat Manajemen Master Data')
@section('page_heading', 'Pusat Manajemen Master Data')
@section('breadcrumb')
    <li class="breadcrumb-item text-muted">Pengaturan</li>
    <li class="breadcrumb-item active">Master Data OPD</li>
@endsection

@section('content')
<!-- Tab Link Header Minimalis -->
<ul class="nav nav-tabs mb-4 border-bottom-0" id="opdTab" role="tablist">
    <li class="nav-item">
        <button class="nav-link active border-0 px-3 py-2 fw-semibold" style="border-radius: 6px;" id="email-tab" data-bs-toggle="tab" type="button"><i class="fas fa-envelope text-primary me-2"></i>Email Kontak OPD</button>
    </li>
    <li class="nav-item">
        <button class="nav-link border-0 text-muted px-3 py-2" disabled><i class="fas fa-share-alt me-2"></i>Media Sosial</button>
    </li>
    <li class="nav-item">
        <button class="nav-link border-0 text-muted px-3 py-2" disabled><i class="fas fa-laptop me-2"></i>Aplikasi Pemprov</button>
    </li>
</ul>

<div class="card card-custom p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold text-dark mb-0" style="font-size: 1.05rem;">Direktori Email Resmi OPD</h5>
        <!-- Trigger Modal -->
        <button class="btn btn-primary btn-sm fw-bold px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalTambahOpd">
            <i class="fas fa-plus me-2"></i>Tambah Data Email
        </button>
    </div>

    <div class="table-responsive">
        <table class="table align-middle">
            <thead class="table-light small text-muted text-uppercase">
                <tr>
                    <th style="width: 60px;">NO</th>
                    <th>NAMA OPD / INSTANSI</th>
                    <th>ALAMAT EMAIL RESMI</th>
                    <th style="width: 140px;" class="text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="small">
                <tr>
                    <td class="text-muted">1</td>
                    <td class="fw-bold text-dark">Dinas Komunikasi, Informatika dan Statistik</td>
                    <td class="font-monospace text-secondary">diskominfotik@lampungprov.go.id</td>
                    <td class="text-center">
                        <a href="#" class="text-primary text-decoration-none fw-semibold me-2">Edit</a>
                        <span class="text-muted">|</span>
                        <a href="#" class="text-danger text-decoration-none fw-semibold ms-2">Hapus</a>
                    </td>
                </tr>
                <tr>
                    <td class="text-muted">2</td>
                    <td class="fw-bold text-dark">Dinas Kesehatan Provinsi Lampung</td>
                    <td class="font-monospace text-secondary">dinkes@lampungprov.go.id</td>
                    <td class="text-center">
                        <a href="#" class="text-primary text-decoration-none fw-semibold me-2">Edit</a>
                        <span class="text-muted">|</span>
                        <a href="#" class="text-danger text-decoration-none fw-semibold ms-2">Hapus</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== MODAL TEMPLATE DI ATAS (POPUP DATA OPD) ==================== -->
<div class="modal fade" id="modalTambahOpd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px;">
            <div class="modal-header px-4 pt-4 border-0 justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark" style="font-size: 1.2rem;">Form Tambah Data Email OPD</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4">
                <!-- Status Bar Koneksi Berhasil -->
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success d-flex align-items-center gap-2 small py-2 mb-3 fw-medium">
                    <i class="fas fa-check"></i> Koneksi database berhasil. Siap menyimpan data.
                </div>

                <form action="{{ route('admin.master_opd.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Pilih Instansi / OPD <span class="text-danger">*</span></label>
                        <select class="form-select border rounded" name="opd_id" required>
                            <option selected disabled>Pilih Instansi Pemerintahan...</option>
                            <option value="1">Dinas Komunikasi, Informatika dan Statistik</option>
                            <option value="2">Dinas Kesehatan Provinsi Lampung</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">Alamat Email Resmi <span class="text-danger">*</span></label>
                        <input type="email" class="form-control border rounded" name="email" placeholder="contoh@lampungprov.go.id" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark mb-1">Keterangan / PIC</label>
                        <textarea class="form-control border rounded" name="pic_note" rows="3" placeholder="Opsional: Nama PIC atau Nomor Telepon..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top pt-3 pb-2">
                        <button type="button" class="btn text-danger fw-semibold bg-transparent border-0 p-0 shadow-none">Hapus Data</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light border btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold"><i class="fas fa-save me-2"></i>Simpan Data 💾</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection