<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use App\Models\SiswaDetail;
use App\Helpers\AlertHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PesertaController extends Controller
{
    public function index() {
        $user = Auth::user();
        $kelas = collect();
        
        if ($user->role === 'admin') {
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'hasilUjian.ujian'])
                ->get();
            $kelas = Kelas::all();
        } elseif ($user->role === 'pengajar') {
            $pengajarKelasId = $user->pengajarDetail->kelas_id ?? null;
            
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'hasilUjian.ujian'])
                ->whereHas('siswaDetail', function ($query) use ($pengajarKelasId) {
                    $query->where('kelas_id', $pengajarKelasId);
                })
                ->get();
                
            $kelas = Kelas::where('id', $pengajarKelasId)->get();
        } else {
            $siswas = collect();
        }

        $pesertaData = $siswas->map(function ($siswa) {
            return [
                'id' => $siswa->id,
                'avatar' => $siswa->avatar,
                'name' => $siswa->name,
                'email' => $siswa->email,
                'nama_lengkap' => $siswa->siswaDetail->nama_lengkap ?? '-',
                'kelas' => optional($siswa->siswaDetail->kelas)->nama ?? '-',
                'kelas_id' => $siswa->siswaDetail->kelas_id ?? null,
                'avatar' => $siswa->getAvatarHtml(),
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

        return view('Dashboard.Peserta', compact('pesertaData', 'kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'kelas_id' => 'required_if:admin_role,true|exists:kelas,id'
        ]);

        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            
            if ($user->role === 'admin') {
                $kelasId = $request->kelas_id;
            } elseif ($user->role === 'pengajar') {
                $kelasId = $user->pengajarDetail->kelas_id ?? null;
                
                if (!$kelasId) {
                    throw new \Exception('Pengajar belum memiliki kelas yang ditugaskan');
                }
            } else {
                throw new \Exception('Anda tidak memiliki akses untuk menambahkan peserta');
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
                'kelas_id' => $kelasId,
            ]);

            DB::commit();
            
            return redirect()->back()->with(AlertHelper::success('Peserta berhasil ditambahkan', 'Success'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(AlertHelper::error('Gagal menambahkan peserta: ' . $e->getMessage(), 'Error'));
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'kelas_id' => 'required_if:admin_role,true|exists:kelas,id'
        ]);

        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $siswa = User::siswa()->findOrFail($id);
            
            if ($user->role === 'pengajar') {
                $pengajarKelasId = $user->pengajarDetail->kelas_id ?? null;
                $siswaKelasId = $siswa->siswaDetail->kelas_id ?? null;
                
                if ($pengajarKelasId !== $siswaKelasId) {
                    throw new \Exception('Anda tidak memiliki akses untuk mengubah peserta ini');
                }
            }

            $siswa->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->filled('password')) {
                $siswa->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            $siswaDetail = $siswa->siswaDetail;
            if ($siswaDetail) {
                $updateData = ['nama_lengkap' => $request->nama_lengkap];
                if ($user->role === 'admin' && $request->filled('kelas_id')) {
                    $updateData['kelas_id'] = $request->kelas_id;
                }
                
                $siswaDetail->update($updateData);
            } else {
                $kelasId = $user->role === 'admin' ? $request->kelas_id : $user->pengajarDetail->kelas_id;
                
                SiswaDetail::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelasId,
                ]);
            }

            DB::commit();
            
            return redirect()->back()->with(AlertHelper::success('Data peserta berhasil diupdate', 'Success'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with(AlertHelper::error('Gagal mengupdate peserta: ' . $e->getMessage(), 'Error'));
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        
        try {
            $user = Auth::user();
            $siswa = User::siswa()->findOrFail($id);
            
            if ($user->role === 'pengajar') {
                $pengajarKelasId = $user->pengajarDetail->kelas_id ?? null;
                $siswaKelasId = $siswa->siswaDetail->kelas_id ?? null;
                
                if ($pengajarKelasId !== $siswaKelasId) {
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

    
    public function getPesertaData()
    {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'hasilUjian.ujian'])
                ->get();
        } elseif ($user->role === 'pengajar') {
            $pengajarKelasId = $user->pengajarDetail->kelas_id ?? null;
            
            $siswas = User::siswa()
                ->with(['siswaDetail.kelas', 'hasilUjian.ujian'])
                ->whereHas('siswaDetail', function ($query) use ($pengajarKelasId) {
                    $query->where('kelas_id', $pengajarKelasId);
                })
                ->get();
        } else {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $data = $siswas->map(function ($siswa) {
            return [
                'id' => $siswa->id,
                'avatar' => $siswa->avatar,
                'name' => $siswa->name,
                'nama_lengkap' => $siswa->siswaDetail->nama_lengkap ?? '-',
                'kelas' => optional($siswa->siswaDetail->kelas)->nama ?? '-',
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