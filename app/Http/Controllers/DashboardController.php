<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Ujian;
use App\Models\HasilUjian;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()  {         
        $user = Auth::user();
        
        $data = ['user' => $user];
        
        if (in_array($user->role, ['admin', 'pengajar'])) {
            $data['cardData'] = $this->getCardData($user);                  
            $data['chartData'] = $this->getStackedBarChartData($user);                  
            $data['recentSubmissions'] = $this->getRecentExamSubmissions($user);         
            $data['activeExamData'] = $this->getActiveExamData($user);
        } else {
            $data['chartData'] = $this->getSiswaChartData($user);
            $data['leaderboardData'] = $this->getSiswaLeaderboardData($user);
        }
        
        return view('Dashboard.Dashboard', $data);     
    }

    private function getCardData($user) {
        $data = [];
        
        if ($user->role === 'admin') {
            $totalSiswa = User::where('role', 'siswa')->count();
            $totalUjian = Ujian::count();
            $totalUjianActive = Ujian::where('status', 'active')->count();
            $totalKelas = Kelas::count();
            
            $data = [
                'total_peserta' => $totalSiswa,
                'total_ujian' => $totalUjian,
                'total_ujian_active' => $totalUjianActive,
                'total_kelas' => $totalKelas,
                'show_kelas_card' => true
            ];
        } else if ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelasId = $pengajarDetail ? $pengajarDetail->kelas_id : null;
            
            $totalSiswa = User::where('role', 'siswa')
                ->whereHas('siswaDetail', function($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                })->count();
            
            $totalUjian = Ujian::where('kelas_id', $kelasId)->count();
            $totalUjianActive = Ujian::where('kelas_id', $kelasId)
                ->where('status', 'active')->count();
            
            $data = [
                'total_peserta' => $totalSiswa,
                'total_ujian' => $totalUjian,
                'total_ujian_active' => $totalUjianActive,
                'show_kelas_card' => false
            ];
        }
        
        return $data;
    }

    private function getStackedBarChartData($user) {
        $currentYear = now()->year;

        if ($user->role === 'admin') {
            $months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            $currentMonth = now()->month;

            $chartData = [
                'labels' => $months,
                'datasets' => []
            ];

            $colors = [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
            ];

            $kelasList = Kelas::all();

            foreach ($kelasList as $index => $kelas) {
                $monthlyAverages = [];

                for ($month = 1; $month <= 12; $month++) {
                    if ($month <= $currentMonth) {
                        $average = HasilUjian::whereHas('siswa.siswaDetail', function ($q) use ($kelas) {
                                $q->where('kelas_id', $kelas->id);
                            })
                            ->whereHas('ujian', function ($q) use ($kelas) {
                                $q->where('kelas_id', $kelas->id);
                            })
                            ->whereMonth('created_at', $month)
                            ->whereYear('created_at', $currentYear)
                            ->avg('nilai');

                        $monthlyAverages[] = $average ? round($average, 1) : 0;
                    } else {
                        $monthlyAverages[] = 0;
                    }
                }

                $chartData['datasets'][] = [
                    'label' => $kelas->nama,
                    'data' => $monthlyAverages,
                    'backgroundColor' => $colors[$index % count($colors)],
                    'type' => 'bar'
                ];
            }
        } else if ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelas = Kelas::find($pengajarDetail->kelas_id);

            if (!$kelas) {
                return [
                    'labels' => [],
                    'datasets' => []
                ];
            }

            $ujianList = Ujian::where('kelas_id', $kelas->id)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($ujianList->isEmpty()) {
                return [
                    'labels' => ['Tidak ada ujian'],
                    'datasets' => []
                ];
            }

            $labels = $ujianList->map(function ($ujian) {
                return $ujian->nama ?? 'Ujian ' . $ujian->judul;
            })->toArray();

            $chartData = [
                'labels' => $labels,
                'datasets' => []
            ];

            $colors = ['#36A2EB'];

            $ujianAverages = [];

            foreach ($ujianList as $ujian) {
                $average = HasilUjian::whereHas('siswa.siswaDetail', function ($q) use ($kelas) {
                        $q->where('kelas_id', $kelas->id);
                    })
                    ->where('ujian_id', $ujian->id)
                    ->avg('nilai');

                $ujianAverages[] = $average ? round($average, 1) : 0;
            }

            $chartData['datasets'][] = [
                'label' => 'Rata-rata Nilai - ' . $kelas->nama,
                'data' => $ujianAverages,
                'backgroundColor' => $colors[0],
                'borderColor' => $colors[0],
                'fill' => false,
                'type' => 'line'
            ];
        }

        return $chartData;
    }

    private function getRecentExamSubmissions($user) {
        $twelveHoursAgo = now()->subHours(12);
        $activeExams = [];
        $recentSubmissions = [];

        if ($user->role === 'admin') {
            $activeExams = Ujian::where('status', 'active')->pluck('judul', 'id');

            $recentSubmissions = HasilUjian::with(['siswa', 'ujian'])
                ->whereHas('ujian', function ($q) {
                    $q->where('status', 'active');
                })
                ->where('created_at', '>=', $twelveHoursAgo)
                ->whereNotNull('siswa_id')
                ->orderBy('created_at', 'desc')
                ->get();
        } else if ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelasId = $pengajarDetail ? $pengajarDetail->kelas_id : null;

            if ($kelasId) {
                $activeExams = Ujian::where('status', 'active')
                    ->where('kelas_id', $kelasId)
                    ->pluck('judul', 'id');

                $recentSubmissions = HasilUjian::with(['siswa', 'ujian'])
                    ->whereHas('ujian', function ($q) use ($kelasId) {
                        $q->where('status', 'active')
                          ->where('kelas_id', $kelasId);
                    })
                    ->where('created_at', '>=', $twelveHoursAgo)
                    ->whereNotNull('siswa_id')
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        }

        $activeExamTitles = $activeExams->values()->implode(', ');

        return [
            'active_exam_titles' => $activeExamTitles,
            'submissions' => $recentSubmissions
        ];
    }

    private function getActiveExamData($user) {
        $result = [];
        
        switch ($user->role) {
            case 'admin':
                $result = $this->getAdminActiveExamData();
                break;
            case 'pengajar':
                $result = $this->getPengajarActiveExamData($user);
                break;
            default:
                $result = [];
        }
        
        return $result;
    }

    private function getAdminActiveExamData() {
        $result = [];
        
        $ujianList = Ujian::where('status', 'active')
            ->with(['kelas', 'hasilUjians'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($ujianList as $index => $ujian) {
            $hasilUjians = $ujian->hasilUjians;
            $totalHasil = $hasilUjians->count();
            $waktuPengerjaan = $this->formatWaktuPengerjaan($ujian->waktu);
            
            $result[] = [
                'no' => $index + 1,
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'waktu_pengerjaan' => $waktuPengerjaan,
                'status' => ucfirst($ujian->status),
                'kelas' => $ujian->kelas->nama ?? '-',
                'total_hasil' => $totalHasil . ' siswa',
                'ujian_detail' => $ujian, 
                'siswa_results' => $this->getSiswaResultsForExam($ujian->id)
            ];
        }
        
        return $result;
    }

    private function getPengajarActiveExamData($user) {
        $result = [];
        
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return $result;
        }
        
        $ujianList = Ujian::where('status', 'active')
            ->where('kelas_id', $pengajarDetail->kelas_id)
            ->with(['kelas', 'hasilUjians'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($ujianList as $index => $ujian) {
            $hasilUjians = $ujian->hasilUjians;
            $totalHasil = $hasilUjians->count();
            
            $waktuPengerjaan = $this->formatWaktuPengerjaan($ujian->waktu_pengerjaan);
            
            $result[] = [
                'no' => $index + 1,
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'waktu_pengerjaan' => $waktuPengerjaan,
                'status' => ucfirst($ujian->status),
                'kelas' => $ujian->kelas->nama ?? '-',
                'total_hasil' => $totalHasil . ' siswa',
                'ujian_detail' => $ujian, 
                'siswa_results' => $this->getSiswaResultsForExam($ujian->id)
            ];
        }
        
        return $result;
    }

    private function getSiswaResultsForExam($ujianId) {
        $hasilUjians = HasilUjian::where('ujian_id', $ujianId)
            ->with(['siswa.siswaDetail'])
            ->orderBy('nilai', 'desc')
            ->get();
        
        $siswaData = [];
        foreach ($hasilUjians as $hasil) {
            $siswaDetail = $hasil->siswa->siswaDetail;
            $siswaData[] = [
                'avatar' => $hasil->siswa->getAvatarUrl(),
                'nama_lengkap' => $siswaDetail ? $siswaDetail->nama_lengkap : $hasil->siswa->name,
                'nilai' => $hasil->nilai,
                'benar' => $hasil->jumlah_benar,
                'salah' => $hasil->jumlah_salah,
                'waktu_pengerjaan_siswa' => $this->formatWaktuPengerjaanDetik($hasil->waktu_pengerjaan ?? 0)
            ];
        }
        
        return $siswaData;
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

    private function formatWaktuPengerjaan($waktuMenit) {
        if ($waktuMenit < 60) {
            return $waktuMenit . ' menit';
        }
        
        $jam = floor($waktuMenit / 60);
        $sisaMenit = $waktuMenit % 60;
        
        if ($sisaMenit == 0) {
            return $jam . ' jam';
        }
        
        return $jam . ' jam ' . $sisaMenit . ' menit';
    }

    private function getSiswaChartData($user) {
        $siswaDetail = $user->siswaDetail;
        if (!$siswaDetail) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }

        $firstExam = HasilUjian::where('siswa_id', $user->id)
            ->orderBy('created_at', 'asc')
            ->first();

        if (!$firstExam) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }

        $startMonth = $firstExam->created_at->month;
        $startYear = $firstExam->created_at->year;
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $chartData = [
            'labels' => [],
            'datasets' => []
        ];

        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        $year = $startYear;
        $month = $startMonth;
        
        while (($year < $currentYear) || ($year == $currentYear && $month <= $currentMonth)) {
            $monthName = $months[$month] . ($year != $currentYear ? " $year" : "");
            $chartData['labels'][] = $monthName;
            
            $hasilUjianBulan = HasilUjian::where('siswa_id', $user->id)
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->with('ujian')
                ->orderBy('created_at', 'asc')
                ->get();

            if ($hasilUjianBulan->count() > 0) {
                foreach ($hasilUjianBulan as $index => $hasil) {
                    $datasetIndex = null;
                    foreach ($chartData['datasets'] as $key => $dataset) {
                        if ($dataset['ujian_id'] == $hasil->ujian_id) {
                            $datasetIndex = $key;
                            break;
                        }
                    }
                    
                    if ($datasetIndex === null) {
                        $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
                        $colorIndex = count($chartData['datasets']) % count($colors);
                        
                        $chartData['datasets'][] = [
                            'label' => $hasil->ujian->judul ?? 'Ujian ' . $hasil->ujian_id,
                            'data' => array_fill(0, count($chartData['labels']) - 1, 0),
                            'backgroundColor' => $colors[$colorIndex],
                            'ujian_id' => $hasil->ujian_id
                        ];
                        $datasetIndex = count($chartData['datasets']) - 1;
                    }
                    
                    $chartData['datasets'][$datasetIndex]['data'][] = $hasil->nilai;
                    
                    foreach ($chartData['datasets'] as $key => $dataset) {
                        if ($key != $datasetIndex && count($dataset['data']) < count($chartData['labels'])) {
                            $chartData['datasets'][$key]['data'][] = 0;
                        }
                    }
                }
            } else {
                foreach ($chartData['datasets'] as $key => $dataset) {
                    $chartData['datasets'][$key]['data'][] = 0;
                }
            }
            $month++;
            if ($month > 12) {
                $month = 1;
                $year++;
            }
        }

        return $chartData;
    }

    private function getSiswaLeaderboardData($user) {
        $siswaDetail = $user->siswaDetail;
        if (!$siswaDetail) {
            return [];
        }

        $kelasId = $siswaDetail->kelas_id;
        
        $siswaList = User::where('role', 'siswa')
            ->whereHas('siswaDetail', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->with(['siswaDetail', 'hasilUjian.ujian'])
            ->get();

        $leaderboardData = [];
        
        foreach ($siswaList as $siswa) {
            $hasilUjians = $siswa->hasilUjian()
                ->whereHas('ujian', function($q) use ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                })
                ->get();
            
            if ($hasilUjians->count() > 0) {
                $rataRata = $hasilUjians->avg('nilai');
                $totalUjian = $hasilUjians->count();
                
                $leaderboardData[] = [
                    'id' => $siswa->id,
                    'nama' => $siswa->siswaDetail ? $siswa->siswaDetail->nama_lengkap : $siswa->name,
                    'avatar' => $siswa->getAvatarUrl(),
                    'rata_rata' => round($rataRata, 1),
                    'total_ujian' => $totalUjian,
                    'is_current_user' => $siswa->id == $user->id
                ];
            }
        }
        
        usort($leaderboardData, function($a, $b) {
            return $b['rata_rata'] <=> $a['rata_rata'];
        });
        
        foreach ($leaderboardData as $index => &$data) {
            $data['rank'] = $index + 1;
            $data['is_top_3'] = $index < 3;
        }
        
        return $leaderboardData;
    }
}