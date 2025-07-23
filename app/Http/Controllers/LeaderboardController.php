<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    public function leaderboard() {
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

    private function getAdminLeaderboardData() {
        $result = [];
        
        $kelasList = Kelas::all();
        
        foreach ($kelasList as $kelas) {
            $siswaList = User::where('role', 'siswa')
                ->whereHas('siswaDetail', function($q) use ($kelas) {
                    $q->where('kelas_id', $kelas->id);
                })
                ->with(['siswaDetail', 'hasilUjian.ujian'])
                ->get();
            
            $kelasData = [];
            
            foreach ($siswaList as $siswa) {
                $hasilUjians = $siswa->hasilUjian()
                    ->whereHas('ujian', function($q) use ($kelas) {
                        $q->where('kelas_id', $kelas->id);
                    })
                    ->with('ujian')
                    ->orderBy('created_at', 'asc')
                    ->get();
                
                if ($hasilUjians->count() > 0) {
                    $rataRata = $hasilUjians->avg('nilai');
                    $status = $this->getStatusPerubahan($hasilUjians);
                    $chartUjian = [];
                    foreach ($hasilUjians as $hasil) {
                        $chartUjian[] = [
                            'judul' => $hasil->ujian->judul,
                            'nilai' => $hasil->nilai
                        ];
                    }
                    $tableData = [];
                    foreach ($hasilUjians as $hasil) {
                        $tableData[] = [
                            'id' => $hasil->id,
                            'judul' => $hasil->ujian->judul,
                            'nilai' => $hasil->nilai,
                            'benar' => $hasil->jumlah_benar,
                            'salah' => $hasil->jumlah_salah
                        ];
                    }
                    
                    $kelasData[] = [
                        'id' => $siswa->id,
                        'nama' => $siswa->siswaDetail ? $siswa->siswaDetail->nama_lengkap : $siswa->name,
                        'avatar' => $siswa->getAvatarUrl(),
                        'rata_rata' => number_format($rataRata, 1),
                        'status' => $status,
                        'chart_ujian' => $chartUjian,
                        'table_data' => $tableData
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

    private function getPengajarLeaderboardData($user) {
        $result = [];
        
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return $result;
        }
        
        $kelas = Kelas::find($pengajarDetail->kelas_id);
        if (!$kelas) {
            return $result;
        }
        
        $siswaList = User::where('role', 'siswa')
            ->whereHas('siswaDetail', function($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            })
            ->with(['siswaDetail', 'hasilUjian.ujian'])
            ->get();
        
        foreach ($siswaList as $siswa) {
            $hasilUjians = $siswa->hasilUjian()
                ->whereHas('ujian', function($q) use ($kelas) {
                    $q->where('kelas_id', $kelas->id);
                })
                ->with('ujian')
                ->orderBy('created_at', 'asc')
                ->get();
            
            if ($hasilUjians->count() > 0) {
                $rataRata = $hasilUjians->avg('nilai');
                $status = $this->getStatusPerubahan($hasilUjians);
                $chartUjian = [];
                foreach ($hasilUjians as $hasil) {
                    $chartUjian[] = [
                        'judul' => $hasil->ujian->judul,
                        'nilai' => $hasil->nilai
                    ];
                }
                $tableData = [];
                foreach ($hasilUjians as $hasil) {
                    $tableData[] = [
                        'id' => $hasil->id,
                        'judul' => $hasil->ujian->judul,
                        'nilai' => $hasil->nilai,
                        'benar' => $hasil->jumlah_benar,
                        'salah' => $hasil->jumlah_salah
                    ];
                }
                
                $result[] = [
                    'id' => $siswa->id,
                    'nama' => $siswa->siswaDetail ? $siswa->siswaDetail->nama_lengkap : $siswa->name,
                    'avatar' => $siswa->getAvatarUrl(),
                    'rata_rata' => number_format($rataRata, 1),
                    'status' => $status,
                    'chart_ujian' => $chartUjian,
                    'table_data' => $tableData
                ];
            }
        }
        usort($result, function($a, $b) {
            return (float)$b['rata_rata'] <=> (float)$a['rata_rata'];
        });
        
        return $result;
    }

    private function getStatusPerubahan($hasilUjians) {
        if ($hasilUjians->count() < 2) {
            return "Data ujian belum mencukupi untuk analisis perubahan";
        }
        
        $nilaiTerbaru = $hasilUjians->last()->nilai;
        $nilaiSebelumnya = $hasilUjians->slice(-2, 1)->first()->nilai;
        
        if ($nilaiTerbaru > $nilaiSebelumnya) {
            return "Mengalami peningkatan dari materi nilai dari materi sebelumnya";
        } elseif ($nilaiTerbaru < $nilaiSebelumnya) {
            return "Mengalami penurunan dari materi nilai dari materi sebelumnya";
        } else {
            return "Nilai tetap stabil dari materi sebelumnya";
        }
    }
}
