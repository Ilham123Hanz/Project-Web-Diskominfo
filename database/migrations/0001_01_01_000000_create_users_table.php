<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. TABEL UTAMA: USERS
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            
            // --- IDENTITAS UTAMA ---
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable()->comment('Nullable untuk fleksibilitas Google Auth'); 
            $table->string('password')->nullable()->comment('Di-hash dengan Bcrypt, nullable jika login via OAuth'); 
            
            // --- IDENTITAS KEPEGAWAIAN (Diskominfo / Persandian) ---
            $table->string('nip', 18)->unique()->nullable()->comment('Nomor Induk Pegawai 18 Digit');
            $table->string('phone_number', 15)->nullable()->unique();
            $table->string('jabatan')->nullable()->comment('Contoh: Analis Siber, Sandiman, Pranata Komputer');
            $table->text('bio')->nullable()->comment('Biografi singkat petugas/user'); 
            
            // --- PERAN & OTORISASI ---
            $table->enum('role', ['Admin', 'Petugas', 'Super Admin'])->default('Petugas'); 
            
            // --- INTEGRASI GOOGLE OAUTH ---
            $table->string('google_id')->nullable()->unique();  
            $table->text('google_token')->nullable();         
            $table->text('google_refresh_token')->nullable(); 
            $table->string('avatar_url')->nullable()->comment('URL Foto Profil dari Google atau Upload Lokal');         
            
            // --- MANAJEMEN STATUS & KEAMANAN AKUN (AUDIT READY) ---
            $table->enum('status', ['Active', 'Inactive', 'Suspended'])->default('Active')->comment('Status kontrol akses fisik akun');
            $table->boolean('is_email_verified')->default(false)->comment('Status verifikasi kepemilikan email');
            $table->unsignedInteger('login_attempts')->default(0)->comment('Proteksi brute-force: menghitung kesalahan login');
            $table->timestamp('locked_until')->nullable()->comment('Waktu penangguhan akun akibat salah password berulang');
            $table->timestamp('password_changed_at')->nullable()->comment('Log rotasi kepatuhan password');
            
            // --- AUDIT TRAIL / LOG PELACAKAN SIBER ---
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable()->comment('Mendukung pelacakan alamat IPv4 dan IPv6');
            
            // --- TEMPLATE BAWAAN LARAVEL & ARSIP ---
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Menghindari kehilangan data log melalui soft delete ('deleted_at')
            
            // --- INDEX KINERJA UNTUK OPTIMASI DATA DENGAN VOLUMETRIK BESAR ---
            $table->index(['role', 'status']);
            $table->index('nip'); // Mempercepat query cari petugas berdasarkan NIP
        });

        // 2. TABEL TOKEN RESET PASSWORD (Disinkronkan dengan Standar Laravel & Fleksibilitas Email)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 3. TABEL MANAJEMEN SESI AKTIF (SESSIONS)
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};