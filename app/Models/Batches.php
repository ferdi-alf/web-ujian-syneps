<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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

     public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

     public function getSlugAttribute()
    {
        $kelasSlug = Str::slug($this->kelas->nama);
        $batchSlug = Str::slug($this->nama);
        return $kelasSlug . '-' . $batchSlug;
    }
    
    // Static method untuk find by slug
    public static function findActiveBySlug($slug)
    {
        return static::with('kelas')
            ->where('status', 'active') 
            ->get()
            ->first(function ($batch) use ($slug) {
                return $batch->slug === $slug;
            });
    }
    public static function findRegistrationBySlug($slug)
    {
        return static::with('kelas')
            ->where('status', 'registration') 
            ->get()
            ->first(function ($batch) use ($slug) {
                return $batch->slug === $slug;
            });
    }
    


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


}
