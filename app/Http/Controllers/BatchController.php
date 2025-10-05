<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Batches;
use App\Models\Kelas;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

use function Illuminate\Log\log;

class BatchController extends Controller
{
    /**
     * Store a newly created batch
     */
     public function show($id)
    {
        try {
            $materi = Batches::with(['kelas'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $materi->id,
                    'nama' => $materi->nama,
                    'status' => $materi->status,
                    'tanggal_mulai' => $materi->tanggal_mulai,
                    'tanggal_selesai' => $materi->tanggal_selesai,
                    'kelas' => $materi->kelas->nama,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'nama'           => 'required|string|max:255',
            'status'         => 'required|in:active,inactive,registration',
            'kelas_id'       => 'required|exists:kelas,id',
            'tanggal_mulai'   => 'required_if:status,active|nullable|date',
            'tanggal_selesai' => 'required_if:status,active|nullable|date|after:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();

                Log::info('validated', [
                ]);
        }

        $kelas = Kelas::where('id', $request->kelas_id)->first();
        $namaKelas = $kelas ? $kelas->nama : 'Kelas tidak ditemukan';
        $typeKelas = $kelas ? $kelas->type : '-';

        if ($request->status === 'active') {
            $durasiBelajar = (int) ($kelas->durasi_belajar ?? 0);
            $waktuMagang   = (int) ($kelas->waktu_magang ?? 0);
            $totalDurasi   = $durasiBelajar + $waktuMagang;

            $mulai   = \Carbon\Carbon::parse($request->tanggal_mulai);
            $selesai = \Carbon\Carbon::parse($request->tanggal_selesai);

            $selisihBulan = $mulai->diffInMonths($selesai);
            log('Selisih Bulan: ' . $selisihBulan);

     

            if ($selisihBulan != $totalDurasi) {
                if ($waktuMagang > 0) {
                    return redirect()->back()
                        ->with(AlertHelper::error(
                            "Periode tanggal batch pada {$namaKelas} - {$typeKelas} tidak valid. Durasi seharusnya {$totalDurasi} bulan ({$durasiBelajar} bulan + {$waktuMagang} bulan magang), tapi yang diinput {$selisihBulan} bulan",
                            'Error'
                        ))
                        ->withInput();
                } else {
                    return redirect()->back()
                        ->with(AlertHelper::error(
                            "Periode tanggal batch pada {$namaKelas} - {$typeKelas} tidak valid. Durasi seharusnya {$durasiBelajar} bulan, tapi yang diinput {$selisihBulan} bulan",
                            'Error'
                        ))
                        ->withInput();
                }
            }

            $activeBatch = Batches::where('status', 'active')
                ->where('kelas_id', $request->kelas_id)
                ->first();

            if ($activeBatch) {
                return redirect()->back()
                    ->with(AlertHelper::error(
                        "Gagal menambahkan batch. Sudah ada batch yang aktif untuk kelas: {$namaKelas}",
                        'Error'
                    ))
                    ->withInput();
            }
        }

        if ($request->status === 'registration') {
            $registrationBatch = Batches::where('status', 'registration')
                ->where('kelas_id', $request->kelas_id)
                ->first();

            if ($registrationBatch) {
                return redirect()->back()
                    ->with(AlertHelper::error(
                        "Gagal menambahkan batch. Sudah ada batch yang sedang registrasi untuk kelas: {$namaKelas}",
                        'Error'
                    ))
                    ->withInput();
            }
        }

        try {
            Batches::create([
                'nama'            => $request->nama,
                'status'          => $request->status,
                'kelas_id'        => $request->kelas_id,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
            ]);

            return redirect()->back()
                ->with(AlertHelper::success('Batch berhasil ditambahkan!', 'Success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(AlertHelper::error('Terjadi kesalahan saat menambah batch.', 'Error'))
                ->withInput();
        }
    }


    /**
     * Update the specified batch
     */
   public function update(Request $request, $id) {
    Log::info('Mulai proses update batch', [
        'id' => $id,
        'request' => $request->all()
    ]);

    $validator = Validator::make($request->all(), [
        'nama' => 'required|string|max:255',
        'status' => 'required|in:active,inactive,finished,registration',
        'tanggal_mulai'   => 'required_if:status,active|nullable|date',
        'tanggal_selesai' => 'required_if:status,active|nullable|date|after:tanggal_mulai',
    ]);

    if ($request->kelas_id) {
        Log::warning('Percobaan ubah kelas_id terdeteksi', [
            'kelas_id_dikirim' => $request->kelas_id
        ]);
        return back()->withErrors(AlertHelper::error('Kelas tidak bisa dirubah', 'Error'));
    }

    if ($validator->fails()) {
        Log::warning('Validasi gagal', [
            'errors' => $validator->errors()->toArray()
        ]);
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    Log::info('Validasi berhasil');

    $batch = Batches::findOrFail($id);
    Log::info('Batch ditemukan', ['batch' => $batch]);

    $kelas = Kelas::where('id', $batch->kelas_id)->first();
    Log::info('Data kelas terkait batch', ['kelas' => $kelas]);

    $namaKelas = $kelas ? $kelas->nama : 'Kelas tidak ditemukan';
    $typeKelas = $kelas ? $kelas->type : '-';

    if ($request->status === 'active') {
        Log::info('Status update: active');

        if ($request->tanggal_mulai && $request->tanggal_selesai) {
            $durasiBelajar = (int) ($kelas->durasi_belajar ?? 0);
            $waktuMagang   = (int) ($kelas->waktu_magang ?? 0);
            $totalDurasi   = $durasiBelajar + $waktuMagang;

            $mulai   = \Carbon\Carbon::parse($request->tanggal_mulai);
            $selesai = \Carbon\Carbon::parse($request->tanggal_selesai);

            $selisihBulan = $mulai->diffInMonths($selesai);
            Log::info('Perhitungan durasi batch', [
                'mulai' => $mulai,
                'selesai' => $selesai,
                'selisih_bulan' => $selisihBulan,
                'total_durasi' => $totalDurasi
            ]);

            if ($selisihBulan != $totalDurasi) {
                Log::warning('Durasi batch tidak sesuai', [
                    'kelas' => $namaKelas,
                    'selisih_bulan' => $selisihBulan,
                    'seharusnya' => $totalDurasi
                ]);
                if ($waktuMagang > 0) {
                    return redirect()->back()
                        ->with(AlertHelper::error(
                            "Periode tanggal batch pada {$namaKelas} - {$typeKelas} tidak valid. Durasi seharusnya {$totalDurasi} bulan ({$durasiBelajar} bulan + {$waktuMagang} bulan magang), tapi yang diinput {$selisihBulan} bulan",
                            'Error'
                        ))
                        ->withInput();
                } else {
                    return redirect()->back()
                        ->with(AlertHelper::error(
                            "Periode tanggal batch pada {$namaKelas} - {$typeKelas} tidak valid. Durasi seharusnya {$durasiBelajar} bulan, tapi yang diinput {$selisihBulan} bulan",
                            'Error'
                        ))
                        ->withInput();
                }
            }
        }

        $activeBatch = Batches::where('status', 'active')
            ->where('id', '!=', $id)
            ->where('kelas_id', $batch->kelas_id)
            ->first();

        Log::info('Cek batch aktif lain', ['activeBatch' => $activeBatch]);

        if ($activeBatch) {
            Log::warning('Sudah ada batch aktif lain', [
                'kelas_id' => $batch->kelas_id,
                'batch_id' => $activeBatch->id
            ]);
            return redirect()->back()
                ->with(AlertHelper::error(
                    "Gagal mengubah batch. Sudah ada batch yang aktif untuk kelas: {$namaKelas}",
                    'Error'
                ))
                ->withInput();
        }
    }

    if ($request->status === 'registration') {
        Log::info('Status update: registration');

        $registrationBatch = Batches::where('status', 'registration')
            ->where('id', '!=', $id)
            ->where('kelas_id', $batch->kelas_id)
            ->first();

        Log::info('Cek batch registrasi lain', ['registrationBatch' => $registrationBatch]);

        if ($registrationBatch) {
            Log::warning('Sudah ada batch registrasi lain', [
                'kelas_id' => $batch->kelas_id,
                'batch_id' => $registrationBatch->id
            ]);
            return redirect()->back()
                ->with(AlertHelper::error(
                    "Gagal mengubah batch. Sudah ada batch yang sedang registrasi untuk kelas: {$namaKelas}",
                    'Error'
                ))
                ->withInput();
        }
    }

    try {
        Log::info('Memulai transaksi database untuk update batch');
        DB::beginTransaction();

        $updateData = [
            'nama' => $request->nama,
            'status' => $request->status,
        ];

        if (
            ($request->status === 'active' || $request->status === 'registration') 
            && $request->tanggal_mulai 
            && $request->tanggal_selesai
        ) {
            $updateData['tanggal_mulai'] = $request->tanggal_mulai;
            $updateData['tanggal_selesai'] = $request->tanggal_selesai;
        } else if ($request->status === 'active' || $request->status === 'registration') {
            Log::warning('Tanggal batch kosong saat status aktif/registrasi', [
                'status' => $request->status,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai
            ]);
            return redirect()->back()
                ->with(AlertHelper::error(
                    "Harap mengisi periode batch jika status registration atau active",
                    'Error'
                ))
                ->withInput();
        }

        Log::info('Data yang akan diupdate', ['updateData' => $updateData]);

        $batch->update($updateData);
        Log::info('Batch berhasil diupdate', ['batch_id' => $batch->id]);

        if ($request->status === "finished") {
            Log::info('Status finished, mulai ubah siswa jadi alumni');

            $siswaDetails = $batch->siswaDetails;
            Log::info('Jumlah siswa di batch', ['jumlah' => $siswaDetails->count()]);

            if ($siswaDetails && $siswaDetails->count() > 0) {
                foreach ($siswaDetails as $siswaDetail) {
                    $siswaDetail->update(['status' => 'alumni']);
                    Log::info('Siswa diubah menjadi alumni', [
                        'siswa_id' => $siswaDetail->id
                    ]);
                }

                $jumlahSiswa = $siswaDetails->count();
                DB::commit();

                Log::info('Transaksi selesai, batch finished dan siswa diubah');

                return redirect()->back()
                    ->with(AlertHelper::success(
                        "Batch berhasil diperbarui menjadi finished! {$jumlahSiswa} siswa telah diubah statusnya menjadi alumni.",
                        'Success'
                    ));
            }
        }

        DB::commit();
        Log::info('Transaksi selesai, update berhasil');

        return redirect()->back()
            ->with(AlertHelper::success('Batch berhasil diperbarui!', 'Success'));

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Gagal update batch', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()->back()
            ->with(AlertHelper::error("Terjadi kesalahan saat mengubah batch. {$e->getMessage()}", 'Error'))
            ->withInput();
    }
}



   public function destroy($id)
{
    try {
        $batch = Batches::with(['siswaDetails.siswa', 'ujians'])->findOrFail($id);
        foreach ($batch->siswaDetails as $siswaDetail) {
            if ($siswaDetail->siswa) {
                $siswaDetail->siswa->delete();
            }
        }


        $batch->siswaDetails()->delete();


        $batch->ujians()->delete();


        $batch->delete();

        return redirect()->back()
            ->with(AlertHelper::success('Batch dan seluruh data terkait berhasil dihapus!', 'Success'));
    } catch (\Exception $e) {
        Log::error($e->getMessage());

        return redirect()->back()
            ->with(AlertHelper::error('Terjadi kesalahan saat menghapus batch.', 'Error'));
    }
}


}