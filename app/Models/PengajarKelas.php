<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengajarKelas extends Model
{
    protected $table = 'pengajar_kelas';

    protected $fillable = [
        'pengajar_detail_id',
        'kelas_id',
    ];

    public function pengajarDetail(): BelongsTo
    {
        return $this->belongsTo(PengajarDetail::class, 'pengajar_detail_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
 