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
        // Step 1: Drop foreign key dan kolom tagihan_id dari pembayarans
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropForeign(['tagihan_id']);
            $table->dropColumn('tagihan_id');
        });

        Schema::dropIfExists('tagihans');

        Schema::table('pembayarans', function (Blueprint $table) {
            if (!Schema::hasColumn('pembayarans', 'jumlah_dibayar')) {
                $table->decimal('jumlah_dibayar', 15, 2);
            }

            if (!Schema::hasColumn('pembayarans', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran')->nullable();
            }

            if (Schema::hasColumn('pembayarans', 'status')) {
                $table->dropColumn('status');
            }
        });

        // Tambahkan status baru
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->enum('status', ['belum dibayar', 'pending', 'disetujui', 'ditolak'])->default('belum dibayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step rollback: buat ulang tabel tagihans
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa_details')->onDelete('cascade');
            $table->date('tanggal_tagihan');
            $table->decimal('jumlah_tagihan', 15, 2);
            $table->enum('status', ['belum_dibayar', 'menunggu_konfirmasi', 'lunas'])->default('belum_dibayar');
            $table->timestamps();
        });

        // Ubah balik pembayarans
        Schema::table('pembayarans', function (Blueprint $table) {
            $table->dropColumn(['jumlah_dibayar', 'bukti_pembayaran', 'status']);
            $table->foreignId('tagihan_id')->constrained('tagihans')->onDelete('cascade');
        });
    }
};
