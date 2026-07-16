<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void 
    {
        Schema::create('patrols', function (Blueprint $table) {
            $table->id();
            
            // --- KUNCI RELASI (FOREIGN KEYS) ---
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Menghubungkan log patroli secara spesifik ke ID absensi shift hari itu
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade'); 
            
            // --- KODE LOG OTOMATIS (Siber Case Management) ---
            $table->string('log_code', 20)->unique()->comment('Contoh: LOG-2026-001');

            // --- STRUKTUR FORM PATROLI (INTELLIGENCE CATEGORIES) ---
            $table->enum('rumpun_kategori', ['Patroli Harian', 'Advanced Assessment'])
                  ->default('Patroli Harian')
                  ->comment('Pilihan langkah pertama di wizard form');
                  
            $table->enum('main_menu', ['Bug Hunter', 'CTI', 'Laporan Insiden', 'Patroli Siber', 'Sosial Media', 'Vul/Pen Test'])
                  ->comment('Menu utama klasifikasi penugasan');
                  
            $table->string('kategori_insiden')->comment('Contoh: Web Defacement, Judi Online, Phishing, dll.');
            $table->string('opd_sasaran')->comment('Nama Instansi / Perangkat Daerah target');
            $table->string('target_url')->nullable()->comment('Manual copy-paste URL terdampak');
            
            // --- ANALISIS KLASIFIKASI ANCAMAN (THREAT ASSESSMENT) ---
            $table->enum('threat_level', ['Low', 'Medium', 'High', 'Critical'])->default('Low');
            
            // --- URAIAN INFORMASI & KRONOLOGI ---
            $table->text('description')->comment('Kronologi detail temuan siber');
            $table->text('coordination_note')->nullable()->comment('Catatan koordinasi tindak lanjut dengan pihak eksternal/CSIRT');
            
            // --- TRANSMISI BERKAS (VALIDASI KEAMANAN FILE) ---
            $table->string('file_evidence')->nullable()->comment('Bukti dukung (Foto Screenshot / Dokumen PDF/DOCX/XLSX)');
            
            // --- TAHAPAN WORKFLOW & AUDIT VALIDASI ADMIN ---
            $table->enum('status', ['Pending', 'Perlu Perbaikan', 'Verified'])->default('Pending');
            $table->text('admin_correction')->nullable()->comment('Catatan umpan balik dari admin jika status Perlu Perbaikan');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null')->comment('ID Admin/Super Admin yang memvalidasi');
            $table->timestamp('verified_at')->nullable();

            // --- BAWAAN LARAVEL ---
            $table->timestamps();
            
            // --- INDEX KINERJA QUERY ---
            $table->index(['status', 'threat_level']);
            $table->index('log_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        Schema::dropIfExists('patrols');
    }
};