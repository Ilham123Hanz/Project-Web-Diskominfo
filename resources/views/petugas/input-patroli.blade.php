<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Form Patroli - SIP-O-SIBER</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; color: #333; font-family: 'Segoe UI', sans-serif; }
        .card-custom { background: #fff; border-radius: 12px; border: none; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px; }
        .step-indicator { display: flex; justify-content: space-between; align-items: center; background: #fafafa; padding: 15px 30px; border-radius: 8px; border: 1px solid #e2e8f0; }
        .step-item { display: flex; align-items: center; gap: 10px; color: #a0aec0; font-size: 0.9rem; }
        .step-item.active { color: #0d6efd; font-weight: 600; }
        .step-item.completed { color: #198754; }
        .step-icon { width: 32px; height: 32px; border-radius: 50%; background: #edf2f7; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; }
        .step-item.active .step-icon { background: #0d6efd; color: #fff; }
        .step-item.completed .step-icon { background: #198754; color: #fff; }
        .upload-area { border: 2px dashed #cbd5e1; border-radius: 8px; padding: 40px; text-align: center; background: #f8fafc; cursor: pointer; transition: all 0.2s; }
        .upload-area:hover { border-color: #0d6efd; background: #f0f7ff; }
    </style>
</head>
<body>
<div class="container my-5" style="max-width: 900px;">
    <div class="card card-custom">
        <div class="text-center mb-2">
            <span class="badge bg-light text-secondary border px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem;"><i class="fas fa-clipboard-list me-1"></i> Formulir Entri Log</span>
        </div>
        <h3 class="fw-bold text-center text-dark mb-1">Input Form Patroli</h3>
        <p class="text-muted text-center small mb-4">Catat temuan patroli siber Anda. Berkas bukti akan diverifikasi secara ketat oleh sistem sebelum log disimpan.</p>

        <div class="step-indicator mb-4">
            <div class="step-item completed">
                <div class="step-icon"><i class="fas fa-check"></i></div>
                <div><small class="d-block text-muted" style="font-size: 0.7rem;">Langkah 1</small>Kategori</div>
            </div>
            <div class="step-item active">
                <div class="step-icon">2</div>
                <div><small class="d-block text-muted" style="font-size: 0.7rem;">Langkah 2</small>Detail Temuan</div>
            </div>
            <div class="step-item">
                <div class="step-icon">3</div>
                <div><small class="d-block text-muted" style="font-size: 0.7rem;">Langkah 3</small>Bukti & Simpan</div>
            </div>
        </div>

        <form action="{{ route('petugas.log.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary">OPD / Instansi Sasaran</label>
                    <select name="opd_target" class="form-select bg-light" required>
                        <option value="">Pilih OPD Sasaran...</option>
                        <option value="BAPPEDA Lampung">BAPPEDA Lampung</option>
                        <option value="Dinas Kesehatan">Dinas Kesehatan</option>
                        <option value="Dinas Pendidikan">Dinas Pendidikan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-secondary">Kategori Insiden Siber</label>
                    <select name="incident_category" class="form-select bg-light" required>
                        <option value="">Pilih Kategori...</option>
                        <option value="Web Defacement">Web Defacement</option>
                        <option value="Judi Online (Judol)">Judi Online (Judol)</option>
                        <option value="Malware Injection">Malware Injection</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold text-secondary">URL / Target Sasaran</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted"><i class="fas fa-link"></i></span>
                        <input type="url" name="url_target" class="form-control bg-light" placeholder="https://contoh-sasaran.go.id/path" required>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold text-secondary">Kronologi Temuan & Dampak</label>
                    <textarea name="chronology" class="form-control bg-light" rows="4" placeholder="Uraikan kronologi temuan secara rinci: waktu, indikator, dampak, dan langkah awal yang telah diambil..." required></textarea>
                </div>
                
                <div class="col-12 mt-4">
                    <label class="form-label small fw-bold text-secondary">Unggah Berkas Foto Bukti</label>
                    <div class="upload-area" onclick="document.getElementById('file-bukti').click()">
                        <i class="fas fa-cloud-upload-alt text-primary fs-1 mb-2"></i>
                        <p class="mb-1 fw-semibold">Seret & lepas berkas di sini, atau <span class="text-primary text-decoration-underline">telusuri dari perangkat</span></p>
                        <small class="text-muted d-block">Sistem memvalidasi ukuran (Maksimal 2MB) dan keamanan berkas (Magic Bytes verification).</small>
                        <input type="file" id="file-bukti" name="evidence" class="d-none" accept="image/*" required>
                    </div>
                    
                    <div class="alert alert-danger border-0 small fw-bold p-3 mt-3 text-danger bg-danger-subtle rounded-3 d-none" id="error-file-alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> Error: File Ditolak / Sistem Error. Format tidak sesuai.<br>
                        <span class="fw-normal text-muted" style="font-size: 0.8rem;">Verifikasi Magic Bytes gagal atau ukuran melebihi 2MB. Pastikan berkas berupa gambar (JPG/PNG) yang valid dan tidak dimodifikasi.</span>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                <a href="{{ route('petugas.dashboard') }}" class="btn btn-light border px-4"><i class="fas fa-arrow-left me-1"></i> Kembali</a>
                <button type="submit" class="btn btn-primary px-4">Simpan Log <i class="fas fa-save ms-1"></i></button>
            </div>
        </form>
    </div>
</div>
</body>
</html>