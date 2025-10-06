<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Batches;
use App\Models\HasilUjian;
use App\Models\Kelas;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
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
            $batch = Batches::with([
                'kelas',
                'siswaDetails.siswa',
                'siswaDetails.pembayarans',
                'materis',
                'ujians.soals',
                'ujians.hasilUjians'
            ])->findOrFail($id);
            
            $siswaData = $batch->siswaDetails->map(function ($siswaDetail) use ($batch) {
                $siswa = $siswaDetail->siswa;
                if (!$siswa) return null;
                
                $hasilUjians = HasilUjian::where('siswa_id', $siswa->id)
                    ->whereHas('ujian', function($q) use ($batch) {
                        $q->where('batch_id', $batch->id);
                    })
                    ->get();
                
                $rataRata = $hasilUjians->count() > 0 
                    ? number_format($hasilUjians->avg('nilai'), 1) 
                    : '0.0';
                
                return [
                    'id' => $siswa->id,
                    'nama' => $siswaDetail->nama_lengkap ?? $siswa->name,
                    'email' => $siswa->email,
                    'avatar' => $siswa->getAvatarUrl(),
                    'rata_rata' => $rataRata,
                    'status' => $siswaDetail->status,
                    'ikut_magang' => $siswaDetail->ikut_magang ?? '-',
                ];
            })->filter()->values();
            
            // Data Materi
            $materiData = $batch->materis->map(function ($materi) {
                return [
                    'id' => $materi->id,
                    'judul' => $materi->judul,
                    'created_at' => $materi->created_at->format('d M Y'),
                ];
            });
            
            // Data Pembayaran (hanya yang disetujui)
            $pembayaranData = Pembayaran::whereHas('siswaDetail', function($q) use ($batch) {
                    $q->where('batch_id', $batch->id);
                })
                ->where('status', 'disetujui')
                ->with('siswaDetail.siswa')
                ->get()
                ->map(function ($pembayaran) {
                    $siswaDetail = $pembayaran->siswaDetail;
                    return [
                        'id' => $pembayaran->id,
                        'siswa' => $siswaDetail->nama_lengkap ?? $siswaDetail->siswa->name,
                        'jumlah' => 'Rp ' . number_format($pembayaran->jumlah_dibayar, 0, ',', '.'),
                        'cicilan_ke' => $pembayaran->cicilan_ke,
                        'tanggal' => \Carbon\Carbon::parse($pembayaran->updated_at)->format('d M Y'),
                    ];
                });
            
            // Data Ujian
            $ujianData = $batch->ujians->map(function ($ujian) {
                $totalSoal = $ujian->soals->count();
                $totalSiswa = $ujian->hasilUjians->count();
                $rataRata = $totalSiswa > 0 
                    ? number_format($ujian->hasilUjians->avg('nilai'), 1) 
                    : '0.0';
                
                return [
                    'id' => $ujian->id,
                    'judul' => $ujian->judul,
                    'total_soal' => $totalSoal,
                    'total_siswa' => $totalSiswa,
                    'rata_rata' => $rataRata,
                    'status' => $ujian->status,
                ];
            });
            
            // Data untuk Chart - Rata-rata nilai per ujian
            $chartData = $batch->ujians->map(function ($ujian) {
                $totalSiswa = $ujian->hasilUjians->count();
                $rataRata = $totalSiswa > 0 
                    ? $ujian->hasilUjians->avg('nilai') 
                    : 0;
                
                return [
                    'judul' => $ujian->judul,
                    'rata_rata' => round($rataRata, 1),
                ];
            })->filter(function($item) {
                return $item['rata_rata'] > 0;
            })->values();
            
            // Statistik
            $totalSiswa = $batch->siswaDetails->count();
            $totalMateri = $batch->materis->count();
            $totalUjian = $batch->ujians->count();
            $totalPembayaranDisetujui = $pembayaranData->count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $batch->id,
                    'nama' => $batch->nama,
                    'kelas' => $batch->kelas->nama,
                    'status' => $batch->status,
                    'tanggal_mulai' => $batch->tanggal_mulai ? \Carbon\Carbon::parse($batch->tanggal_mulai)->format('d F Y') : '-',
                    'tanggal_selesai' => $batch->tanggal_selesai ? \Carbon\Carbon::parse($batch->tanggal_selesai)->format('d F Y') : '-',
                    'periode' => $batch->tanggal_mulai && $batch->tanggal_selesai 
                        ? \Carbon\Carbon::parse($batch->tanggal_mulai)->format('d M Y') . ' - ' . \Carbon\Carbon::parse($batch->tanggal_selesai)->format('d M Y')
                        : '-',
                    // Statistik
                    'total_siswa' => $totalSiswa,
                    'total_materi' => $totalMateri,
                    'total_ujian' => $totalUjian,
                    'total_pembayaran' => $totalPembayaranDisetujui,
                    // Detail Data
                    'siswa' => $siswaData,
                    'materi' => $materiData,
                    'pembayaran' => $pembayaranData,
                    'ujian' => $ujianData,
                    'chart_data' => $chartData,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data batch tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    public function downloadPdf($id)
    {
        try {
            $response = $this->show($id);
            $data = json_decode($response->content(), true)['data'];
            
            $pdf = Pdf::loadView('pdf.batch-detail', ['batch' => $data]);
            
            return $pdf->download('batch_' . $data['nama'] . '_' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with(AlertHelper::error('Gagal generate PDF: ' . $e->getMessage(), 'Error'));
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