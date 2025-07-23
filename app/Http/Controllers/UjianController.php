<?php

namespace App\Http\Controllers;

use App\Models\Ujian;
use App\Models\Soal;
use App\Models\JawabanSiswa;
use App\Models\HasilUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UjianController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== "siswa") {
            return redirect()->back()->with('error', 'Akses ditolak');
        }

        $ujians = Ujian::with([
            'kelas',
            'soals',
            'hasilUjians' => function($query) {
                $query->where('siswa_id', Auth::id());
            }
        ])
        ->where('status', 'active')
        ->where('kelas_id', optional(Auth::user()->siswaDetail)->kelas_id)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('Dashboard.Ujian', compact('ujians'));
    }

    public function mulai($slug) {
    
    $ujian = Ujian::with(['soals.jawabans', 'kelas'])
        ->where('status', 'active')
        ->where('kelas_id', optional(Auth::user()->siswaDetail)->kelas_id)
        ->get()
        ->first(function ($item) use ($slug) {
            return Str::slug($item->judul) === $slug;
        });

    if (!$ujian) {
        return redirect()->route('ujian.index')->with('alert', [
            'type' => 'error',
            'title' => 'Ujian Tidak Ditemukan',
            'message' => 'Ujian yang Anda cari tidak tersedia.'
        ]);
    }

    $isCompleted = HasilUjian::where('siswa_id', Auth::id())
        ->where('ujian_id', $ujian->id)
        ->exists();
        
    if ($isCompleted) {
        return redirect()->route('ujian.selesai', $slug)->with('alert', [
            'type' => 'info', 
            'title' => 'Ujian Sudah Selesai',
            'message' => 'Anda sudah menyelesaikan ujian ini.'
        ]);
    }

    $existingAnswers = JawabanSiswa::where('siswa_id', Auth::id())
        ->where('ujian_id', $ujian->id)
        ->pluck('jawaban_pilihan', 'soal_id')
        ->toArray();

    return view('Dashboard.ujian-mulai', compact('ujian', 'existingAnswers'));
}

    public function store(Request $request, $slug)
    {
        $ujian = Ujian::with(['soals.jawabans'])
            ->where('status', 'active')
            ->where('kelas_id', optional(Auth::user()->siswaDetail)->kelas_id)
            ->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->judul) === $slug;
            });

        if (!$ujian) {
            return response()->json(['error' => 'Ujian tidak ditemukan'], 404);
        }
        $isCompleted = HasilUjian::where('siswa_id', Auth::id())
            ->where('ujian_id', $ujian->id)
            ->exists();

        if ($isCompleted) {
            return response()->json(['error' => 'Ujian sudah diselesaikan'], 400);
        }

        $answers = $request->input('answers', []);
        $waktuPengerjaan = $request->input('waktu_pengerjaan', 0); 

        DB::beginTransaction();
        try {
            JawabanSiswa::where('siswa_id', Auth::id())
                ->where('ujian_id', $ujian->id)
                ->delete();

            $jumlahBenar = 0;
            $jumlahSalah = 0;
            foreach ($ujian->soals as $soal) {
                $jawabanPilihan = $answers[$soal->id] ?? null;
                $benar = false;

                if ($jawabanPilihan) {
                    $jawabanBenar = $soal->jawabans()->where('benar', true)->first();
                    $benar = $jawabanBenar && $jawabanBenar->pilihan === $jawabanPilihan;
                    
                    if ($benar) {
                        $jumlahBenar++;
                    } else {
                        $jumlahSalah++;
                    }
                } else {
                    $jumlahSalah++;
                }
                JawabanSiswa::create([
                    'siswa_id' => Auth::id(),
                    'ujian_id' => $ujian->id,
                    'soal_id' => $soal->id,
                    'jawaban_pilihan' => $jawabanPilihan,
                    'benar' => $benar,
                ]);
            }
            $totalSoal = $ujian->soals->count();
            $nilai = $totalSoal > 0 ? round(($jumlahBenar / $totalSoal) * 100, 2) : 0;
            HasilUjian::create([
                'siswa_id' => Auth::id(),
                'ujian_id' => $ujian->id,
                'jumlah_benar' => $jumlahBenar,
                'jumlah_salah' => $jumlahSalah,
                'nilai' => $nilai,
                'waktu_pengerjaan' => $waktuPengerjaan,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('ujian.selesai', Str::slug($ujian->judul))
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Terjadi kesalahan saat menyimpan jawaban'], 500);
        }
    }

  public function selesai($slug) {
    $ujian = Ujian::with(['kelas', 'soals'])
        ->where('status', 'active')
        ->where('kelas_id', optional(Auth::user()->siswaDetail)->kelas_id)
        ->get()
        ->first(function ($item) use ($slug) {
            return Str::slug($item->judul) === $slug;
        });

    if (!$ujian) {
        return redirect()->route('ujian.index')->with('alert', [
            'type' => 'error',
            'title' => 'Ujian Tidak Ditemukan',
            'message' => 'Ujian yang Anda cari tidak tersedia.'
        ]);
    }

    $hasil = HasilUjian::where('siswa_id', Auth::id())
        ->where('ujian_id', $ujian->id)
        ->first();

    if (!$hasil) {
        return redirect()->route('ujian.index')->with('alert', [
            'type' => 'warning',
            'title' => 'Hasil Tidak Ditemukan',
            'message' => 'Anda belum menyelesaikan ujian ini.'
        ]);
    }

    $jawabanDetails = JawabanSiswa::with(['soal.jawabans'])
        ->where('siswa_id', Auth::id())
        ->where('ujian_id', $ujian->id)
        ->get();

    return view('Dashboard.ujian-selesai', compact('ujian', 'hasil', 'jawabanDetails'));
}

    public function saveProgress(Request $request, $slug)
    {
        $ujian = Ujian::where('status', 'active')
            ->where('kelas_id', optional(Auth::user()->siswaDetail)->kelas_id)
            ->get()
            ->first(function ($item) use ($slug) {
                return Str::slug($item->judul) === $slug;
            });

        if (!$ujian) {
            return response()->json(['error' => 'Ujian tidak ditemukan'], 404);
        }

        $soalId = $request->input('soal_id');
        $jawaban = $request->input('jawaban');

        // Save or update answer
        JawabanSiswa::updateOrCreate(
            [
                'siswa_id' => Auth::id(),
                'ujian_id' => $ujian->id,
                'soal_id' => $soalId,
            ],
            [
                'jawaban_pilihan' => $jawaban,
                'benar' => false, // Will be calculated on submit
            ]
        );

        return response()->json(['success' => true]);
    }
}