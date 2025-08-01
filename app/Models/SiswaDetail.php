<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
    'siswa_id',
    'kelas_id',
    'batch_id',
    'nama_lengkap',
];

public function batches(): BelongsTo
{
    return $this->belongsTo(Batches::class, 'batch_id');
}


    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    // Relasi ke kelas
    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}