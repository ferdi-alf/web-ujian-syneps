<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendaftaranPeserta extends Model
{
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
            'tagihan_per_bulan',
            'bukti_pembayaran_dp',
            'created_at',
        ];
}
