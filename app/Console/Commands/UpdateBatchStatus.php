<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Batches;
use App\Models\SiswaDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateBatchStatus extends Command
{
    protected $signature = 'batch:update-status';
    protected $description = 'Update status batch berdasarkan tanggal mulai dan selesai';

    public function handle()
    {
        $today = Carbon::today();

       Log::info("Scheduler jalan di tanggal: " . $today);

        // Update batch yang mencapai tanggal mulai
        $batchesMulai = Batches::whereIn('status', ['registration', 'inactive'])
            ->whereDate('tanggal_mulai', '<=', $today)
            ->get();

        foreach ($batchesMulai as $batch) {
           Log::info("Batch {$batch->nama} dimulai, status berubah ke active");
            $batch->update(['status' => 'active']);
        }

        // Update batch yang mencapai tanggal selesai
        $batchesSelesai = Batches::where('status', 'active')
            ->whereDate('tanggal_selesai', '<=', $today)
            ->get();

        foreach ($batchesSelesai as $batch) {
            $batch->update(['status' => 'finished']);

            // Update semua siswa menjadi alumni
            SiswaDetail::where('batch_id', $batch->id)
                ->update(['status' => 'alumni']);

           Log::info("Batch {$batch->nama} selesai, siswa menjadi alumni");
        }

       Log::info('Update status batch selesai dipanggil.');
        $this->info('Update status batch selesai');
    }
}
