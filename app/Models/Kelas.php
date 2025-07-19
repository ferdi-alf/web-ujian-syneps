<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
    ];

    public function siswaDetails(): HasMany
    {
        return $this->hasMany(SiswaDetail::class, 'kelas_id');
    }

    public function pengajarDetails(): HasMany
    {
        return $this->hasMany(PengajarDetail::class, 'kelas_id');
    }

    public function ujians(): HasMany
    {
        return $this->hasMany(Ujian::class, 'kelas_id');
    }

    public function siswas()
    {
        return $this->hasManyThrough(
            User::class,
            SiswaDetail::class,
            'kelas_id',
            'id',
            'id',
            'siswa_id'
        );
    }

    public function pengajars()
    {
        return $this->hasManyThrough(
            User::class,
            PengajarDetail::class,
            'kelas_id',
            'id',
            'id',
            'pengajar_id'
        );
    }
}