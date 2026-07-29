<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('opd_sosmeds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('opd_id')->constrained('master_opd')->onDelete('cascade');
            $table->string('instagram');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('opd_sosmeds');
    }
};