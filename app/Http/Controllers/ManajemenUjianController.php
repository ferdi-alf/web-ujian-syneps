<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\PengajarDetail;
use App\Models\Ujian;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ManajemenUjianController extends Controller
{
  public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $ujians = Ujian::with(['kelas', 'soals.jawabans'])->get();
        } else {
            $kelasIds = PengajarDetail::where('pengajar_id', $user->id)->pluck('kelas_id');
            $ujians = Ujian::with(['kelas', 'soals.jawabans'])
                ->whereIn('kelas_id', $kelasIds)
                ->get();
        }

        $formatted = $ujians->map(function ($ujian) {
            return [
                'id' => $ujian->id,
                'judul' => $ujian->judul,
                'kelas' => optional($ujian->kelas)->nama ?? '-',
                'display_waktu' => $ujian->waktu ? $ujian->waktu . ' Menit' : '-',
                'waktu' => (string) $ujian->waktu,
                'status' => [
                    'text' => $ujian->status,
                    'badge' => match ($ujian->status) {
                        'pending' => 'bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-gray-400',
                        'active' => 'bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-green-400',
                        'finished' => 'bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-purple-400',
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
        });

        return view('Dashboard.Manajemen-Ujian', [
            'dataUjian' => $formatted,
        ]);
    }


public function update(Request $request, $id)
{
    $request->validate([
        'judul' => 'required|string|max:255',
        'waktu' => 'required|in:30,60,90',
        'status' => 'required|in:pending,active,finished',
    ]);

    try {
        $ujian = Ujian::findOrFail($id);
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
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return redirect()->back()->with(AlertHelper::success('Ujian berhasil dihapus', 'Success'));
    } catch (\Exception $e) {
        return redirect()->back()->with(AlertHelper::error('Gagal menghapus ujian: ' . $e->getMessage(), 'Error'));
    }
}




}
