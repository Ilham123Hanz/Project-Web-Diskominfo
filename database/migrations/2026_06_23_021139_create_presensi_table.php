<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Menerapkan skema tabel presensi harian reguler tanpa fitur GPS & Bukti Foto.
     */
    public function up(): void 
    {
        // Pengecekan preventif untuk menghindari konflik saat rollback/refresh
        Schema::dropIfExists('presensi');

        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            
            // --- RELASI UTAMA TO USERS ---
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key mengarah ke tabel users');
            
            // --- DATA WAKTU PRESENSI REGULER ---
            $table->date('tanggal_presensi')->index()->comment('Tanggal presensi harian (YYYY-MM-DD)');
            $table->time('jam_masuk')->nullable()->comment('Jam clock-in aktual petugas');
            $table->time('jam_pulang')->nullable()->comment('Jam clock-out aktual petugas');
            $table->integer('durasi_kerja_menit')->nullable()->default(0)->comment('Total durasi menit kerja harian');
            
            // --- STATUS EVALUASI KINERJA KETEPATAN WAKTU ---
            $table->enum('status_masuk', ['Tepat Waktu', 'Terlambat'])
                  ->default('Tepat Waktu')
                  ->comment('Status kedatangan petugas');
                  
            $table->enum('status_pulang', ['Selesai', 'Pulang Cepat'])
                  ->nullable()
                  ->comment('Status kepulangan petugas');

            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Dinas Luar', 'Alpa'])
                  ->default('Hadir')
                  ->index()
                  ->comment('Status utama kehadiran harian');

            $table->enum('verifikasi_admin', ['Pending', 'Approved', 'Rejected'])
                  ->default('Approved')
                  ->comment('Status verifikasi manual oleh Admin');

            // --- METODE PRESENSI ---
            $table->enum('metode_masuk', ['Web Portal', 'Mobile App', 'Manual Admin'])
                  ->default('Web Portal')
                  ->comment('Kanal pencatatan clock-in');
            $table->enum('metode_pulang', ['Web Portal', 'Mobile App', 'Manual Admin'])
                  ->nullable()
                  ->comment('Kanal pencatatan clock-out');
            
            // --- CATATAN TEKS PETUGAS & ADMIN ---
            $table->text('catatan_masuk')->nullable()->comment('Keterangan dari petugas saat clock-in');
            $table->text('catatan_pulang')->nullable()->comment('Keterangan dari petugas saat clock-out');
            $table->text('catatan_admin')->nullable()->comment('Catatan/verifikasi manual oleh Admin');
            
            // --- AUDIT TRAIL / LOG KEAMANAN JARINGAN ---
            $table->string('ip_address_masuk', 45)->nullable()->comment('IP Address client saat jam masuk (IPv4/IPv6)');
            $table->string('ip_address_pulang', 45)->nullable()->comment('IP Address client saat jam pulang (IPv4/IPv6)');
            $table->text('user_agent_masuk')->nullable()->comment('Metadata browser/device saat clock-in');
            $table->text('user_agent_pulang')->nullable()->comment('Metadata browser/device saat clock-out');
            
            // --- TIMESTAMPS LARAVEL & SOFT DELETES ---
            $table->timestamps();
            $table->softDeletes()->comment('Audit trail penghapusan data (soft delete)');
            
            // --- INTEGRITY CONSTRAINT & INDEX OPTIMIZATION ---
            // 1. Memastikan 1 petugas HANYA BISA memiliki 1 record presensi per tanggal
            $table->unique(['user_id', 'tanggal_presensi'], 'unique_user_daily_presensi');

            // 2. Composite Index untuk mempercepat query laporan rekapitualisasi harian/bulanan
            $table->index(['user_id', 'tanggal_presensi', 'status_kehadiran'], 'idx_rekap_presensi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        Schema::table('presensi', function (Blueprint $table) {
            if (Schema::hasTable('presensi')) {
                $table->dropForeign(['user_id']);
                $table->dropIndex('idx_rekap_presensi');
                $table->dropUnique('unique_user_daily_presensi');
            }
        });

        Schema::dropIfExists('presensi');
    }
};