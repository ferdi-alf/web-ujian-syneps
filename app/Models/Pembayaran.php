<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'siswa_detail_id',
        'jumlah_dibayar',
        'bukti_pembayaran',
        'status',
        'tanggal_jatuh_tempo',
        'cicilan_ke',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
    ];

    public function siswaDetail()
    {
        return $this->belongsTo(SiswaDetail::class, 'siswa_detail_id');
    }

    public function isDisetujui()
    {
        return $this->status === 'disetujui';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isBelumDibayar()
    {
        return $this->status === 'belum dibayar';
    }

    public function getJumlahFormattedAttribute()
    {
        return number_format($this->jumlah_dibayar, 0, ',', '.');
    }


}