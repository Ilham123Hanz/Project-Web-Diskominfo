@extends('petugas.petugas-layout.petugas-layout')

@section('title', 'Input Form Patroli Siber - SIP-O-SIBER')

@push('styles')
<style>
    /* Tipografi Base System & Keterbacaan Visual */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #1e293b;
    }

    .main-container-patrol {
        max-width: 920px;
        margin: 24px auto;
    }

    .card-custom {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.01);
        padding: 32px;
    }
    
    /* Wizard Step Indicator */
    .step-indicator {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        padding: 18px 24px;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 32px;
        position: relative;
    }
    .step-item {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #64748b;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        z-index: 2;
    }
    .step-item.active {
        color: #0f3057;
        font-weight: 700;
    }
    .step-item.completed {
        color: #059669;
        font-weight: 600;
    }
    .step-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #e2e8f0;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .step-item.active .step-icon {
        background: #0f3057;
        color: #ffffff;
        box-shadow: 0 0 0 4px rgba(15, 48, 87, 0.15);
    }
    .step-item.completed .step-icon {
        background: #10b981;
        color: #ffffff;
    }

    /* Container Dynamic File Upload Area */
    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        padding: 32px 20px;
        text-align: center;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.25s ease-in-out;
    }
    .upload-area:hover, .upload-area.dragover {
        border-color: #0f3057;
        background: #f1f5f9;
    }

    /* Form Controls & Inputs Optimization */
    .input-gov {
        font-size: 0.875rem;
        border-color: #cbd5e1;
        padding: 0.55rem 0.75rem;
    }
    .input-gov:focus {
        border-color: #0f3057;
        box-shadow: 0 0 0 0.2rem rgba(15, 48, 87, 0.15);
    }
    label.form-label {
        font-size: 0.825rem;
        letter-spacing: 0.01em;
    }

    /* Preview Container */
    .preview-container {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 14px 18px;
        background-color: #ffffff;
        display: none;
        margin-top: 16px;
    }
    .preview-image {
        max-height: 90px;
        object-fit: contain;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
    }

    /* Step Content Animation Display */
    .wizard-step-content {
        display: none;
    }
    .wizard-step-content.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="main-container-patrol">
        
        <!-- Header Navigasi / Kembali ke Dashboard -->
        <div class="d-flex align-items-center justify-content-between mb-3">
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-2 fw-bold px-3 py-1.5">
                <i class="fas fa-arrow-left me-1.5"></i> Kembali ke Dashboard
            </a>
            <span class="badge bg-dark font-monospace px-3 py-2" style="font-size: 0.75rem;">
                <i class="fas fa-network-wired me-1"></i> IP: {{ request()->ip() }}
            </span>
        </div>

        <!-- Flash Message Alerts -->
        @if(session('success'))
            <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-semibold p-3 mb-4 rounded-3 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle fa-lg me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-semibold p-3 mb-4 rounded-3 shadow-sm alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                <div>{{ $errors->first() }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Card Form Utama -->
        <div class="card card-custom">
            <div class="text-center mb-2">
                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 px-3 py-2 text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                    <i class="fas fa-clipboard-list me-1"></i> Formulir Entri Log SIBER
                </span>
            </div>
            <h3 class="fw-bold text-center mb-1" style="color: #0f3057;">Input Form Patroli Siber</h3>
            <p class="text-muted text-center small mb-4">Catat temuan patroli siber Anda secara sistematis. Berkas bukti akan diverifikasi kelayakannya sebelum disimpan.</p>

            <!-- Multi-Step Indicator -->
            <div class="step-indicator">
                <div class="step-item active" id="step-node-1">
                    <div class="step-icon" id="step-icon-1">1</div>
                    <div>
                        <small class="d-block text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 600;">Langkah 1</small>
                        <span class="fw-bold">Klasifikasi & Drive</span>
                    </div>
                </div>
                <div class="step-item" id="step-node-2">
                    <div class="step-icon" id="step-icon-2">2</div>
                    <div>
                        <small class="d-block text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 600;">Langkah 2</small>
                        <span class="fw-bold">Detail Target & Isu</span>
                    </div>
                </div>
                <div class="step-item" id="step-node-3">
                    <div class="step-icon" id="step-icon-3">3</div>
                    <div>
                        <small class="d-block text-muted" style="font-size: 0.68rem; text-transform: uppercase; font-weight: 600;">Langkah 3</small>
                        <span class="fw-bold">Matriks & Bukti</span>
                    </div>
                </div>
            </div>

            <form action="{{ route('petugas.patrol.store') }}" method="POST" enctype="multipart/form-data" id="patrolForm" class="needs-validation" novalidate>
                @csrf

                <!-- STEP 1: KLASIFIKASI & GOOGLE DRIVE FOLDER -->
                <div class="wizard-step-content active" id="wizard-step-1">
                    <div class="border-bottom pb-2 mb-3">
                        <h6 class="fw-bold mb-0" style="color: #0f3057;"><i class="fas fa-layer-group me-1.5 text-primary"></i> Langkah 1: Pengelompokan Kerja & V-Drive</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">Rumpun Kategori Kerja <span class="text-danger">*</span></label>
                            <select name="rumpun_kategori" id="rumpun_kategori" class="form-select input-gov rounded-2" required>
                                <option value="" disabled selected>-- Pilih Rumpun Kerja --</option>
                                <option value="Patroli Harian" {{ old('rumpun_kategori') == 'Patroli Harian' ? 'selected' : '' }}>Patroli Harian</option>
                                <option value="Advanced Assessment" {{ old('rumpun_kategori') == 'Advanced Assessment' ? 'selected' : '' }}>Advanced Assessment</option>
                            </select>
                            <div class="invalid-feedback small">Rumpun kategori kerja wajib dipilih.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">Kategori Folder Google Drive <span class="text-danger">*</span></label>
                            <select name="main_menu" id="main_menu" class="form-select input-gov rounded-2" required>
                                <option value="" disabled selected>-- Pilih Folder V-Drive Tujuan --</option>
                                <option value="Patroli Siber" {{ old('main_menu') == 'Patroli Siber' ? 'selected' : '' }}>Patroli Siber</option>
                                <option value="Bug Hunter" {{ old('main_menu') == 'Bug Hunter' ? 'selected' : '' }}>Bug Hunter</option>
                                <option value="CTI" {{ old('main_menu') == 'CTI' ? 'selected' : '' }}>CTI</option>
                                <option value="Laporan Insiden" {{ old('main_menu') == 'Laporan Insiden' ? 'selected' : '' }}>Laporan Insiden</option>
                                <option value="Sosial Media" {{ old('main_menu') == 'Sosial Media' ? 'selected' : '' }}>Sosial Media</option>
                                <option value="Vul/Pen Test" {{ old('main_menu') == 'Vul/Pen Test' ? 'selected' : '' }}>Vul/Pen Test</option>
                            </select>
                            <div class="invalid-feedback small">Folder virtual drive tujuan wajib ditentukan.</div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-primary px-4 rounded-2 fw-bold d-inline-flex align-items-center" onclick="nextWizardStep(1, 2)">
                            Lanjut ke Langkah 2 <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 2: DETAIL TARGET SASARAN & ISU -->
                <div class="wizard-step-content" id="wizard-step-2">
                    <div class="border-bottom pb-2 mb-3">
                        <h6 class="fw-bold mb-0" style="color: #0f3057;"><i class="fas fa-university me-1.5 text-primary"></i> Langkah 2: Detail Instansi Target & Temuan</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">OPD / Instansi Sasaran <span class="text-danger">*</span></label>
                            <input type="text" id="opdFilterInput" class="form-control form-control-sm mb-2 rounded-2" placeholder="🔍 Ketik untuk memfilter instansi...">
                            <select name="agency_name" id="agencySelect" class="form-select input-gov rounded-2" required onchange="handleManualToggle('agency')">
                                <option value="" disabled selected>-- Pilih OPD / Instansi --</option>
                                @if(isset($listOPD) && count($listOPD) > 0)
                                    @foreach($listOPD as $opd)
                                        <option value="{{ $opd }}" {{ old('agency_name') == $opd ? 'selected' : '' }}>{{ $opd }}</option>
                                    @endforeach
                                @else
                                    <option value="BAPPEDA Lampung">BAPPEDA Lampung</option>
                                    <option value="Dinas Kesehatan">Dinas Kesehatan</option>
                                    <option value="Dinas Pendidikan">Dinas Pendidikan</option>
                                    <option value="Diskominfo Lampung">Diskominfo Lampung</option>
                                @endif
                                <option value="Lainnya" {{ old('agency_name') == 'Lainnya' ? 'selected' : '' }}>-- Perangkat Daerah Tidak Ada (Input Manual) --</option>
                            </select>
                            <input type="text" name="agency_name_manual" id="agencyManualInput" class="form-control input-gov rounded-2 mt-2 d-none" value="{{ old('agency_name_manual') }}" placeholder="Tuliskan nama OPD/Instansi baru...">
                            <div class="invalid-feedback small">OPD/Instansi sasaran wajib ditentukan.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">Kategori Insiden / Isu Siber <span class="text-danger">*</span></label>
                            <select name="category" id="categorySelect" class="form-select input-gov rounded-2" required onchange="handleManualToggle('category')">
                                <option value="" disabled selected>-- Pilih Kategori Insiden --</option>
                                @if(isset($listKategori) && count($listKategori) > 0)
                                    @foreach($listKategori as $kat)
                                        <option value="{{ $kat }}" {{ old('category') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                                    @endforeach
                                @else
                                    <option value="Web Defacement">Web Defacement</option>
                                    <option value="Judi Online (Judol)">Judi Online (Judol)</option>
                                    <option value="Malware Injection">Malware Injection</option>
                                    <option value="Data Leakage">Data Leakage</option>
                                @endif
                                <option value="Lainnya" {{ old('category') == 'Lainnya' ? 'selected' : '' }}>-- Kategori Insiden Baru (Input Manual) --</option>
                            </select>
                            <input type="text" name="category_manual" id="categoryManualInput" class="form-control input-gov rounded-2 mt-2 d-none" value="{{ old('category_manual') }}" placeholder="Tuliskan kategori insiden baru...">
                            <div class="invalid-feedback small">Kategori insiden wajib ditentukan.</div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-1">URL Target Sasaran (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fas fa-link"></i></span>
                                <input type="url" name="target_url" class="form-control input-gov border-start-0 rounded-end-2" value="{{ old('target_url') }}" placeholder="https://contoh-sasaran.go.id/path">
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold text-dark mb-1">Kronologi Temuan & Dampak <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control input-gov rounded-2" rows="4" placeholder="Uraikan kronologi temuan secara rinci: waktu, indikator, dampak, dan langkah awal yang telah diambil..." required>{{ old('description') }}</textarea>
                            <div class="invalid-feedback small">Deskripsi kronologi temuan wajib diisi.</div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light border px-4 rounded-2 fw-bold d-inline-flex align-items-center" onclick="prevWizardStep(1)">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </button>
                        <button type="button" class="btn btn-primary px-4 rounded-2 fw-bold d-inline-flex align-items-center" onclick="nextWizardStep(2, 3)">
                            Lanjut ke Langkah 3 <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>

                <!-- STEP 3: MATRIKS RISIKO, BUKTI BUKTI & SIMPAN -->
                <div class="wizard-step-content" id="wizard-step-3">
                    <div class="border-bottom pb-2 mb-3">
                        <h6 class="fw-bold mb-0" style="color: #0f3057;"><i class="fas fa-shield-alt me-1.5 text-primary"></i> Langkah 3: Tingkat Ancaman & Upload Bukti</h6>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">Tingkat Ancaman / Threat Level <span class="text-danger">*</span></label>
                            <select name="threat_level" id="threat_level" class="form-select input-gov rounded-2" required>
                                <option value="Low" {{ old('threat_level') == 'Low' ? 'selected' : '' }}>Low (Rendah)</option>
                                <option value="Medium" {{ old('threat_level', 'Medium') == 'Medium' ? 'selected' : '' }}>Medium (Sedang)</option>
                                <option value="High" {{ old('threat_level') == 'High' ? 'selected' : '' }}>High (Tinggi)</option>
                                <option value="Critical" {{ old('threat_level') == 'Critical' ? 'selected' : '' }}>Critical (Sangat Kritis)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark mb-1">Catatan Koordinasi / Disposisi (Opsional)</label>
                            <input type="text" name="coordination_note" class="form-control input-gov rounded-2" value="{{ old('coordination_note') }}" placeholder="Catatan opsional untuk verifikator/admin...">
                        </div>

                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold text-dark mb-1">Unggah Berkas Bukti (Gambar/Dokumen) <span class="text-danger">* Maks 2MB</span></label>
                            
                            <div class="upload-area" id="dropArea" onclick="document.getElementById('file-bukti').click()">
                                <i class="fas fa-cloud-upload-alt text-primary fa-3x mb-2 opacity-75"></i>
                                <p class="mb-1 fw-semibold text-dark">Seret & lepas berkas di sini, atau <span class="text-primary text-decoration-underline">telusuri dari perangkat</span></p>
                                <small class="text-muted d-block" style="font-size: 0.78rem;">Format diperbolehkan: JPG, PNG, PDF, DOCX, XLSX (Maksimal 2MB).</small>
                                <input type="file" id="file-bukti" name="file_evidence" class="d-none" accept=".jpg,.jpeg,.png,.pdf,.docx,.xlsx" required onchange="handleFileSelect(this)">
                            </div>

                            <!-- Alert Pesan Error File -->
                            <div class="alert alert-danger border-0 small fw-bold p-3 mt-3 text-danger bg-danger bg-opacity-10 rounded-3 d-none" id="error-file-alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> <span id="error-file-text">Berkas ditolak. Format tidak sesuai atau ukuran melebihi 2MB.</span>
                            </div>

                            <!-- Preview Container -->
                            <div class="preview-container" id="previewContainer">
                                <div class="d-flex align-items-center gap-3">
                                    <div id="visualPreviewHolder"></div>
                                    <div class="text-truncate">
                                        <span class="fw-bold d-block small text-dark" id="previewName">-</span>
                                        <small class="text-muted font-monospace" id="previewSize">-</small>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" onclick="clearFileSelection()" aria-label="Hapus File"></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light border px-4 rounded-2 fw-bold d-inline-flex align-items-center" onclick="prevWizardStep(2)">
                            <i class="fas fa-arrow-left me-2"></i> Kembali
                        </button>
                        <button type="submit" id="btnSubmitForm" class="btn btn-primary px-4 rounded-2 fw-bold d-inline-flex align-items-center">
                            <i class="fas fa-save me-2"></i> Simpan Log Patroli
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Manual Input jika ada old() value
        handleManualToggle('agency');
        handleManualToggle('category');

        // 1. Live Filter Dropdown OPD / Instansi
        document.getElementById('opdFilterInput')?.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            const select = document.getElementById('agencySelect');
            const options = select.options;

            for (let i = 0; i < options.length; i++) {
                const text = options[i].text.toLowerCase();
                if (options[i].value === "Lainnya" || options[i].value === "") {
                    options[i].style.display = "";
                } else {
                    options[i].style.display = text.includes(query) ? "" : "none";
                }
            }
        });

        // 2. Form Submission Handler & Preventing Double Submit
        const patrolForm = document.getElementById('patrolForm');
        patrolForm?.addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file-bukti');
            
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                e.stopPropagation();
                const alertBox = document.getElementById('error-file-alert');
                const alertText = document.getElementById('error-file-text');
                alertText.innerText = 'Harap unggah berkas bukti terlebih dahulu!';
                alertBox.classList.remove('d-none');
                return false;
            }

            if (!patrolForm.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                patrolForm.classList.add('was-validated');
                return false;
            }

            // Disable submit button & show spinner
            const btnSubmit = document.getElementById('btnSubmitForm');
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
            }
        });
    });

    // 3. Toggle Input Manual untuk OPD & Kategori
    function handleManualToggle(type) {
        if (type === 'agency') {
            const select = document.getElementById('agencySelect');
            const manualInput = document.getElementById('agencyManualInput');
            if (select && select.value === 'Lainnya') {
                manualInput.classList.remove('d-none');
                manualInput.required = true;
            } else if (manualInput) {
                manualInput.classList.add('d-none');
                manualInput.required = false;
            }
        } else if (type === 'category') {
            const select = document.getElementById('categorySelect');
            const manualInput = document.getElementById('categoryManualInput');
            if (select && select.value === 'Lainnya') {
                manualInput.classList.remove('d-none');
                manualInput.required = true;
            } else if (manualInput) {
                manualInput.classList.add('d-none');
                manualInput.required = false;
            }
        }
    }

    // 4. Multi-Step Wizard Navigation & Validation
    function nextWizardStep(current, next) {
        const currentStepDiv = document.getElementById(`wizard-step-${current}`);
        const inputs = currentStepDiv.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });

        if (isValid) {
            currentStepDiv.classList.remove('active');
            document.getElementById(`wizard-step-${next}`).classList.add('active');

            // Update Header Indicators
            const currentItem = document.getElementById(`step-node-${current}`);
            const nextItem = document.getElementById(`step-node-${next}`);
            
            currentItem.classList.remove('active');
            currentItem.classList.add('completed');
            document.getElementById(`step-icon-${current}`).innerHTML = '<i class="fas fa-check"></i>';

            nextItem.classList.add('active');
        }
    }

    function prevWizardStep(target) {
        const current = target + 1;
        document.getElementById(`wizard-step-${current}`).classList.remove('active');
        document.getElementById(`wizard-step-${target}`).classList.add('active');

        const currentItem = document.getElementById(`step-node-${current}`);
        const targetItem = document.getElementById(`step-node-${target}`);

        currentItem.classList.remove('active');
        targetItem.classList.remove('completed');
        targetItem.classList.add('active');
        document.getElementById(`step-icon-${target}`).innerText = target;
    }

    // 5. Drag and Drop & Dynamic File Previewer
    const dropArea = document.getElementById('dropArea');

    if (dropArea) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, () => dropArea.classList.remove('dragover'), false);
        });

        dropArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            const fileInput = document.getElementById('file-bukti');
            if (files && files.length > 0) {
                fileInput.files = files;
                handleFileSelect(fileInput);
            }
        });
    }

    function handleFileSelect(input) {
        const alertBox = document.getElementById('error-file-alert');
        const alertText = document.getElementById('error-file-text');
        const previewContainer = document.getElementById('previewContainer');
        const visualHolder = document.getElementById('visualPreviewHolder');
        const previewName = document.getElementById('previewName');
        const previewSize = document.getElementById('previewSize');

        alertBox.classList.add('d-none');

        if (input.files && input.files[0]) {
            const file = input.files[0];

            // Validasi Ukuran (Maksimal 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alertText.innerText = 'Ukuran berkas melebihi batas maksimum 2MB!';
                alertBox.classList.remove('d-none');
                clearFileSelection();
                return;
            }

            previewName.textContent = file.name;
            previewSize.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
            previewContainer.style.display = 'block';

            // Preview Visual (Gambar vs Dokumen)
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    visualHolder.innerHTML = `<img src="${e.target.result}" class="preview-image" alt="Preview Image">`;
                };
                reader.readAsDataURL(file);
            } else {
                let icon = 'fa-file-alt text-secondary';
                if (file.type.includes('pdf')) icon = 'fa-file-pdf text-danger';
                else if (file.name.endsWith('.docx')) icon = 'fa-file-word text-primary';
                else if (file.name.endsWith('.xlsx')) icon = 'fa-file-excel text-success';

                visualHolder.innerHTML = `<i class="fas ${icon} fa-2x"></i>`;
            }
        }
    }

    function clearFileSelection() {
        const fileInput = document.getElementById('file-bukti');
        if (fileInput) fileInput.value = '';
        const previewContainer = document.getElementById('previewContainer');
        if (previewContainer) previewContainer.style.display = 'none';
        const visualHolder = document.getElementById('visualPreviewHolder');
        if (visualHolder) visualHolder.innerHTML = '';
    }
</script>
@endpush