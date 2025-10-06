<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Ujian;
use App\Models\HasilUjian;
use App\Models\Batches;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

class NilaiController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        switch ($user->role) {
            case 'admin':
                $data = $this->getAdminData();
                break;
            case 'pengajar':
                $data = $this->getPengajarData($user);
                break;
            case 'siswa':
                $data = $this->getSiswaData($user);
                break;
            default:
                $data = [];
        }
        
        return view('Dashboard.Nilai', compact('data', 'user'));
    }

    public function show($ujianId)
    {
        try {
            $user = Auth::user();
            $ujian = Ujian::with(['batch', 'kelas'])->findOrFail($ujianId);
            

            if ($user->role === 'pengajar') {
                $pengajarDetail = $user->pengajarDetail;
                if (!$pengajarDetail) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access'
                    ], 403);
                }
                
                $allowedKelasIds = $pengajarDetail->kelas()->pluck('kelas.id')->toArray();
                
                if (!in_array($ujian->kelas_id, $allowedKelasIds)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Unauthorized access to this exam'
                    ], 403);
                }
            }
            
       
            $hasilUjians = HasilUjian::where('ujian_id', $ujianId)
                ->with(['siswa.siswaDetail'])
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
                    'batch_nama' => optional($ujian->batch)->nama ?? '-',
                    'batch_status' => optional($ujian->batch)->status ?? '-',
                ];
            }
            
            $totalHasil = $hasilUjians->count();
            $rataRata = $totalHasil > 0 ? $hasilUjians->avg('nilai') : 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $ujian->id,
                    'judul' => $ujian->judul,
                    'kelas' => optional($ujian->kelas)->nama ?? '-',
                    'batch_nama' => optional($ujian->batch)->nama ?? '-',
                    'batch_status' => optional($ujian->batch)->status ?? '-',
                    'total_hasil' => $totalHasil,
                    'rata_rata' => number_format($rataRata, 1),
                    'siswa' => $siswaData
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data ujian tidak ditemukan: ' . $e->getMessage()
            ], 404);
        }
    }
    
    private function getAdminData()
    {
        $result = [];
        
        $kelasList = Kelas::all();
        
        foreach ($kelasList as $kelas) {
            $ujianList = Ujian::where('kelas_id', $kelas->id)
                ->whereHas('batch', function ($query) {
                    $query->where('status', 'active');
                })
                ->with(['hasilUjians', 'batch'])
                ->get()
                ->sortByDesc(function ($ujian) {
                    return optional($ujian->batch)->status === 'active' ? 1 : 0;
                })->values();
            
            $kelasData = [];
            
            foreach ($ujianList as $ujian) {
                $hasilUjians = $ujian->hasilUjians;
                
                $totalHasil = $hasilUjians->count();
                $rataRata = $totalHasil > 0 ? $hasilUjians->avg('nilai') : 0;
                
                $batchNama = optional($ujian->batch)->nama ?? '-';
                $batchStatus = optional($ujian->batch)->status ?? '-';
                $badgeClass = $batchStatus === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                $batchBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $badgeClass . '">' . $batchNama . '</span>';
                
                $kelasData[] = [
                    'id' => $ujian->id,
                    'judul' => $ujian->judul,
                    'total_hasil' => (string) $totalHasil,
                    'rata_rata' => number_format($rataRata, 1),
                    'batch_nama' => $batchNama,
                    'batch_status' => $batchStatus,
                    'batch_badge' => $batchBadge,
                ];
            }
            
            if (!empty($kelasData)) {
                $result[$kelas->nama] = $kelasData;
            }
        }
        
        return $result;
    }

    private function getPengajarData($user)
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
            $ujianList = Ujian::where('kelas_id', $kelas->id)
                ->whereHas('batch', function ($query) {
                    $query->where('status', 'active');
                })
                ->with(['hasilUjians', 'batch'])
                ->get()
                ->sortByDesc(function ($ujian) {
                    return optional($ujian->batch)->status === 'active' ? 1 : 0;
                })->values();
            
            $kelasData = [];
            
            foreach ($ujianList as $ujian) {
                $hasilUjians = $ujian->hasilUjians;
                
                $totalHasil = $hasilUjians->count();
                $rataRata = $totalHasil > 0 ? $hasilUjians->avg('nilai') : 0;
                
                $batchNama = optional($ujian->batch)->nama ?? '-';
                $batchStatus = optional($ujian->batch)->status ?? '-';
                $badgeClass = $batchStatus === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800';
                $batchBadge = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $badgeClass . '">' . $batchNama . '</span>';
                
                $kelasData[] = [
                    'id' => $ujian->id,
                    'judul' => $ujian->judul,
                    'total_hasil' => (string) $totalHasil,
                    'rata_rata' => number_format($rataRata, 1),
                    'batch_nama' => $batchNama,
                    'batch_status' => $batchStatus,
                    'batch_badge' => $batchBadge,
                ];
            }
            
            if (!empty($kelasData)) {
                $result[$kelas->nama] = $kelasData;
            }
        }
        
        return $result;
    }

    private function getSiswaData($user)
    {
        $result = [];
        
        $hasilUjians = HasilUjian::where('siswa_id', $user->id)
            ->with(['ujian.soals', 'ujian.batch'])
            ->get()
            ->sortByDesc(function ($hasil) {
                return optional($hasil->ujian->batch)->status === 'active' ? 1 : 0;
            })->values();
        
        foreach ($hasilUjians as $hasil) {
            $totalSoal = $hasil->ujian->soals->count();
            
            $result[] = [
                'id' => $hasil->ujian->id,
                'judul' => $hasil->ujian->judul,
                'total_soal' => $totalSoal,
                'nilai' => $hasil->nilai,
                'benar' => $hasil->jumlah_benar,
                'salah' => $hasil->jumlah_salah,
                'batch_nama' => optional($hasil->ujian->batch)->nama ?? '-',
                'batch_status' => optional($hasil->ujian->batch)->status ?? '-',
            ];
        }
        
        return $result;
    }

       public function download(Request $request)
    {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'pengajar'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $batchOption = $request->input('batch_option', 'all'); // 'active' or 'all'

        switch ($user->role) {
            case 'admin':
                return $this->downloadForAdmin($batchOption);
            case 'pengajar':
                return $this->downloadForPengajar($user, $batchOption);
            default:
                return response()->json(['error' => 'Invalid role'], 400);
        }
    }

    private function downloadForAdmin($batchOption)
    {

        $activeBatches = $batchOption === 'active' 
            ? Batches::where('status', 'active')->get()
            : Batches::all();
        
        $zipFileName = $batchOption === 'active'
            ? 'hasil_batch_active_' . date('d-m-Y_H-i-s') . '.zip'
            : 'hasil_ujian_semua_batch_' . date('d-m-Y_H-i-s') . '.zip';
        
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return response()->json(['error' => 'Failed to create zip file'], 500);
        }

        $kelasList = Kelas::all();

        foreach ($kelasList as $kelas) {
            $ujianList = Ujian::where('kelas_id', $kelas->id)
                ->when($batchOption === 'active', function ($query) use ($activeBatches) {
                    $query->whereIn('batch_id', $activeBatches->pluck('id'));
                })
                ->with(['hasilUjians.siswa.siswaDetail', 'batch'])
                ->get();

            foreach ($ujianList as $ujian) {
                foreach ($ujian->hasilUjians as $hasil) {
                    $siswaDetail = $hasil->siswa->siswaDetail;
                    $namaSiswa = $siswaDetail ? $siswaDetail->nama_lengkap : $hasil->siswa->name;
                    
                    $pdfContent = $this->generatePdfContent($hasil->siswa, $kelas->nama);
                    $pdf = Pdf::loadHtml($pdfContent);
                    
                    $batchFolder = $this->sanitizeFileName(optional($ujian->batch)->nama ?? 'No_Batch');
                    $kelasFolder = $this->sanitizeFileName($kelas->nama);
                    $pathInZip = "{$batchFolder}/{$kelasFolder}/nilai_" . $this->sanitizeFileName($namaSiswa) . '.pdf';
                    
                    $zip->addFromString($pathInZip, $pdf->output());
                }
            }
        }
        
        $zip->close();
        
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    private function downloadForPengajar($user, $batchOption)
    {
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return response()->json(['error' => 'Pengajar detail not found'], 404);
        }
        
        $kelasList = $pengajarDetail->kelas()->get();
        if ($kelasList->isEmpty()) {
            return response()->json(['error' => 'No classes found for pengajar'], 404);
        }
        
        $activeBatches = $batchOption === 'active'
            ? Batches::where('status', 'active')->get()
            : Batches::all();
        
        $zipFileName = $batchOption === 'active'
            ? 'hasil_batch_active_' . date('d-m-Y_H-i-s') . '.zip'
            : 'hasil_ujian_kelas_' . date('d-m-Y_H-i-s') . '.zip';
        
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) !== TRUE) {
            return response()->json(['error' => 'Failed to create zip file'], 500);
        }
        
        foreach ($kelasList as $kelas) {
            $ujianList = Ujian::where('kelas_id', $kelas->id)
                ->when($batchOption === 'active', function ($query) use ($activeBatches) {
                    $query->whereIn('batch_id', $activeBatches->pluck('id'));
                })
                ->with(['hasilUjians.siswa.siswaDetail', 'batch'])
                ->get();
            
            foreach ($ujianList as $ujian) {
                foreach ($ujian->hasilUjians as $hasil) {
                    $siswaDetail = $hasil->siswa->siswaDetail;
                    $namaSiswa = $siswaDetail ? $siswaDetail->nama_lengkap : $hasil->siswa->name;
                    
                    $pdfContent = $this->generatePdfContent($hasil->siswa, $kelas->nama);
                    $pdf = Pdf::loadHtml($pdfContent);
                    
                    $batchFolder = $this->sanitizeFileName(optional($ujian->batch)->nama ?? 'No_Batch');
                    $kelasFolder = $this->sanitizeFileName($kelas->nama);
                    $pathInZip = "{$batchFolder}/{$kelasFolder}/nilai_" . $this->sanitizeFileName($namaSiswa) . '.pdf';
                    
                    $zip->addFromString($pathInZip, $pdf->output());
                }
            }
        }
        
        $zip->close();
        
        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    private function generatePdfContent($siswa, $kelasNama)
    {
        $hasilUjians = HasilUjian::where('siswa_id', $siswa->id)
            ->with(['ujian.soals', 'ujian.batch'])
            ->get();
        
        $averageNilai = $hasilUjians->count() > 0 ? $hasilUjians->avg('nilai') : 0;
        $namaSiswa = $siswa->siswaDetail ? $siswa->siswaDetail->nama_lengkap : $siswa->name;
        
        $tableRows = '';
        foreach ($hasilUjians as $hasil) {
            $ujian = $hasil->ujian;
            $totalSoal = $ujian->soals->count();
            $grade = $this->calculateGrade($hasil->nilai);
            $batchNama = optional($ujian->batch)->nama ?? '-';
            
            $tableRows .= "
                <tr>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$ujian->judul}</td>
                    <td style='border: 1px solid #ddd; padding: 8px;'>{$batchNama}</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$hasil->nilai}</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$hasil->jumlah_benar}</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$hasil->jumlah_salah}</td>
                    <td style='border: 1px solid #ddd; padding: 8px; text-align: center;'>{$grade}</td>
                </tr>
            ";
        }

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>Hasil Ujian - {$namaSiswa}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 15px; }
                .info-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .info-table td { padding: 8px 12px; border: 1px solid #ddd; }
                .info-table .label { background-color: #f5f5f5; font-weight: bold; width: 30%; }
                .results-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .results-table th, .results-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .results-table th { background-color: #f5f5f5; font-weight: bold; }
                .average { font-size: 16px; font-weight: bold; text-align: center; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>HASIL UJIAN</h1>
                <h2>{$namaSiswa}</h2>
            </div>
            
            <table class='info-table'>
                <tr>
                    <td class='label'>Nama Siswa</td>
                    <td>{$namaSiswa}</td>
                </tr>
                <tr>
                    <td class='label'>Kelas</td>
                    <td>{$kelasNama}</td>
                </tr>
                <tr>
                    <td class='label'>Tanggal</td>
                    <td>" . date('d F Y') . "</td>
                </tr>
            </table>
            
            <table class='results-table'>
                <thead>
                    <tr>
                        <th>Judul Ujian</th>
                        <th>Batch</th>
                        <th style='text-align: center;'>Nilai</th>
                        <th style='text-align: center;'>Benar</th>
                        <th style='text-align: center;'>Salah</th>
                        <th style='text-align: center;'>Grade</th>
                    </tr>
                </thead>
                <tbody>
                    {$tableRows}
                </tbody>
            </table>
            
            <div class='average'>
                Rata-rata Nilai: " . number_format($averageNilai, 1) . "
            </div>
            
            <div style='margin-top: 40px; text-align: center; color: #666; font-size: 12px;'>
                <p>Dokumen ini dibuat secara otomatis pada " . date('d F Y H:i:s') . "</p>
            </div>
        </body>
        </html>
        ";
    }

    private function calculateGrade($nilai)
    {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    private function sanitizeFileName($filename)
    {
        $filename = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $filename);
        $filename = preg_replace('/\s+/', '_', $filename);
        return trim($filename, '_');
    }
}