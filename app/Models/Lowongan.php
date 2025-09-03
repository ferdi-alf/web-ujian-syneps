<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lowongan extends Model
{
    protected $table = 'lowongan';

    protected $fillable = [
        'posisi',
        'perusahaan',
        'lokasi',
        'gaji',
        'deskripsi',
        'persyaratan',
        'tipe',
        'status',
        'deadline',
    ];

    public function lamaran()
    {
        return $this->hasMany(Lamaran::class);
    }
}
