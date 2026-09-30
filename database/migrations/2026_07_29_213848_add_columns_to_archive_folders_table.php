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
        Schema::table('archive_folders', function (Blueprint $table) {
            $table->string('year', 4)->nullable()->after('description');
            $table->foreignId('parent_id')->nullable()->constrained('archive_folders')->onDelete('cascade')->after('year');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->after('parent_id');
            $table->string('main_menu')->nullable()->after('created_by'); // Link to petugas main_menu categories
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('archive_folders', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['year', 'parent_id', 'created_by', 'main_menu']);
        });
    }
};
