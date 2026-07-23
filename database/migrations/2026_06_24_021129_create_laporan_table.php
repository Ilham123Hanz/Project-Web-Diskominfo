<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Menerapkan skema tabel laporan log patroli siber dengan integritas data dan audit validasi admin.
     */
    public function up(): void 
    {
        // Drop preventif jika terjadi sisa rollback/migration gagal sebelumnya
        Schema::dropIfExists('laporan');

        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            
            // --- KUNCI RELASI (FOREIGN KEYS) ---
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('ID Petugas pelapor log laporan');

            // Menghubungkan log laporan secara spesifik ke ID presensi harian
            $table->foreignId('presensi_id')
                  ->nullable()
                  ->constrained('presensi')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('ID sesi presensi harian petugas'); 
            
            // --- KODE LOG OTOMATIS (Siber Case Management) ---
            $table->string('log_code', 30)->unique()->comment('Kode Unik Log, Contoh: LOG-2026-001 / LOG-045');

            // --- STRUKTUR FORM LAPORAN (INTELLIGENCE CATEGORIES) ---
            $table->enum('rumpun_kategori', ['Patroli Harian', 'Advanced Assessment'])
                  ->default('Patroli Harian')
                  ->comment('Pilihan langkah pertama di form wizard');
                  
            $table->enum('main_menu', ['Bug Hunter', 'CTI', 'Laporan Insiden', 'Patroli Siber', 'Sosial Media', 'Vul/Pen Test'])
                  ->default('Patroli Siber')
                  ->comment('Menu utama klasifikasi penugasan');
                  
            $table->string('kategori_insiden')->comment('Contoh: Web Defacement, Judi Online (Judol), Phishing, Malware Injection, dll.');
            $table->string('opd_sasaran')->comment('Nama Perangkat Daerah / OPD / Instansi target');
            $table->text('target_url')->nullable()->comment('URL / Subdomain terdampak');
            
            // --- ANALISIS KLASIFIKASI ANCAMAN (THREAT ASSESSMENT) ---
            $table->enum('threat_level', ['Low', 'Medium', 'High', 'Critical'])
                  ->default('Medium')
                  ->comment('Tingkat keparahan ancaman siber');
            
            // --- URAIAN INFORMASI & KRONOLOGI ---
            $table->longText('description')->comment('Kronologi & deskripsi detail temuan insiden siber');
            $table->text('coordination_note')->nullable()->comment('Catatan koordinasi tindak lanjut dengan pihak eksternal/CSIRT/OPD');
            
            // --- TRANSMISI BERKAS (VALIDASI KEAMANAN FILE) ---
            $table->string('file_evidence')->nullable()->comment('Path / Nama file bukti dukung (Image/PDF/Doc)');
            
            // --- TAHAPAN WORKFLOW & AUDIT VALIDASI ADMIN ---
            // Enum status diakomodasi penuh untuk menyelaraskan query pada LaporanController
            $table->enum('status', [
                'Pending', 
                'Menunggu Validasi', 
                'Approved', 
                'Verified', 
                'Disetujui Admin', 
                'Perlu Perbaikan', 
                'Revision', 
                'Rejection'
            ])->default('Pending')
              ->comment('Status validasi oleh Admin/Super Admin');
                  
            $table->text('admin_correction')->nullable()->comment('Catatan umpan balik / revisi dari admin jika ada koreksi');
            
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('ID Admin / Verifikator yang menyetujui/meminta perbaikan');
                  
            $table->timestamp('verified_at')->nullable()->comment('Waktu stempel verifikasi oleh admin');

            // --- TIMESTAMPS LARAVEL ---
            $table->timestamps();
            
            // --- INDEX KINERJA QUERY ---
            $table->index(['user_id', 'status']);
            $table->index(['status', 'threat_level']);
            $table->index('log_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        Schema::table('laporan', function (Blueprint $table) {
            if (Schema::hasTable('laporan')) {
                $table->dropForeign(['user_id']);
                $table->dropForeign(['presensi_id']);
                $table->dropForeign(['verified_by']);
            }
        });

        Schema::dropIfExists('laporan');
    }
};