<?php

namespace App\Http\Controllers;

use App\Models\HasilUjian;
use App\Models\Kelas;
use App\Models\Ujian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class NilaiController extends Controller
{
    public function index() {
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

    public function download() {
        $user = Auth::user();
        
        if (!in_array($user->role, ['admin', 'pengajar'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        switch ($user->role) {
            case 'admin':
                return $this->downloadForAdmin();
            case 'pengajar':
                return $this->downloadForPengajar($user);
            default:
                return response()->json(['error' => 'Invalid role'], 400);
        }
    }

    private function getAdminData() {
        $result = [];
        
        $kelasList = Kelas::all();
        
        foreach ($kelasList as $kelas) {
            $ujianList = Ujian::where('kelas_id', $kelas->id)
                ->withCount('hasilUjians')
                ->get();
            
            $kelasData = [];
            
            foreach ($ujianList as $ujian) {
                $hasilUjians = HasilUjian::where('ujian_id', $ujian->id)
                    ->with(['siswa.siswaDetail'])
                    ->get();
                
                $totalHasil = $hasilUjians->count();
                $rataRata = $totalHasil > 0 ? $hasilUjians->avg('nilai') : 0;
                
                $siswaData = [];
                foreach ($hasilUjians as $hasil) {
                    $siswaDetail = $hasil->siswa->siswaDetail;
                    $siswaData[] = [
                        'avatar' => $hasil->siswa->getAvatarUrl(),
                        'nama_lengkap' => $siswaDetail ? $siswaDetail->nama_lengkap : $hasil->siswa->name,
                        'nilai' => $hasil->nilai,
                        'benar' => $hasil->jumlah_benar,
                        'salah' => $hasil->jumlah_salah
                    ];
                }
                
                $kelasData[] = [
                    'id' => $ujian->id,
                    'judul' => $ujian->judul,
                    'total_hasil' => (string) $totalHasil,
                    'rata_rata' => number_format($rataRata, 1),
                    'siswa' => $siswaData
                ];
            }
            
            if (!empty($kelasData)) {
                $result[$kelas->nama] = $kelasData;
            }
        }
        
        return $result;
    }

    private function getPengajarData($user) {
        $result = [];
        
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return $result;
        }
        
        $kelas = Kelas::find($pengajarDetail->kelas_id);
        if (!$kelas) {
            return $result;
        }
        
        $ujianList = Ujian::where('kelas_id', $kelas->id)
            ->withCount('hasilUjians')
            ->get();
        
        foreach ($ujianList as $ujian) {
            $hasilUjians = HasilUjian::where('ujian_id', $ujian->id)
                ->with(['siswa.siswaDetail'])
                ->get();
            
            $totalHasil = $hasilUjians->count();
            $rataRata = $totalHasil > 0 ? $hasilUjians->avg('nilai') : 0;
            
            $siswaData = [];
            foreach ($hasilUjians as $hasil) {
                $siswaDetail = $hasil->siswa->siswaDetail;
                $siswaData[] = [
                    'avatar' => $hasil->siswa->getAvatarUrl(),
                    'nama_lengkap' => $siswaDetail ? $siswaDetail->nama_lengkap : $hasil->siswa->name,
                    'nilai' => $hasil->nilai,
                    'benar' => $hasil->jumlah_benar,
                    'salah' => $hasil->jumlah_salah
                ];
            }
            
            $result[] = [
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'total_hasil' => (string) $totalHasil,
                'rata_rata' => number_format($rataRata, 1),
                'siswa' => $siswaData
            ];
        }
        
        return $result;
    }

    private function getSiswaData($user) {
        $result = [];
        
        $hasilUjians = HasilUjian::where('siswa_id', $user->id)
            ->with(['ujian.soals'])
            ->get();
        
        foreach ($hasilUjians as $hasil) {
            $totalSoal = $hasil->ujian->soals->count();
            
            $result[] = [
                'id' => $hasil->ujian->id,
                'judul' => $hasil->ujian->judul,
                'total_soal' => $totalSoal,
                'nilai' => $hasil->nilai,
                'benar' => $hasil->jumlah_benar,
                'salah' => $hasil->jumlah_salah
            ];
        }
        
        return $result;
    }

    private function downloadForAdmin() {
        $zipFileName = 'hasil_ujian_semua_kelas_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            
            $kelasList = Kelas::all();
            
            foreach ($kelasList as $kelas) {
                $ujianList = Ujian::where('kelas_id', $kelas->id)->get();
                
                foreach ($ujianList as $ujian) {
                    $hasilUjians = HasilUjian::where('ujian_id', $ujian->id)
                        ->with(['siswa.siswaDetail'])
                        ->get();
                    
                    foreach ($hasilUjians as $hasil) {
                        $siswaDetail = $hasil->siswa->siswaDetail;
                        $namaSiswa = $siswaDetail ? $siswaDetail->nama_lengkap : $hasil->siswa->name;
                        
                        $pdfContent = $this->generatePdfContent($hasil, $ujian, $namaSiswa);
                        $pdf = Pdf::loadHtml($pdfContent);
                        
                        $pathInZip = $kelas->nama . '/' . $this->sanitizeFileName($namaSiswa) . '/' . $this->sanitizeFileName($ujian->judul) . '.pdf';
                        $zip->addFromString($pathInZip, $pdf->output());
                    }
                }
            }
            
            $zip->close();
            
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }
        
        return response()->json(['error' => 'Failed to create zip file'], 500);
    }

    private function downloadForPengajar($user) {
        $pengajarDetail = $user->pengajarDetail;
        if (!$pengajarDetail) {
            return response()->json(['error' => 'Pengajar detail not found'], 404);
        }
        
        $kelas = Kelas::find($pengajarDetail->kelas_id);
        if (!$kelas) {
            return response()->json(['error' => 'Kelas not found'], 404);
        }
        
        $zipFileName = 'hasil_ujian_kelas_' . $kelas->nama . '_' . date('Y-m-d_H-i-s') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE) === TRUE) {
            
            $ujianList = Ujian::where('kelas_id', $kelas->id)->get();
            
            foreach ($ujianList as $ujian) {
                $hasilUjians = HasilUjian::where('ujian_id', $ujian->id)
                    ->with(['siswa.siswaDetail'])
                    ->get();
                
                foreach ($hasilUjians as $hasil) {
                    $siswaDetail = $hasil->siswa->siswaDetail;
                    $namaSiswa = $siswaDetail ? $siswaDetail->nama_lengkap : $hasil->siswa->name;
                    
                    $pdfContent = $this->generatePdfContent($hasil, $ujian, $namaSiswa);
                    $pdf = Pdf::loadHtml($pdfContent);
                    
                    $pathInZip = $this->sanitizeFileName($namaSiswa) . '/' . $this->sanitizeFileName($ujian->judul) . '.pdf';
                    $zip->addFromString($pathInZip, $pdf->output());
                }
            }
            
            $zip->close();
            
            return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
        }
        
        return response()->json(['error' => 'Failed to create zip file'], 500);
    }

    private function generatePdfContent($hasilUjian, $ujian, $namaSiswa) {
        $totalSoal = $ujian->soals->count();
        $grade = $this->calculateGrade($hasilUjian->nilai);
        
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
                .score-card { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
                .score-item { display: inline-block; margin-right: 20px; text-align: center; }
                .score-value { font-size: 24px; font-weight: bold; color: #333; }
                .score-label { font-size: 12px; color: #666; }
                .grade { font-size: 36px; font-weight: bold; color: " . ($hasilUjian->nilai >= 80 ? '#4CAF50' : ($hasilUjian->nilai >= 70 ? '#FF9800' : '#F44336')) . "; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>HASIL UJIAN</h1>
                <h2>{$ujian->judul}</h2>
            </div>
            
            <table class='info-table'>
                <tr>
                    <td class='label'>Nama Siswa</td>
                    <td>{$namaSiswa}</td>
                </tr>
                <tr>
                    <td class='label'>Judul Ujian</td>
                    <td>{$ujian->judul}</td>
                </tr>
                <tr>
                    <td class='label'>Total Soal</td>
                    <td>{$totalSoal} soal</td>
                </tr>
                <tr>
                    <td class='label'>Tanggal</td>
                    <td>" . $hasilUjian->created_at->format('d F Y H:i') . "</td>
                </tr>
            </table>
            
            <div class='score-card'>
                <div style='text-align: center;'>
                    <div class='score-item'>
                        <div class='score-value'>{$hasilUjian->nilai}</div>
                        <div class='score-label'>NILAI</div>
                    </div>
                    <div class='score-item'>
                        <div class='score-value' style='color: #4CAF50;'>{$hasilUjian->jumlah_benar}</div>
                        <div class='score-label'>BENAR</div>
                    </div>
                    <div class='score-item'>
                        <div class='score-value' style='color: #F44336;'>{$hasilUjian->jumlah_salah}</div>
                        <div class='score-label'>SALAH</div>
                    </div>
                    <div class='score-item'>
                        <div class='grade'>{$grade}</div>
                        <div class='score-label'>GRADE</div>
                    </div>
                </div>
            </div>
            
            <div style='margin-top: 40px; text-align: center; color: #666; font-size: 12px;'>
                <p>Dokumen ini dibuat secara otomatis pada " . date('d F Y H:i:s') . "</p>
            </div>
        </body>
        </html>
        ";
    }

    private function calculateGrade($nilai) {
        if ($nilai >= 90) return 'A';
        if ($nilai >= 80) return 'B';
        if ($nilai >= 70) return 'C';
        if ($nilai >= 60) return 'D';
        return 'E';
    }

    private function sanitizeFileName($filename) {
        $filename = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $filename);
        $filename = preg_replace('/\s+/', '_', $filename);
        return trim($filename, '_');
    }
}
