<?php

namespace App\Models;

use App\Models\Batches;
use App\Models\Kelas;
use Illuminate\Database\Eloquent\Model;

class PendaftaranPeserta extends Model {
    protected $table = 'pendaftaran_peserta';
        protected $fillable = [
            'batch_id',
            'kelas_id',
            'nama_lengkap',
            'email',
            'no_hp',
            'alamat',
            'pendidikan_terakhir',
            'jenis_kelamin',
            'status',
            'mengetahui_program_dari',
            'total_tagihan',
            'jumlah_cicilan',
            'bukti_pembayaran_dp',
            'link_register',
            'created_at',

        ];

    public function batches()
    {
        return $this->belongsTo(Batches::class, 'batch_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

     public function getFormattedHargaAttribute()
    {
        return 'Rp ' . number_format($this->total_tagihan, 0, ',', '.');
    }
}
