<?php

namespace App\Http\Controllers;

use App\Models\PengajarDetail;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManajemenUjianController extends Controller
{
    public function index() {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $ujians = Ujian::with(['kelas', 'soals', 'hasilUjians.siswa.siswaDetail'])->get();
        } else {
            $kelasIds = PengajarDetail::where('pengajar_id', $user->id)->pluck('kelas_id');

            $ujians = Ujian::with(['kelas', 'soals', 'hasilUjians.siswa.siswaDetail'])
                ->whereIn('kelas_id', $kelasIds)
                ->get();
        }

        $formatted = $ujians->map(function ($ujian) {
            return [
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'kelas' => optional($ujian->kelas)->nama ?? '-', 
                'waktu' => $ujian->waktu ? $ujian->waktu->format('H:i') : '-',
                'status' => [
                    'text' => $ujian->status,
                    'badge' => match ($ujian->status) {
                        'pending' => 'bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-gray-400',
                        'active' => 'bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-green-400',
                        'finished' => 'bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-purple-400',
                    }
                ],
                'total_soal' => $ujian->soals->count(),
                'hasil' => $ujian->hasilUjians->map(function ($hasil) {
                    $detail = optional($hasil->siswa->siswaDetail);
                    return [
                        'nama_lengkap' => $detail->nama_lengkap ?? '-',
                        'email' => $hasil->siswa->email ?? '-',
                        'nilai' => $hasil->nilai,
                    ];
                })->values(),
            ];
        });

        return view('Dashboard.Manajemen-Ujian', [
            'dataUjian' => $formatted,
        ]);
    }
}
