<?php

namespace App\Http\Controllers;

use App\Models\Batches;
use App\Models\Kelas;
use App\Models\Ujian;
use App\Models\HasilUjian;
use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller {
    public function index()
    {
        $user = Auth::user();
        
        $data = ['user' => $user];
        
        if (in_array($user->role, ['admin', 'pengajar'])) {

            $data['cardData'] = $this->getCardData($user);
            $data['chartData'] = $this->getStackedBarChartData($user);
            $data['recentSubmissions'] = $this->getRecentExamSubmissions($user);
            $data['activeExamData'] = $this->getActiveExamData($user);
            if ($user->role === 'admin') {
                $kelas = Kelas::select('id', 'nama', 'type', 'waktu_magang', 'durasi_belajar')->get();
                
             
                
                $kelasData = $kelas->mapWithKeys(function ($item) {
                    return [$item->id => [
                        'nama' => $item->nama,
                        'type' => $item->type,
                        'durasi_belajar' => $item->durasi_belajar,
                        'waktu_magang' => $item->waktu_magang
                    ]];
                })->toArray();
                
                $data['kelas'] = $kelas;
                $data['kelasData'] = $kelasData; 
                
                $batchData = $this->getBatchData();
                $data['batchData'] = $batchData;
            }
        } else {
            $data['chartData'] = $this->getSiswaChartData($user);
            $data['leaderboardData'] = $this->getSiswaLeaderboardData($user);
            $data['pembayaran'] = $this->getSiswaPembayaranData($user);

        }
        
        return view('Dashboard.Dashboard', $data);
    }


    private function getBatchData()
    {
        return Batches::withCount('siswaDetails')
            ->with('kelas')
            ->get()
            ->map(function ($batch) {
                $batchNumber = $this->extractBatchNumber($batch->nama);

                $periode = "-";
                if ($batch->tanggal_mulai && $batch->tanggal_selesai) {
                    $periode = \Carbon\Carbon::parse($batch->tanggal_mulai)->format('d M Y') .
                        ' - ' .
                        \Carbon\Carbon::parse($batch->tanggal_selesai)->format('d M Y');
                }

                if (strtolower($batch->status) === 'registration') {
                    $jumlahPeserta = \App\Models\PendaftaranPeserta::where('batch_id', $batch->id)
                        ->where('kelas_id', $batch->kelas_id)
                        ->count();

                    $jumlahPesertaText = $jumlahPeserta . ' Jumlah Pendaftar';
                } else {
                    $jumlahPesertaText = $batch->siswa_details_count . ' Peserta';
                }

                return [
                    'id' => $batch->id,
                    'nama' => $batch->nama,
                    'status' => $batch->status,
                    'kelas' => $batch->kelas->nama,
                    'kelas_id' => $batch->kelas_id,
                    'batch_number' => $batchNumber,
                    'status_badge' => match (strtolower($batch->status)) {
                        'active' => '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Active</span>',
                        'registration' => '<span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">Registration</span>',
                        'finished' => '<span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">Finished</span>',
                        default => '<span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">Inactive</span>',
                    },
                    'jumlah_peserta' => $jumlahPesertaText,
                    'created_at' => $batch->created_at->format('d M Y'),
                    'tanggal_mulai' => $batch->tanggal_mulai,
                    'tanggal_selesai' => $batch->tanggal_selesai,
                    'periode' => $periode,
                ];
            })
            ->sort(function ($a, $b) {
                $aIsActive = strtolower($a['status']) === 'active';
                $bIsActive = strtolower($b['status']) === 'active';

                if ($aIsActive && !$bIsActive) return -1;
                if (!$aIsActive && $bIsActive) return 1;

                if ($aIsActive && $bIsActive) {
                    return $a['kelas_id'] <=> $b['kelas_id'];
                }

                if ($a['kelas_id'] !== $b['kelas_id']) {
                    return $a['kelas_id'] <=> $b['kelas_id'];
                }

                $statusOrder = [
                    'inactive' => 1,
                    'registration' => 2,
                    'finished' => 3
                ];

                $aStatusOrder = $statusOrder[strtolower($a['status'])] ?? 4;
                $bStatusOrder = $statusOrder[strtolower($b['status'])] ?? 4;

                if ($aStatusOrder !== $bStatusOrder) {
                    return $aStatusOrder <=> $bStatusOrder;
                }

                return $b['batch_number'] <=> $a['batch_number'];
            })
            ->values();
    }


    private function extractBatchNumber($batchName) {
        preg_match_all('/\d+/', $batchName, $matches);
            
        if (!empty($matches[0])) {
            return (int) end($matches[0]);
        }
            
        return 0;
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
            if (!$pengajarDetail) {
                return [
                    'total_peserta' => 0,
                    'total_ujian' => 0,
                    'total_ujian_active' => 0,
                    'show_kelas_card' => false
                ];
            }
            
            $kelasIds = $pengajarDetail->kelas()->pluck('kelas.id')->toArray();
            
            $totalSiswa = User::where('role', 'siswa')
                ->whereHas('siswaDetail', function($q) use ($kelasIds) {
                    $q->whereIn('kelas_id', $kelasIds);
                })->count();
            
            $totalUjian = Ujian::whereIn('kelas_id', $kelasIds)->count();
            $totalUjianActive = Ujian::whereIn('kelas_id', $kelasIds)
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

    private function getStackedBarChartData($user)
{
    $currentYear = now()->year;

    if ($user->role === 'admin') {
        // Ambil kelas yang memiliki batch active
        $kelasList = Kelas::whereHas('batches', function ($q) {
            $q->where('status', 'active');
        })->with(['batches' => function ($q) {
            $q->where('status', 'active');
        }])->get();

        if ($kelasList->isEmpty()) {
            return [
                'labels' => ['Tidak ada kelas dengan batch aktif'],
                'datasets' => []
            ];
        }

        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Rata-rata Nilai',
                    'data' => [],
                    'borderColor' => 'gradient', // Marker untuk gradient
                    'backgroundColor' => 'gradient-fill', // Marker untuk area fill
                    'borderWidth' => 3,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => '#fff',
                    'pointBorderWidth' => 3,
                    'type' => 'line'
                ]
            ]
        ];

        foreach ($kelasList as $kelas) {
            $activeBatch = $kelas->batches->first();
            
            if ($activeBatch) {
                // Tambahkan nama kelas ke labels
                $chartData['labels'][] = $kelas->nama;

                // Hitung rata-rata nilai untuk kelas dengan batch active
                $averageScore = HasilUjian::whereHas('siswa.siswaDetail', function ($q) use ($kelas, $activeBatch) {
                        $q->where('kelas_id', $kelas->id)
                          ->where('batch_id', $activeBatch->id);
                    })
                    ->whereHas('ujian', function ($q) use ($kelas, $activeBatch) {
                        $q->where('kelas_id', $kelas->id)
                          ->where('batch_id', $activeBatch->id);
                    })
                    ->whereYear('created_at', $currentYear)
                    ->avg('nilai');

                $chartData['datasets'][0]['data'][] = $averageScore ? round($averageScore, 1) : 0;
            }
        }

    } else if ($user->role === 'pengajar') {
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return [
                'labels' => [],
                'datasets' => []
            ];
        }

        // Ambil kelas pengajar yang memiliki batch active
        $kelasList = $pengajarDetail->kelas()
            ->whereHas('batches', function ($q) {
                $q->where('status', 'active');
            })
            ->with(['batches' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get();

        if ($kelasList->isEmpty()) {
            return [
                'labels' => ['Tidak ada kelas dengan batch aktif'],
                'datasets' => []
            ];
        }

        $chartData = [
            'labels' => [],
            'datasets' => [
                [
                    'label' => 'Rata-rata Nilai',
                    'data' => [],
                    'borderColor' => 'gradient',
                    'backgroundColor' => 'gradient-fill',
                    'borderWidth' => 3,
                    'tension' => 0.4,
                    'fill' => true,
                    'pointRadius' => 6,
                    'pointHoverRadius' => 8,
                    'pointBackgroundColor' => '#fff',
                    'pointBorderWidth' => 3,
                    'type' => 'line'
                ]
            ]
        ];

        foreach ($kelasList as $kelas) {
            $activeBatch = $kelas->batches->first();
            
            if ($activeBatch) {
                $chartData['labels'][] = $kelas->normal_nama;

                $averageScore = HasilUjian::whereHas('siswa.siswaDetail', function ($q) use ($kelas, $activeBatch) {
                        $q->where('kelas_id', $kelas->id)
                          ->where('batch_id', $activeBatch->id);
                    })
                    ->whereHas('ujian', function ($q) use ($kelas, $activeBatch) {
                        $q->where('kelas_id', $kelas->id)
                          ->where('batch_id', $activeBatch->id);
                    })
                    ->whereYear('created_at', $currentYear)
                    ->avg('nilai');

                $chartData['datasets'][0]['data'][] = $averageScore ? round($averageScore, 1) : 0;
            }
        }
    }

    return $chartData;
}

    private function getRecentExamSubmissions($user)
    {
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
            if (!$pengajarDetail) {
                return [
                    'active_exam_titles' => '',
                    'submissions' => []
                ];
            }

            $kelasIds = $pengajarDetail->kelas()->pluck('kelas.id')->toArray();
            if (empty($kelasIds)) {
                return [
                    'active_exam_titles' => '',
                    'submissions' => []
                ];
            }

            $activeExams = Ujian::where('status', 'active')
                ->whereIn('kelas_id', $kelasIds)
                ->pluck('judul', 'id');

            $recentSubmissions = HasilUjian::with(['siswa', 'ujian'])
                ->whereHas('ujian', function ($q) use ($kelasIds) {
                    $q->where('status', 'active')
                      ->whereIn('kelas_id', $kelasIds);
                })
                ->where('created_at', '>=', $twelveHoursAgo)
                ->whereNotNull('siswa_id')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $activeExamTitles = $activeExams->values()->implode(', ');

        return [
            'active_exam_titles' => $activeExamTitles,
            'submissions' => $recentSubmissions
        ];
    }

    private function getActiveExamData($user)
    {
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

    private function getAdminActiveExamData()
    {
        $result = [];
        
        $ujianList = Ujian::where('status', 'active')
            ->with(['kelas', 'hasilUjians'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($ujianList as $index => $ujian) {
            $hasilUjians = $ujian->hasilUjians;
            $totalHasil = $hasilUjians->count();
            $waktuPengerjaan = $ujian->waktu;
            
            $result[] = [
                'no' => $index + 1,
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'waktu_pengerjaan' => $waktuPengerjaan . " menit",
                'status' => ucfirst($ujian->status),
                'kelas' => $ujian->kelas->nama ?? '-',
                'total_hasil' => $totalHasil . ' siswa',
                'ujian_detail' => $ujian, 
                'siswa_results' => $this->getSiswaResultsForExam($ujian->id)
            ];
        }
        
        return $result;
    }

    private function getPengajarActiveExamData($user)
    {
        $result = [];
        
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return $result;
        }
        
        $kelasIds = $pengajarDetail->kelas()->pluck('kelas.id')->toArray();
        if (empty($kelasIds)) {
            return $result;
        }
        
        $ujianList = Ujian::where('status', 'active')
            ->whereIn('kelas_id', $kelasIds)
            ->with(['kelas', 'hasilUjians'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        foreach ($ujianList as $index => $ujian) {
            $hasilUjians = $ujian->hasilUjians;
            $totalHasil = $hasilUjians->count();
            
           $waktuPengerjaan = $ujian->waktu;

            
            $result[] = [
                'no' => $index + 1,
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'waktu_pengerjaan' => $waktuPengerjaan . " menit",
                'status' => ucfirst($ujian->status),
                'kelas' => $ujian->kelas->nama ?? '-',
                'total_hasil' => $totalHasil . ' siswa',
                'ujian_detail' => $ujian, 
                'siswa_results' => $this->getSiswaResultsForExam($ujian->id)
            ];
        }
        
        return $result;
    }

    private function getSiswaResultsForExam($ujianId)
    {
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

    private function formatWaktuPengerjaanDetik($waktuDetik)
    {
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

    private function getSiswaChartData($user)
    {
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

    private function getSiswaLeaderboardData($user)
    {
        $siswaDetail = $user->siswaDetail;
        if (!$siswaDetail) {
            return [];
        }

        $kelasId = $siswaDetail->kelas_id;
        $batchId = $siswaDetail->batch_id;

        $siswaList = User::where('role', 'siswa')
            ->whereHas('siswaDetail', function($q) use ($kelasId, $batchId) {
                $q->where('kelas_id', $kelasId)
                ->where('batch_id', $batchId);
            })
            ->with(['siswaDetail', 'hasilUjian.ujian'])
            ->get();

        $leaderboardData = [];

        foreach ($siswaList as $siswa) {
            $hasilUjians = $siswa->hasilUjian()
                ->whereHas('ujian', function($q) use ($kelasId, $batchId) {
                    $q->where('kelas_id', $kelasId)
                    ->where('batch_id', $batchId);
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

    private function getSiswaPembayaranData($user)
    {
        $siswaDetail = $user->siswaDetail;
        if (!$siswaDetail) {
            return [];
        }

        return Pembayaran::where('siswa_detail_id', $siswaDetail->id)
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get()
            ->map(function($pembayaran) {
                return [
                    'id' => $pembayaran->id,
                    'cicilan_ke' => $pembayaran->cicilan_ke,
                    'jumlah' => $pembayaran->jumlah_formatted,
                    'bukti_pembayaran' => $pembayaran->bukti_pembayaran,
                    'status' => $pembayaran->status,
                    'tanggal_jatuh_tempo' => $pembayaran->tanggal_jatuh_tempo->format('Y-m-d'),
                ];
            });
    }

}