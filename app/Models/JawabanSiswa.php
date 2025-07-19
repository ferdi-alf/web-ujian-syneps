<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JawabanSiswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'ujian_id',
        'soal_id',
        'jawaban_pilihan',
        'benar',
    ];

    protected function casts(): array
    {
        return [
            'benar' => 'boolean',
        ];
    }

    // Relasi ke users (siswa)
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    // Relasi ke ujians
    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    // Relasi ke soals
    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    // Scope untuk jawaban benar
    public function scopeBenar($query)
    {
        return $query->where('benar', true);
    }

    // Scope untuk jawaban salah
    public function scopeSalah($query)
    {
        return $query->where('benar', false);
    }
}