<?php

namespace App\Http\Controllers;

use App\Models\HasilUjian;
use App\Models\Kelas;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
