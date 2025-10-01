<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->enum('ikut_magang', ['belum ditentukan', 'ikut', 'tidak'])
                  ->default('belum ditentukan')
                  ->change();
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->tinyInteger('ikut_magang')
                  ->default(0)
                  ->change();
        });
    }
};
