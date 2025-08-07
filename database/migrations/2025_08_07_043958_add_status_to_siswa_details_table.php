<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->enum('status', ['active', 'alumni'])->default('active')->after('batch_id');
        });
    }

    public function down(): void {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};