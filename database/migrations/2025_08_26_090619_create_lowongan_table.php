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
        Schema::create('lowongan', function (Blueprint $table) {
            $table->id();
            $table->string('posisi');
            $table->string('perusahaan');
            $table->string('lokasi');
            $table->unsignedBigInteger('gaji')->nullable();
            $table->text('deskripsi');
            $table->text('persyaratan');
            $table->enum('tipe', ['Full-time', 'Part-time', 'Internship', 'Contract'])->default('Full-time');
            $table->enum('status', ['Aktif', 'Ditutup'])->default('Aktif');
            $table->date('deadline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lowongan');
    }
};
