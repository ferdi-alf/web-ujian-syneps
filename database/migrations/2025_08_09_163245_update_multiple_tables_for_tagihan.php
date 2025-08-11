<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /**
         * Update table kelas
         */
        Schema::table('kelas', function (Blueprint $table) {
            $table->decimal('harga', 15, 2)->after('nama'); // harga kelas
            $table->enum('type', ['intensif', 'partime'])->nullable()->after('harga'); // boleh kosong
            $table->integer('dp_persen')->default(0)->after('type'); // DP dalam %
            $table->integer('waktu_magang')->default(0)->after('dp_persen'); // bulan magang
            $table->integer('durasi_belajar')->default(0)->after('waktu_magang'); // lama belajar dalam bulan
        });


        /**
         * Update table pendaftaran_peserta
         */
        Schema::table('pendaftaran_peserta', function (Blueprint $table) {
            $table->decimal('total_tagihan', 15, 2)->nullable()->after('status'); // hasil hitung backend
            $table->integer('jumlah_cicilan')->nullable()->after('total_tagihan');
            $table->string('bukti_pembayaran_dp')->nullable()->after('jumlah_cicilan');
        });
       


        /**
         * Update table siswa_details
         */
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->decimal('total_tagihan', 15, 2)->nullable()->after('siswa_id');
            $table->integer('jumlah_cicilan')->nullable()->after('total_tagihan');
            $table->decimal('tagihan_per_bulan', 15, 2)->nullable()->after('jumlah_cicilan');
            $table->boolean('ikut_magang')->default(true)->after('tagihan_per_bulan');
        });

    }

    public function down(): void
    {
        /**
         * Revert table kelas
         */
        Schema::table('kelas', function (Blueprint $table) {
            $table->dropColumn(['harga', 'type', 'dp_persen', 'waktu_magang']);
        });

        /**
         * Revert table pendaftaran_peserta
         */
        Schema::table('pendaftaran_peserta', function (Blueprint $table) {
            $table->dropColumn(['ikut_magang', 'total_tagihan', 'jumlah_cicilan']);
        });

        /**
         * Revert table siswa_details
         */
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->dropColumn(['total_tagihan', 'jumlah_cicilan', 'ikut_magang']);
        });
    }
};
