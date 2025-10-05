<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function leaderboard()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                $data = $this->getAdminLeaderboardData();
                break;
            case 'pengajar':
                $data = $this->getPengajarLeaderboardData($user);
                break;
            default:
                $data = [];
        }
        
        return view('Dashboard.Leaderboard', compact('data', 'user'));
    }

    public function show($siswaId)
    {
        try {
            $user = Auth::user();
            $siswa = User::findOrFail($siswaId);
            

            if ($user->role === 'pengajar') {
                $pengajarDetail = $user->pengajarDetail;
                if (!$pengajarDetail) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access'
                    ], 403);
                }
                
                $siswaKelasId = optional($siswa->siswaDetail)->kelas_id;
                $allowedKelasIds = $pengajarDetail->kelas()->pluck('kelas.id')->toArray();
                
                if (!in_array($siswaKelasId, $allowedKelasIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access to this student'
                    ], 403);
                }
            }
            

            $kelasId = optional($siswa->siswaDetail)->kelas_id;
            if (!$kelasId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa belum memiliki kelas'
                ], 404);
            }
            

            $hasilUjians = $siswa->hasilUjian()
                ->whereHas('ujian', function($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId)
                      ->whereHas('batch', function ($query) {
                          $query->where('status', 'active');
                      });
                })
                ->with('ujian')
                ->orderBy('created_at', 'asc')
                ->get();

            $chartData = [];
            $tableData = [];
            
            foreach ($hasilUjians as $hasil) {
                $chartData[] = [
                    'judul' => $hasil->ujian->judul,
                    'nilai' => $hasil->nilai
                ];
                
                $tableData[] = [
                    'judul' => $hasil->ujian->judul,
                    'nilai' => $hasil->nilai,
                    'benar' => $hasil->jumlah_benar,
                    'salah' => $hasil->jumlah_salah
                ];
            }

            $rataRata = $hasilUjians->count() > 0 
                ? number_format($hasilUjians->avg('nilai'), 1)
                : '0.0';
            
            $status = $this->getStatusPerubahan($hasilUjians);
            $kelas = Kelas::find($kelasId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $siswa->id,
                    'nama' => $siswa->siswaDetail ? $siswa->siswaDetail->nama_lengkap : $siswa->name,
                    'avatar' => $siswa->getAvatarUrl(),
                    'kelas' => $kelas ? $kelas->nama : '-',
                    'rata_rata' => $rataRata,
                    'status' => $status,
                    'chart_ujian' => $chartData,
                    'table_data' => $tableData
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }

    private function getAdminLeaderboardData()
    {
        $result = [];
        
        $kelasList = Kelas::all();
        
        foreach ($kelasList as $kelas) {
            $siswaList = User::where('role', 'siswa')
                ->whereHas('siswaDetail', function($q) use ($kelas) {
                    $q->where('kelas_id', $kelas->id);
                })
                ->with(['siswaDetail'])
                ->get();
            
            $kelasData = [];
            
            foreach ($siswaList as $siswa) {
                $hasilUjians = $siswa->hasilUjian()
                    ->whereHas('ujian', function($q) use ($kelas) {
                        $q->where('kelas_id', $kelas->id)
                          ->whereHas('batch', function ($query) {
                              $query->where('status', 'active');
                          });
                    })
                    ->with('ujian')
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                if ($hasilUjians->count() > 0) {
                    $rataRata = $hasilUjians->avg('nilai');
                    $status = $this->getStatusPerubahan($hasilUjians);
                    
                    $kelasData[] = [
                        'id' => $siswa->id,
                        'nama' => $siswa->siswaDetail ? $siswa->siswaDetail->nama_lengkap : $siswa->name,
                        'avatar' => $siswa->getAvatarUrl(),
                        'rata_rata' => number_format($rataRata, 1),
                        'status' => $status,
                    ];
                }
            }
            
            usort($kelasData, function($a, $b) {
                return (float)$b['rata_rata'] <=> (float)$a['rata_rata'];
            });
            
            if (!empty($kelasData)) {
                $result[$kelas->nama] = $kelasData;
            }
        }
        
        return $result;
    }

    private function getPengajarLeaderboardData($user)
    {
        $result = [];
        
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return $result;
        }
        
        $kelasList = $pengajarDetail->kelas()->get();
        if ($kelasList->isEmpty()) {
            return $result;
        }
        
        foreach ($kelasList as $kelas) {
            $siswaList = User::where('role', 'siswa')
                ->whereHas('siswaDetail', function($q) use ($kelas) {
                    $q->where('kelas_id', $kelas->id);
                })
                ->with(['siswaDetail'])
                ->get();
            
            $kelasData = [];
            
            foreach ($siswaList as $siswa) {
                $hasilUjians = $siswa->hasilUjian()
                    ->whereHas('ujian', function($q) use ($kelas) {
                        $q->where('kelas_id', $kelas->id)
                          ->whereHas('batch', function ($query) {
                              $query->where('status', 'active');
                          });
                    })
                    ->with('ujian')
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                if ($hasilUjians->count() > 0) {
                    $rataRata = $hasilUjians->avg('nilai');
                    $status = $this->getStatusPerubahan($hasilUjians);
                    
                    $kelasData[] = [
                        'id' => $siswa->id,
                        'nama' => $siswa->siswaDetail ? $siswa->siswaDetail->nama_lengkap : $siswa->name,
                        'avatar' => $siswa->getAvatarUrl(),
                        'rata_rata' => number_format($rataRata, 1),
                        'status' => $status,
                    ];
                }
            }
            
            usort($kelasData, function($a, $b) {
                return (float)$b['rata_rata'] <=> (float)$a['rata_rata'];
            });
            
            if (!empty($kelasData)) {
                $result[$kelas->nama] = $kelasData;
            }
        }
        
        return $result;
    }

    private function getStatusPerubahan($hasilUjians)
    {
        if ($hasilUjians->count() < 2) {
            return "Data ujian belum mencukupi untuk analisis perubahan";
        }
        
        $nilaiTerbaru = $hasilUjians->last()->nilai;
        $nilaiSebelumnya = $hasilUjians->slice(-2, 1)->first()->nilai;
        
        if ($nilaiTerbaru > $nilaiSebelumnya) {
            return "Mengalami peningkatan nilai dari materi sebelumnya";
        } elseif ($nilaiTerbaru < $nilaiSebelumnya) {
            return "Mengalami penurunan nilai dari materi sebelumnya";
        } else {
            return "Nilai tetap stabil dari materi sebelumnya";
        }
    }
}