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
        ->where('status', 'active')
        ->get()
        ->map(function ($batch) {
            return (object)[
                'display_name' => $batch->nama . ' - ' . $batch->kelas->nama
            ];
        });

            Log::info($activeBatch);
        

        return view('Dashboard.Peserta', compact('pesertaData', 'kelas', 'activeBatch'));
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
                      ->where('status', 'active')
                      ->first();

              if (!$activeBatch) {
                    $kelas = Kelas::find($request->kelas_id);
                    $namaKelas = $kelas ? $kelas->nama : 'Kelas tidak ditemukan';
                    throw new \Exception("Kelas $namaKelas belum memiliki batch yang aktif. Harap aktifkan batch terlebih dahulu.");
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
                $siswaDetail->update([
                    'nama_lengkap' => $request->nama_lengkap,
                ]);
            } else {
                SiswaDetail::create([
                    'siswa_id' => $siswa->id,
                    'nama_lengkap' => $request->nama_lengkap,
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