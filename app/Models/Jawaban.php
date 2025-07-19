<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jawaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'soal_id',
        'pilihan',
        'teks',
        'benar',
    ];

    protected function casts(): array
    {
        return [
            'benar' => 'boolean',
        ];
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