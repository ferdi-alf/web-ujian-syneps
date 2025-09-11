<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model {
    use HasFactory;

    protected $fillable = [
        'nama',
        'harga',
        'type',
        'dp_persen',
        'durasi_belajar',
        'waktu_magang'
    ];

    public function getFormattedTypeAttribute()
    {
        return $this->type ? ucfirst($this->type) : '-';
    }

    public function getNamaAttribute($value)
    {
        if (isset($this->attributes['type']) && !empty($this->attributes['type'])) {
            return $value . ' - ' . $this->attributes['type'];
        }
        return $value;
    }

    public function getFormattedDurationAttribute()
    {
        $result = '';
        
        if ($this->durasi_belajar) {
            $result = $this->durasi_belajar . ' bulan';
            
            if ($this->waktu_magang && $this->waktu_magang > 0) {
                $result .= ' + ' . $this->waktu_magang . ' bulan magang';
            }
        }
        
        return $result ?: '-';
    }


    public function getTypeAndDurationAttribute() {
        $result = '';
        
        if ($this->type) {
            $result .= ucfirst($this->type);
        } else {
            $result .= '-';
        }

        if ($this->durasi_belajar) {
            if ($result !== '-') {
                $result .= ' (' . $this->durasi_belajar . ' bulan';
                } else {
                    $result = $this->durasi_belajar . ' bulan';
                }
                
                if ($this->waktu_magang && $this->waktu_magang > 0) {
                    $result .= ' + ' . $this->waktu_magang . ' bulan magang';
                }
                
                if ($result !== '-' && strpos($result, '(') !== false) {
                    $result .= ')';
            }
        }

        return $result;
    }

    public function getTotalDpAttribute() {
        return ($this->harga * $this->dp_persen) / 100;
    }

    public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->harga, 0, ',', '.');
    }

    public function getFormattedDpAttribute()
    {
        return 'Rp ' . number_format($this->total_dp, 0, ',', '.');
    }

    public function materis()
    {
        return $this->hasMany(Materi::class);
    }

    public function siswaDetails(): HasMany
    {
        return $this->hasMany(SiswaDetail::class, 'kelas_id');
    }

    public function getFormattedKelasAttribute() 
    {
        $result = $this->nama;
        
        $details = [];
        
        if (!empty($this->type)) {
            $details[] = ucfirst($this->type);
        }
        
        if (!empty($this->durasi_belajar)) {
            $durasi = $this->durasi_belajar . ' bulan';
            
            if (!empty($this->waktu_magang) && $this->waktu_magang > 0) {
                $durasi .= ' + ' . $this->waktu_magang . ' bulan magang';
            }
            
            $details[] = $durasi;
        }
        
        if (!empty($details)) {
            $result .= '<br><small class="text-muted">(' . implode(', ', $details) . ')</small>';
        }
        
        return $result;
    }


    public function pengajarDetails(): BelongsToMany
    {
        return $this->belongsToMany(
            PengajarDetail::class,
            'pengajar_kelas',
            'kelas_id',
            'pengajar_detail_id'
        );
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

    public function batches(): HasMany
    {
        return $this->hasMany(Batches::class, 'kelas_id');
    }

     public function getActiveBatchAttribute()
    {
        return $this->batches()->where('status', 'active')->first();
    }
    
    public function hasActiveBatch()
    {
        return $this->batches()->where('status', 'active')->exists();
    }

    public function hasRegistrationBatch()
    {
        return $this->batches()->where('status', 'registration')->exists();
    }
    
    public function getSlugAttribute()
    {
        $activeBatch = $this->active_batch;
        if ($activeBatch) {
            return $activeBatch->slug;
        }
        return null;
    }

}