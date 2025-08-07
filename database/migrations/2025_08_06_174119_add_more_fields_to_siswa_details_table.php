<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->string('pendidikan_terakhir')->nullable();

            $table->enum('mengetahui_program_dari', [
                'instagram', 'tiktok', 'facebook', 'website', 'teman/keluarga', 'google', 'lain_lain'
            ])->default('lain_lain');

            $table->string('link_instagram')->nullable();
            $table->string('link_tiktok')->nullable();
            $table->string('link_linkedin')->nullable();
            $table->string('link_facebook')->nullable();
            $table->string('link_x')->nullable(); // Twitter/X
            $table->string('link_github')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('siswa_details', function (Blueprint $table) {
            $table->dropColumn([
                'no_hp',
                'alamat',
                'pendidikan_terakhir',
                'mengetahui_program_dari',
                'link_instagram',
                'link_tiktok',
                'link_linkedin',
                'link_facebook',
                'link_x',
                'link_github'
            ]);
        });
    }
};
