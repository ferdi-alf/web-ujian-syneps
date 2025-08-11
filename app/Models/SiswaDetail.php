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
    'no_hp',
    'status',
    'alamat',
    'pendidikan_terakhir',
    'jenis_kelamin',
    'mengetahui_program_dari',
    'total_tagihan',
    'jumlah_cicilan',
    'tagihan_per_bulan',
    'ikut_magang',
    'link_tiktok',
    'link_instagram',
    'link_x',
    'link_linkedin',
    'link_facebook',
    'link_github'
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

     public function tagihans()
    {
        return $this->hasMany(Tagihan::class, 'siswa_id');
    }
}