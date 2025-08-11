<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tagihan extends Model
{
    use HasFactory;

    protected $table = 'tagihans';

    protected $fillable = [
        'siswa_id',
        'tanggal_tagihan',
        'jumlah_tagihan',
        'status',
    ];


    public function siswa()
    {
        return $this->belongsTo(SiswaDetail::class, 'siswa_id');
    }


    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class, 'tagihan_id');
    }


    public function isLunas()
    {
        return $this->status === 'lunas';
    }
}
