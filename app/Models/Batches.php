<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batches extends Model
{
   use HasFactory;

    protected $fillable = [
        'nama',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'kelas_id',
    ];

      public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    /**
     * Relasi ke siswa detail
     */
    public function siswaDetails(): HasMany
    {
        return $this->hasMany(SiswaDetail::class, 'batch_id');
    }

    /**
     * Relasi ke ujian
     */
    public function ujians(): HasMany
    {
        return $this->hasMany(Ujian::class, 'batch_id');
    }

    /**
     * Scope untuk batch aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'active');
    }

    public function kelas()
{
    return $this->belongsTo(Kelas::class, 'kelas_id');
}

}
