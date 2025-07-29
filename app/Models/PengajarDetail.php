<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PengajarDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'pengajar_id',
        'kelas_id',
        'nama_lengkap',
    ];

    public function pengajar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengajar_id');
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(
            Kelas::class,
            'pengajar_kelas',
            'pengajar_detail_id',
            'kelas_id'
        );
    }

}
