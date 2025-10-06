<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\PengajarDetail;
use App\Models\Ujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManajemenUjianController extends Controller
{
    public function index() {
        $user = Auth::user();
        $ujians = collect();

        if ($user->role === 'admin') {
            $ujians = Ujian::with(['kelas', 'soals.jawabans', 'batch'])
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($user->role === 'pengajar') {
            $pengajarDetail = $user->pengajarDetail;
            $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
            $ujians = Ujian::with(['kelas', 'soals.jawabans', 'batch'])
                ->whereIn('kelas_id', $kelasIds)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $formatted = $ujians->map(function ($ujian) {
            return [
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'kelas' => optional($ujian->kelas)->nama ?? '-',
                'batch' => optional($ujian->batch)->nama ?? '-',
                'batch_status' => optional($ujian->batch)->status ?? '-',
                'display_waktu' => $ujian->waktu ? $ujian->waktu . ' Menit' : '-',
                'waktu' => (string) $ujian->waktu,
                'status' => [
                    'text' => $ujian->status,
                    'badge' => match ($ujian->status) {
                        'pending' => 'bg-gray-100 text-gray-800 text-xs  font-medium px-2.5 rounded-sm border border-gray-400',
                        'active' => 'bg-green-100 text-green-800 text-xs  font-medium px-2.5 rounded-sm border border-green-400',
                        'finished' => 'bg-purple-100 text-purple-800 text-xs  font-medium px-2.5 rounded-sm border border-purple-400',
                        default => 'bg-gray-100 text-gray-800 text-xs  font-medium px-2.5 rounded-sm border border-gray-400',
                    }
                ],
                'total_soal' => $ujian->soals->count(),
                'soals' => $ujian->soals->map(function ($soal) {
                    return [
                        'id' => $soal->id,
                        'teks' => $soal->soal,
                        'jawabans' => $soal->jawabans->map(function ($jawaban) {
                            return [
                                'id' => $jawaban->id,
                                'pilihan' => $jawaban->pilihan,
                                'teks' => $jawaban->teks,
                                'benar' => $jawaban->benar,
                            ];
                        })->values(),
                    ];
                })->values(),
            ];
        })->sortByDesc(function ($ujian) {
            return $ujian['batch_status'] === 'active' ? 1 : 0;
        })->values();

        return view('Dashboard.Manajemen-Ujian', [
            'dataUjian' => $formatted,
        ]);
    }

    public function show($id)
    {
        try {
            $ujian = Ujian::with(['kelas', 'soals.jawabans', 'batch'])->findOrFail($id);
            
            $formattedSoals = $ujian->soals->map(function ($soal, $index) {
                return [
                    'id' => $soal->id,
                    'nomor' => $index + 1,
                    'teks' => $soal->soal,
                    'jawabans' => $soal->jawabans->map(function ($jawaban) {
                        return [
                            'id' => $jawaban->id,
                            'pilihan' => $jawaban->pilihan,
                            'teks' => $jawaban->teks,
                            'benar' => $jawaban->benar,
                        ];
                    })->values(),
                ];
            })->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $ujian->id,
                    'judul' => $ujian->judul,
                    'kelas' => optional($ujian->kelas)->nama ?? '-',
                    'batch' => optional($ujian->batch)->nama ?? '-',
                    'waktu' => $ujian->waktu ? $ujian->waktu  : '-',
                    'status' => $ujian->status,
                    'total_soal' => $ujian->soals->count(),
                    'soals' => $formattedSoals,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data ujian tidak ditemukan'
            ], 404);
        }
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'waktu' => 'required|in:30,60,90',
            'status' => 'required|in:pending,active,finished',
        ]);

        try {
            $user = Auth::user();
            $ujian = Ujian::findOrFail($id);

            if ($user->role === 'pengajar') {
                $pengajarDetail = $user->pengajarDetail;
                $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];

                if (!in_array($ujian->kelas_id, $kelasIds)) {
                    throw new \Exception('Anda tidak memiliki akses untuk mengubah ujian ini');
                }
            }

            $ujian->judul = $request->input('judul');
            $ujian->waktu = (int) $request->input('waktu');
            $ujian->status = $request->input('status');
            $ujian->save();

            return redirect()->back()->with(AlertHelper::success('Ujian berhasil diperbarui', 'Success'));
        } catch (\Exception $e) {
            return redirect()->back()->with(AlertHelper::error('Gagal memperbarui ujian: ' . $e->getMessage(), 'Error'));
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $ujian = Ujian::findOrFail($id);

            if ($user->role === 'pengajar') {
                $pengajarDetail = $user->pengajarDetail;
                $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];

                if (!in_array($ujian->kelas_id, $kelasIds)) {
                    throw new \Exception('Anda tidak memiliki akses untuk menghapus ujian ini');
                }
            }

            $ujian->delete();

            return redirect()->back()->with(AlertHelper::success('Ujian berhasil dihapus', 'Success'));
        } catch (\Exception $e) {
            return redirect()->back()->with(AlertHelper::error('Gagal menghapus ujian: ' . $e->getMessage(), 'Error'));
        }
    }
}
