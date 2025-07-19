<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ujian extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'kelas_id',
        'waktu_menit',
        'mulai',
    ];

    protected function casts(): array
    {
        return [
            'mulai' => 'datetime',
        ];
    }

    // Relasi ke kelas
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke soals
    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'ujian_id');
    }

    // Relasi ke jawaban_siswas
    public function jawabanSiswas(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'ujian_id');
    }

    // Relasi ke hasil_ujians
    public function hasilUjians(): HasMany
    {
        return $this->hasMany(HasilUjian::class, 'ujian_id');
    }

    // Scope untuk ujian yang sudah dimulai
    public function scopeStarted($query)
    {
        return $query->where('mulai', '<=', now());
    }

    // Scope untuk ujian yang belum dimulai
    public function scopeNotStarted($query)
    {
        return $query->where('mulai', '>', now());
    }
}