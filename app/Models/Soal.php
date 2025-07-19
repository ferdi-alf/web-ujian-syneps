<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Soal extends Model
{
    use HasFactory;

    protected $fillable = [
        'ujian_id',
        'soal',
    ];

    // Relasi ke ujians
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    // Relasi ke jawabans
    public function jawabans(): HasMany
    {
        return $this->hasMany(Jawaban::class, 'soal_id');
    }

    // Relasi ke jawaban_siswas
    public function jawabanSiswas(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'soal_id');
    }

    // Method untuk mendapatkan jawaban benar
    public function jawabanBenar()
    {
        return $this->jawabans()->where('benar', true)->first();
    }
}