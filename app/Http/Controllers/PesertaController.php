<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Kelas;
use App\Models\SiswaDetail;
use App\Models\Batches;
use App\Helpers\AlertHelper;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Log;

class PesertaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $kelas = collect();
        
        if ($user->role === 'admin') {
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'siswaDetail.batches', 'hasilUjian.ujian'])
                ->get();
            $kelas = Kelas::all();
        } elseif ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
            
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'siswaDetail.batches', 'hasilUjian.ujian'])
                ->whereHas('siswaDetail', function ($query) use ($kelasIds) {
                    $query->whereIn('kelas_id', $kelasIds);
                })
                ->get();
                
            $kelas = Kelas::whereIn('id', $kelasIds)->get();
        } else {
            $siswas = collect();
        }

        $siswas = $siswas->sortByDesc(function ($siswa) {
            $siswaDetail = $siswa->siswaDetail;
            $batch = $siswaDetail?->batches;

            if (!$batch || $batch->status !== 'active') {
                return -1; 
            }

            preg_match('/\d+/', $batch->nama, $matches);
            return isset($matches[0]) ? (int) $matches[0] : 0;
        })->values();


        $pesertaData = $siswas->map(function ($siswa) {
            return [
                'id' => $siswa->id,
                'avatar' => $siswa->getAvatarHtml(),
                'name' => $siswa->name,
                'email' => $siswa->email,
                'nama_lengkap' => $siswa->siswaDetail->nama_lengkap ?? '-',
                'kelas' => $siswa->siswaDetail?->kelas?->nama ?? '-',
                'kelas_id' => $siswa->siswaDetail->kelas_id ?? null,
                'batch' => $siswa->siswaDetail?->batches?->nama ?? '-',
                'ikut_magang' => $siswa->siswaDetail?->ikut_magang ?? '-',
                'status' => match ($siswa->siswaDetail?->status) {
                    'active' => '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Active</span>',
                    'alumni' => '<span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Alumni</span>',
                     default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Inactive</span>',
                },
                'batch_id' => $siswa->siswaDetail->batch_id ?? null,
                'hasil' => $siswa->hasilUjian->map(function ($hasil) {
                    return [
                        'id' => $hasil->id,
                        'judul' => $hasil->ujian->judul ?? '-',
                        'nilai' => $hasil->nilai,
                        'waktu' => $this->formatWaktuPengerjaanDetik($hasil->waktu_pengerjaan),
                        'benar' => $hasil->jumlah_benar,
                        'salah' => $hasil->jumlah_salah
                    ];
                })
            ];
        });

      $activeBatch = Batches::with('kelas')
        ->where('status', 'registration')
        ->get()
        ->map(function ($batch) {
            return (object)[
                'display_name' => $batch->nama . ' - ' . $batch->kelas->nama
            ];
        });

            Log::info($activeBatch);
        

        return view('Dashboard.Peserta', compact('pesertaData', 'kelas', 'activeBatch'));
    }

    
    public function show($id)
    {
        try {
            $user = Auth::user();
            $siswa = User::siswa()->with(['siswaDetail.kelas', 'siswaDetail.batches', 'hasilUjian.ujian'])->findOrFail($id);

            if ($user->role === 'pengajar') {
                $pengajarDetail = $user->pengajarDetail;
                $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
                $siswaKelasId = $siswa->siswaDetail->kelas_id ?? null;

                if (!in_array($siswaKelasId, $kelasIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized'
                    ], 403);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $siswa->id,
                    'name' => $siswa->name,
                    'email' => $siswa->email,
                    'nama_lengkap' => $siswa->siswaDetail->nama_lengkap ?? '',
                    'kelas_id' => $siswa->siswaDetail->kelas_id ?? null,
                    'batch_id' => $siswa->siswaDetail->batch_id ?? null,
                    'status' => $siswa->siswaDetail->status ?? 'inactive',
                    'ikut_magang' => $siswa->siswaDetail->ikut_magang ?? '',
                    'kelas' => $siswa->siswaDetail?->kelas ? [
                        'id' => $siswa->siswaDetail->kelas->id,
                        'nama' => $siswa->siswaDetail->kelas->nama
                    ] : null,
                    'batch' => $siswa->siswaDetail?->batches ? [
                        'id' => $siswa->siswaDetail->batches->id,
                        'nama' => $siswa->siswaDetail->batches->nama
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data peserta tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function approval()
    {
        return view('Dashboard.Approval');
    }


    private function formatWaktuPengerjaanDetik($waktuDetik) {
        if ($waktuDetik < 60) {
            return $waktuDetik . ' detik';
        }
        
        $menit = floor($waktuDetik / 60);
        $sisaDetik = $waktuDetik % 60;
        
        if ($menit < 60) {
            if ($sisaDetik == 0) {
                return $menit . ' menit';
            }
            return $menit . ' menit ' . $sisaDetik . ' detik';
        }
        
        $jam = floor($menit / 60);
        $sisaMenit = $menit % 60;
        
        $result = $jam . ' jam';
        if ($sisaMenit > 0) {
            $result .= ' ' . $sisaMenit . ' menit';
        }
        if ($sisaDetik > 0) {
            $result .= ' ' . $sisaDetik . ' detik';
        }
        
        return $result;
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            if ($user->role === 'pengajar') {
                $pengajarDetail = $user->pengajarDetail;
                $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
                
                if (!in_array($request->kelas_id, $kelasIds)) {
                    throw new \Exception('Anda tidak memiliki akses untuk menambahkan peserta ke kelas ini');
                }
            }

            if($request->kelas_id) {

              $activeBatch = Batches::where('kelas_id', $request->kelas_id)
                      ->where('status', 'registration')
                      ->first();

              if (!$activeBatch) {
                    $kelas = Kelas::find($request->kelas_id);
                    $namaKelas = $kelas ? $kelas->nama : 'Kelas tidak ditemukan';
                    throw new \Exception("Kelas $namaKelas belum memiliki batch yang statusnya Register. Harap ubah status batch menjadi register jika ingin menambahkan peserta pada batch tertentu terlebih dahulu.");
                }

            }

            $avatar = 'avatar-' . rand(1, 10) . '.png';
            $newUser = User::create([
                'avatar' => $avatar,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'siswa'
            ]);

            SiswaDetail::create([
                'siswa_id' => $newUser->id,
                'nama_lengkap' => $request->nama_lengkap,
                'kelas_id' => $request->kelas_id,
                'batch_id' => $activeBatch->id,
            ]);

            DB::commit();
            
            return redirect()->back()->with(AlertHelper::success('Peserta berhasil ditambahkan ke batch: ' . $activeBatch->nama . ' kelas '. $activeBatch->kelas->nama, 'Success'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(AlertHelper::error('Gagal menambahkan peserta: ' . $e->getMessage(), 'Error'));
        }
    }

    public function update(Request $request, $id) {
    $request->validate([
        'name' => 'required|string|max:255',
        'ikut_magang' => 'required|string|in:belum ditentukan,ikut,tidak',
        'email' => 'required|email|unique:users,email,' . $id,
        'nama_lengkap' => 'required|string|max:255',
        'password' => 'nullable|string|min:6',
    ]);

    DB::beginTransaction();

    try {
        $user = Auth::user(); 
        $siswa = User::siswa()->findOrFail($id); 

        if ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
            $siswaKelasId = $siswa->siswaDetail->kelas_id ?? null;

            if (!in_array($siswaKelasId, $kelasIds)) {
                throw new \Exception('Anda tidak memiliki akses untuk mengubah peserta ini.');
            }
        }

        $siswa->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $siswa->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $siswaDetail = $siswa->siswaDetail;
        if ($siswaDetail) {
            $ikutMagangLama = $siswaDetail->ikut_magang;
            $ikutMagangBaru = $request->ikut_magang;

            // Update data siswa detail
            $siswaDetail->update([
                'nama_lengkap' => $request->nama_lengkap,
                'ikut_magang' => $ikutMagangBaru,
            ]);

            // Jika status magang berubah dari "belum ditentukan" atau "ikut" menjadi "tidak"
            if ($ikutMagangLama !== 'tidak' && $ikutMagangBaru === 'tidak') {
                $this->hitungUlangTagihan($siswaDetail);
            }
            
            // Jika status magang berubah dari "tidak" menjadi "ikut"
            if ($ikutMagangLama === 'tidak' && $ikutMagangBaru === 'ikut') {
                $this->kembalikanTagihanNormal($siswaDetail);
            }
        } else {
            SiswaDetail::create([
                'siswa_id' => $siswa->id,
                'nama_lengkap' => $request->nama_lengkap,
                'ikut_magang' => $request->ikut_magang,
            ]);
        }

        DB::commit();

        return redirect()->back()->with(AlertHelper::success('Data peserta berhasil diupdate.', 'Success'));
    } catch (\Exception $e) {
        DB::rollBack();

        return redirect()->back()->with(AlertHelper::error(
            'Gagal mengupdate peserta: ' . $e->getMessage(),
            'Error'
        ));
    }
}

/**
 * Hitung ulang tagihan ketika siswa tidak ikut magang
 */
    private function hitungUlangTagihan($siswaDetail)
    {
        $batch = $siswaDetail->batches;
        $kelas = $siswaDetail->kelas;
        
        // Hitung sisa cicilan yang belum dibayar
        $cicilanBelumDibayar = Pembayaran::where('siswa_detail_id', $siswaDetail->id)
            ->where('status', 'belum dibayar')
            ->count();
        
        // Hitung total sisa tagihan
        $sisaTagihan = $siswaDetail->total_tagihan;
        
        // Durasi belajar (dalam bulan)
        $tanggalMulai = \Carbon\Carbon::parse($batch->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($batch->tanggal_selesai);
        $totalBulan = $tanggalMulai->diffInMonths($tanggalSelesai);
        
        // Durasi magang (misalnya 2 bulan, bisa disesuaikan dengan data kelas)
        $durasiMagang = $kelas->durasi_magang ?? 2; // dalam bulan
        
        // Jumlah cicilan baru = total bulan - durasi magang
        $jumlahCicilanBaru = $totalBulan - $durasiMagang;
        
        // Hitung tagihan per bulan yang baru
        $tagihanPerBulanBaru = $cicilanBelumDibayar > 0 
            ? round($sisaTagihan / $cicilanBelumDibayar) 
            : 0;
        
        // Update siswa detail
        $siswaDetail->update([
            'jumlah_cicilan' => $jumlahCicilanBaru,
            'tagihan_per_bulan' => $tagihanPerBulanBaru,
        ]);
        
        // Update semua pembayaran yang belum dibayar dengan jumlah baru
        Pembayaran::where('siswa_detail_id', $siswaDetail->id)
            ->where('status', 'belum dibayar')
            ->update([
                'jumlah_dibayar' => $tagihanPerBulanBaru,
            ]);
        
        // Hapus pembayaran yang melebihi jumlah cicilan baru
        $pembayaranBelumBayar = Pembayaran::where('siswa_detail_id', $siswaDetail->id)
            ->where('status', 'belum dibayar')
            ->orderBy('cicilan_ke', 'desc')
            ->get();
        
        $jumlahHapus = $pembayaranBelumBayar->count() - $cicilanBelumDibayar;
        if ($jumlahHapus > 0) {
            $pembayaranBelumBayar->take($jumlahHapus)->each(function($pembayaran) {
                $pembayaran->delete();
            });
        }
    }


    private function kembalikanTagihanNormal($siswaDetail)
    {
        $batch = $siswaDetail->batches;
        
        // Hitung sisa tagihan
        $sisaTagihan = $siswaDetail->total_tagihan;
        
        // Durasi belajar + magang (dalam bulan)
        $tanggalMulai = \Carbon\Carbon::parse($batch->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($batch->tanggal_selesai);
        $totalBulan = $tanggalMulai->diffInMonths($tanggalSelesai);
        
        // Hitung sisa cicilan yang belum dibayar
        $cicilanBelumDibayar = Pembayaran::where('siswa_detail_id', $siswaDetail->id)
            ->where('status', 'belum dibayar')
            ->count();
        
        // Hitung berapa cicilan yang sudah dibayar
        $cicilanSudahDibayar = Pembayaran::where('siswa_detail_id', $siswaDetail->id)
            ->where('status', 'disetujui')
            ->count();
        
        // Jumlah cicilan normal
        $jumlahCicilanNormal = $totalBulan;
        
        // Tagihan per bulan normal
        $tagihanPerBulanNormal = $jumlahCicilanNormal > 0 
            ? round(($siswaDetail->kelas->harga * 0.6) / $jumlahCicilanNormal) 
            : 0;
        
        // Update siswa detail
        $siswaDetail->update([
            'jumlah_cicilan' => $jumlahCicilanNormal,
            'tagihan_per_bulan' => $tagihanPerBulanNormal,
        ]);
        
        // Update pembayaran yang belum dibayar
        Pembayaran::where('siswa_detail_id', $siswaDetail->id)
            ->where('status', 'belum dibayar')
            ->update([
                'jumlah_dibayar' => $tagihanPerBulanNormal,
            ]);
        
        // Generate pembayaran tambahan jika perlu (untuk bulan magang)
        $sisaCicilan = $jumlahCicilanNormal - $cicilanSudahDibayar - $cicilanBelumDibayar;
        
        if ($sisaCicilan > 0) {
            $pembayaranTerakhir = Pembayaran::where('siswa_detail_id', $siswaDetail->id)
                ->orderBy('cicilan_ke', 'desc')
                ->first();
            
            $cicilanKeTerakhir = $pembayaranTerakhir ? $pembayaranTerakhir->cicilan_ke : 0;
            $tanggalJatuhTempoTerakhir = $pembayaranTerakhir 
                ? \Carbon\Carbon::parse($pembayaranTerakhir->tanggal_jatuh_tempo)
                : \Carbon\Carbon::parse($batch->tanggal_mulai);
            
            for ($i = 1; $i <= $sisaCicilan; $i++) {
                $tanggalJatuhTempo = $tanggalJatuhTempoTerakhir->copy()->addMonth();
                
                Pembayaran::create([
                    'siswa_detail_id' => $siswaDetail->id,
                    'jumlah_dibayar' => $tagihanPerBulanNormal,
                    'status' => 'belum dibayar',
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo,
                    'cicilan_ke' => $cicilanKeTerakhir + $i,
                ]);
                
                $tanggalJatuhTempoTerakhir = $tanggalJatuhTempo;
            }
        }
    }


    public function destroy($id) {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $siswa = User::siswa()->findOrFail($id);
            
            if ($user->role === 'pengajar') {
                $pengajarDetail = $user->pengajarDetail;
                $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
                $siswaKelasId = $siswa->siswaDetail->kelas_id ?? null;
                
                if (!in_array($siswaKelasId, $kelasIds)) {
                    throw new \Exception('Anda tidak memiliki akses untuk menghapus peserta ini');
                }
            }

            $siswa->hasilUjian()->delete();
            $siswa->jawabanSiswa()->delete();
            $siswa->siswaDetail()->delete();
            $siswa->delete();

            DB::commit();
            
            return redirect()->back()->with(AlertHelper::success('Peserta berhasil dihapus', 'Success'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(AlertHelper::error('Gagal menghapus peserta: ' . $e->getMessage(), 'Error'));
        }
    }

    public function getPesertaData() {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'siswaDetail.batches', 'hasilUjian.ujian'])
                ->get();
        } elseif ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
            
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'siswaDetail.batches', 'hasilUjian.ujian'])
                ->whereHas('siswaDetail', function ($query) use ($kelasIds) {
                    $query->whereIn('kelas_id', $kelasIds);
                })
                ->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $siswas->map(function ($siswa) {
            return [
                'id' => $siswa->id,
                'avatar' => $siswa->getAvatarHtml(),
                'name' => $siswa->name,
                'nama_lengkap' => $siswa->siswaDetail->nama_lengkap ?? '-',
                'kelas' => optional($siswa->siswaDetail->kelas)->nama ?? '-',
                'batch' => optional($siswa->siswaDetail->batches)->nama ?? '-',
                'hasil' => $siswa->hasilUjian->map(function ($hasil) {
                    return [
                        'id' => $hasil->id,
                        'judul' => $hasil->ujian->judul ?? '-',
                        'nilai' => $hasil->nilai,
                        'waktu' => $hasil->waktu_pengerjaan,
                        'benar' => $hasil->jumlah_benar,
                        'salah' => $hasil->jumlah_salah
                    ];
                })
            ];
        });

        return response()->json($data);
    }
}