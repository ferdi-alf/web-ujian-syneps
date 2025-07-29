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
            Schema::create('ujians', function (Blueprint $table) {
                $table->id();
                $table->string('judul');
                $table->foreignId('kelas_id')
                    ->nullable() 
                    ->constrained('kelas')
                    ->onDelete('set null');
                $table->enum('status', ['pending', 'active', 'finished'])->default('pending');
                $table->integer('waktu')->nullable();
                
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ujians');
    }
};
