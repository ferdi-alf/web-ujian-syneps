<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',        
        'materi',       
        'kelas_id',
        'batch_id'
    ];

    /**
     * Relasi ke model Kelas
     * Setiap materi belongs to satu kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    /**
     * Relasi ke model Batch
     * Setiap materi belongs to satu batch
     */
    public function batch()
    {
        return $this->belongsTo(Batches::class);
    }
}