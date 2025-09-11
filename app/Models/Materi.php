<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',        
        'materi',       
        'kelas_id',
        'batch_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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

     public function getFormattedTitleAttribute()
    {
        $downloadUrl = route('materi.download', $this->id);
        return '<a href="' . $downloadUrl . '" class="flex items-center hover:text-blue-600 transition-colors">
                    <i class="fa-solid fa-file-pdf text-red-500 mr-2"></i>
                    <span class="font-medium">' . $this->judul . '</span>
                </a>';
    }

    public function getFileSizeAttribute()
    {
        if ($this->file_pdf && Storage::exists($this->file_pdf)) {
            $bytes = Storage::size($this->file_pdf);
            $units = ['B', 'KB', 'MB', 'GB'];
            $i = 0;
            while ($bytes >= 1024 && $i < 3) {
                $bytes /= 1024;
                $i++;
            }
            return round($bytes, 2) . ' ' . $units[$i];
        }
        return 'Unknown';
    }
}