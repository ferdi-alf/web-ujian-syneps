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
        'waktu',
        'status',
        'batch_id',
    ];

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batches::class, 'batch_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'ujian_id');
    }

    public function jawabanSiswas(): HasMany
    {
        return $this->hasMany(JawabanSiswa::class, 'ujian_id');
    }

    public function hasilUjians(): HasMany
    {
        return $this->hasMany(HasilUjian::class, 'ujian_id');
    }
}