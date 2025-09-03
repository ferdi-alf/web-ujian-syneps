<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lamaran extends Model
{
    protected $table = 'lamaran';

    protected $fillable = [
        'user_id',
        'lowongan_id',
        'status',
        'resume_path',
        'surat_lamaran',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class);
    }
}
