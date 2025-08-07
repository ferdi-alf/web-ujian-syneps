<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Batches;
use App\Models\Jawaban;
use App\Models\Kelas;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TambahUjianController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelas = collect();

        if ($user->role === 'admin') {
            $kelas = Kelas::select('id', 'nama')->get();
        } elseif ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
            $kelas = Kelas::select('id', 'nama')->whereIn('id', $kelasIds)->get();
        }

        return view('Dashboard.Tambah-Ujian', compact('kelas'));
    }

    public function store(Request $request) {
        Log::info('📝 Memulai proses penyimpanan ujian', [
            'user_id' => Auth::id(),
            'user_role' => Auth::user()->role,
            'data_count' => count($request->soal ?? [])
        ]);

        try {
            $this->updateProgress(5, 'Memulai validasi data...');
            sleep(1);

            $request->validate([
                'judul' => 'required|string|max:255',
                'kelas_id' => 'required|exists:kelas,id',
                'soal' => 'required|array|min:1',
                'soal.*.soal' => 'required|string',
                'soal.*.jawaban' => 'required|array|size:4',
                'soal.*.jawaban.*.pilihan' => 'required|in:A,B,C,D',
                'soal.*.jawaban.*.teks' => 'required|string',
                'soal.*.jawaban.*.benar' => 'required|boolean',
            ]);

            $this->updateProgress(15, 'Validasi data berhasil');
            Log::info('✅ Validasi data berhasil');
            sleep(0.5);

            $this->updateProgress(25, 'Memverifikasi akses pengguna...');
            sleep(0.8);

            $user = Auth::user();
            $kelasId = $request->kelas_id;

            if ($user->role === 'pengajar') {
                Log::info('👨‍🏫 User adalah pengajar');
                $pengajarDetail = $user->pengajarDetail;

                if (!$pengajarDetail) {
                    $this->updateProgress(0, 'Error: Pengajar belum memiliki kelas');
                    return response()->json([
                        'success' => false,
                        'message' => 'Pengajar belum memiliki kelas yang ditugaskan'
                    ], 400);
                }

                $kelasIds = $pengajarDetail->kelas()->pluck('kelas.id')->toArray();

                if (!in_array($kelasId, $kelasIds)) {
                    $this->updateProgress(0, 'Error: Akses ke kelas tidak diizinkan');
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses untuk membuat ujian di kelas ini'
                    ], 403);
                }
            } elseif ($user->role !== 'admin') {
                $this->updateProgress(0, 'Error: Akses ditolak');
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk membuat ujian'
                ], 403);
            }

            $this->updateProgress(35, 'Akses pengguna terverifikasi');
            sleep(0.5);

            $this->updateProgress(37, 'Memeriksa batch aktif...');
            $kelas = Kelas::find($request->kelas_id);

            $activeBatch = Batches::where('kelas_id', $request->kelas_id)
                ->where('status', 'active')
                ->first();

            if (!$activeBatch) {
                $message = "Kelas {$kelas->nama} belum memiliki batch aktif. Silakan aktifkan batch terlebih dahulu.";
                Log::error("❌ $message");

                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 400);
            }
            Log::info('✅ Batch aktif ditemukan', ['batch_id' => $activeBatch->id]);

            $this->updateProgress(40, 'Menyiapkan database...');
            sleep(0.7);

            DB::beginTransaction();
            Log::info('🔄 Database transaction dimulai');

            try {
                $this->updateProgress(45, 'Menyimpan informasi ujian...');
                sleep(0.8);

                $ujian = Ujian::create([
                    'judul' => $request->judul,
                    'kelas_id' => $kelasId,
                    'status' => 'pending',
                    'batch_id' => $activeBatch->id, // Assign active batch ID
                ]);

                $this->updateProgress(50, 'Ujian berhasil dibuat');
                Log::info('✅ Ujian berhasil disimpan', ['ujian_id' => $ujian->id]);
                sleep(0.5);

                $totalSoal = count($request->soal);
                $progressRange = 40;
                $progressPerSoal = $progressRange / $totalSoal;
                $currentProgress = 50;

                foreach ($request->soal as $index => $soalData) {
                    $soalNumber = $index + 1;

                    $this->updateProgress(
                        (int) $currentProgress,
                        "Memproses soal #{$soalNumber} dari {$totalSoal}..."
                    );

                    sleep(0.3 + (rand(0, 300) / 1000));

                    Log::info("📝 Menyimpan soal #{$soalNumber}");

                    $soal = Soal::create([
                        'ujian_id' => $ujian->id,
                        'soal' => $soalData['soal']
                    ]);

                    $jawabanBenarCount = collect($soalData['jawaban'])->where('benar', true)->count();

                    if ($jawabanBenarCount !== 1) {
                        throw new \Exception("Soal #{$soalNumber} harus memiliki tepat satu jawaban yang benar");
                    }

                    foreach ($soalData['jawaban'] as $jawabanData) {
                        Jawaban::create([
                            'soal_id' => $soal->id,
                            'pilihan' => $jawabanData['pilihan'],
                            'teks' => $jawabanData['teks'],
                            'benar' => $jawabanData['benar']
                        ]);
                    }

                    $currentProgress += $progressPerSoal;

                    Log::info("✅ Soal #{$soalNumber} selesai diproses");
                }

                $this->updateProgress(92, 'Menyelesaikan transaksi...');
                sleep(1);

                DB::commit();
                Log::info('🎉 Database transaction berhasil');

                $this->updateProgress(98, 'Membersihkan cache...');
                sleep(0.5);

                $this->updateProgress(100, '🎉 Ujian berhasil disimpan!');

                Log::info('🎯 Proses penyimpanan ujian selesai', [
                    'ujian_id' => $ujian->id,
                    'total_soal' => $totalSoal,
                    'batch_id' => $activeBatch->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Ujian berhasil disimpan',
                    'data' => [
                        'ujian_id' => $ujian->id,
                        'judul' => $ujian->judul,
                        'total_soal' => $totalSoal,
                        'batch_id' => $activeBatch->id
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                $this->updateProgress(0, 'Error: ' . $e->getMessage());
                Log::error('❌ Transaction rollback', ['error' => $e->getMessage()]);
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->updateProgress(0, 'Validasi data gagal');
            Log::error('❌ Validasi gagal', ['errors' => $e->errors()]);

            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            $this->updateProgress(0, 'Terjadi kesalahan: ' . $e->getMessage());
            Log::error('❌ Error umum', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateProgress($percentage, $message)
    {
        $sessionKey = 'ujian_progress_' . Auth::id() . '_' . session()->getId();

        $progressData = [
            'percentage' => max(0, min(100, (int) $percentage)),
            'message' => $message,
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id()
        ];

        Cache::put($sessionKey, $progressData, 600);

        Log::info("📊 Progress: {$percentage}% - {$message}");

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        if (ob_get_level()) {
            ob_flush();
        }
        flush();
    }

    public function getProgress()
    {
        $sessionKey = 'ujian_progress_' . Auth::id() . '_' . session()->getId();

        $progress = Cache::get($sessionKey, [
            'percentage' => 0,
            'message' => 'Menunggu proses dimulai...',
            'timestamp' => now()->toISOString(),
            'user_id' => Auth::id()
        ]);

        return response()->json($progress);
    }

    public function clearProgress()
    {
        $sessionKey = 'ujian_progress_' . Auth::id() . '_' . session()->getId();
        Cache::forget($sessionKey);

        Log::info('🧹 Progress cache cleared untuk user: ' . Auth::id());

        return response()->json([
            'success' => true,
            'message' => 'Progress cleared'
        ]);
    }
}

