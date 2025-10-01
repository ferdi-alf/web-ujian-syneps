<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batches;
use App\Models\SiswaDetail;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GeneratePembayaran extends Command
{
    protected $signature = 'pembayaran:generate {--date=}';
    protected $description = 'Generate tagihan pembayaran bulanan untuk siswa';
    

    public function handle()
    {
        $today = $this->option('date') 
            ? Carbon::parse($this->option('date')) 
            : Carbon::today();

        
        // Ambil semua batch yang aktif
        $batchesAktif = Batches::where('status', 'active')->get();
        
        foreach ($batchesAktif as $batch) {
            $tanggalMulai = Carbon::parse($batch->tanggal_mulai);
            $siswaDetails = SiswaDetail::where('batch_id', $batch->id)
                ->where('status', '!=', 'alumni')
                ->get();
            
            foreach ($siswaDetails as $siswaDetail) {
                // Cek apakah sudah ada pembayaran yang di-generate
                $pembayaranTerakhir = Pembayaran::where('siswa_detail_id', $siswaDetail->id)
                    ->orderBy('cicilan_ke', 'desc')
                    ->first();
                
                if ($pembayaranTerakhir) {
                    $tanggalJatuhTempoBerikutnya = Carbon::parse($pembayaranTerakhir->tanggal_jatuh_tempo)->addMonth();
                    $cicilanKe = $pembayaranTerakhir->cicilan_ke + 1;
                } else {
                    // Pembayaran pertama dimulai 1 bulan setelah tanggal mulai
                    $tanggalJatuhTempoBerikutnya = $tanggalMulai->copy()->addMonth();
                    $cicilanKe = 1;
                }
                
                // Cek apakah hari ini adalah tanggal jatuh tempo
                if ($today->isSameDay($tanggalJatuhTempoBerikutnya)) {
                    // Cek apakah masih ada cicilan yang harus dibayar
                    if ($cicilanKe <= $siswaDetail->jumlah_cicilan && $siswaDetail->total_tagihan > 0) {
                        // Generate pembayaran baru
                        Pembayaran::create([
                            'siswa_detail_id' => $siswaDetail->id,
                            'jumlah_dibayar' => $siswaDetail->tagihan_per_bulan,
                            'status' => 'belum dibayar',
                            'tanggal_jatuh_tempo' => $tanggalJatuhTempoBerikutnya,
                            'cicilan_ke' => $cicilanKe,
                        ]);
                        
                        $this->info("Pembayaran cicilan ke-{$cicilanKe} untuk {$siswaDetail->nama_lengkap} telah dibuat");
                    }
                }
            }
        }
        
        $this->info('Generate pembayaran selesai');
    }
}