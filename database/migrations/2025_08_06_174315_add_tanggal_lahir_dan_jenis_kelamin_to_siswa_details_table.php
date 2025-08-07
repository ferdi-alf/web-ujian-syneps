<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->date('tanggal_lahir')->nullable()->after('pendidikan_terakhir');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan'])->nullable()->after('tanggal_lahir');
        });
    }

    public function down(): void
    {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lahir', 'jenis_kelamin']);
        });
    }
};
