<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('archive_folders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // Nama Folder (misal: Arsip_Judol_2026)
            $table->string('slug')->unique();  // Slug untuk nama folder di penyimpanan fisik
            $table->text('description')->nullable(); // Deskripsi folder
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('archive_folders');
    }
};