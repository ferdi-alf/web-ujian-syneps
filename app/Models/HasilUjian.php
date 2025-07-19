<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilUjian extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'ujian_id',
        'jumlah_benar',
        'jumlah_salah',
        'nilai',
        'waktu_pengerjaan',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(User::class, 'siswa_id');
    }

    public function ujian(): BelongsTo
    {
        return $this->belongsTo(Ujian::class, 'ujian_id');
    }

    public function getPersentaseAttribute()
    {
        $totalSoal = $this->jumlah_benar + $this->jumlah_salah;
        return $totalSoal > 0 ? round(($this->jumlah_benar / $totalSoal) * 100, 2) : 0;
    }

    public function getGradeAttribute()
    {
        $persentase = $this->persentase;
        
        if ($persentase >= 90) return 'A';
        if ($persentase >= 80) return 'B';
        if ($persentase >= 70) return 'C';
        if ($persentase >= 60) return 'D';
        return 'E';
    }
}