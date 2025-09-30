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
        Schema::table('pendaftaran_peserta', function (Blueprint $table) {
            $table->decimal('tagihan_per_bulan', 12, 2)
                  ->nullable()
                  ->after('jumlah_cicilan'); 
                  // sesuaikan posisi "after" dengan kolom terakhir di tabelmu
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_peserta', function (Blueprint $table) {
            $table->dropColumn('tagihan_per_bulan');
        });
    }
};
