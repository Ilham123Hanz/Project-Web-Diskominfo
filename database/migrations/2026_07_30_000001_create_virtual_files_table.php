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
        Schema::create('virtual_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('archive_folder_id')->constrained('archive_folders')->onDelete('cascade');
            $table->string('name'); // Nama file
            $table->string('original_name'); // Nama asli file
            $table->string('path'); // Path penyimpanan
            $table->string('mime_type')->nullable(); // Tipe MIME
            $table->unsignedBigInteger('size')->default(0); // Ukuran file dalam bytes
            $table->string('source')->nullable(); // 'patroli' (dari input petugas) atau 'manual' (drag & drop)
            $table->foreignId('laporan_id')->nullable()->constrained('laporan')->onDelete('set null'); // Relasi ke laporan jika dari petugas
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Siapa yang upload
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_files');
    }
};