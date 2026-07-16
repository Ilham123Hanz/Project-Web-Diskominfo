<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Menerapkan skema tabel absensi reguler non-GPS dengan proteksi integritas data tingkat tinggi.
     */
    public function up(): void 
    {
        // Pengecekan preventif: Jika tabel sudah ada karena sisa-sisa rollback gagal, drop terlebih dahulu
        Schema::dropIfExists('attendances');

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            
            // --- RELASI UTAMA ---
            // Menggunakan foreignId dengan restriksi cascade agar jika user dihapus, riwayat log ikut terhapus bersih
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            
            // --- DATA WAKTU PRESENSI REGULER (07.30 - 16.00) ---
            // Index dipasang pada tanggal karena kolom ini akan sangat sering digunakan dalam klausa WHERE/filter rekap
            $table->date('attendance_date')->index()->comment('Tanggal rekaman presensi harian');
            $table->time('clock_in')->comment('Jam masuk aktual personel');
            $table->time('clock_out')->nullable()->comment('Jam pulang/keluar aktual personel');
            
            // --- STATUS EVALUASI KINERJA WAKTU ---
            $table->string('status_in', 30)->default('Tepat Waktu')->comment('Status Masuk: Tepat Waktu / Terlambat');
            $table->string('status_out', 30)->nullable()->comment('Status Pulang: Selesai / Pulang Awal');
            
            // --- KOLOM KETERANGAN / NOTES PETUGAS (PENGGANTI GPS) ---
            $table->text('notes_in')->nullable()->comment('Keterangan status/kondisi saat absen masuk');
            $table->text('notes_out')->nullable()->comment('Keterangan status/kondisi saat absen pulang');
            
            // --- AUDIT TRAIL / LOG KEAMANAN JARINGAN ---
            // Panjang 45 karakter disiapkan untuk mendukung format IPv4 maupun IPv6 di masa depan
            $table->string('ip_address_in', 45)->nullable()->comment('IP Address client saat clock in');
            $table->string('ip_address_out', 45)->nullable()->comment('IP Address client saat clock out');
            $table->text('device_agent')->nullable()->comment('Metadata user-agent browser/perangkat personel');
            
            // --- BAWAAN LARAVEL ---
            $table->timestamps();
            
            // --- INTEGRITY CONSTRAINT (BUSINESS RULE LAYER) ---
            // Kunci utama: Memastikan 1 user HANYA BISA memiliki 1 baris absensi per tanggal kerja. Mencegah double insert.
            $table->unique(['user_id', 'attendance_date'], 'unique_user_daily_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void 
    {
        // Putus foreign key constraint terlebih dahulu sebelum melakukan drop table demi keamanan integritas database
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasTable('attendances')) {
                $table->dropForeign(['user_id']);
            }
        });

        Schema::dropIfExists('attendances');
    }
};