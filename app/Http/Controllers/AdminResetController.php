<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Ujian;
use App\Models\Kelas;
use App\Models\HasilUjian;
use App\Models\JawabanSiswa;
use App\Models\Soal;
use Illuminate\Support\Facades\Log;

class AdminResetController extends Controller
{
    public function resetData(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access!'
            ], 403);
        }

        if (!Hash::check($request->password_confirmation, Auth::user()->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password yang Anda masukkan salah!'
            ], 400);
        }

        $resetData = $request->input('reset_data', []);
        
        if (empty($resetData)) {
            return response()->json([
                'success' => false,
                'message' => 'Pilih minimal satu data untuk direset!'
            ], 400);
        }

        try {
            DB::beginTransaction();

            $resetMessages = [];

            foreach ($resetData as $dataType) {
                switch ($dataType) {
                    case 'users':
                        $deletedUsers = User::where('id', '!=', Auth::id())->count();
                        User::where('id', '!=', Auth::id())->delete(); 
                        $resetMessages[] = "Semua Users ($deletedUsers data)";
                        break;

                    case 'pengajar':
                        $deletedPengajar = User::where('role', 'pengajar')->count();
                        User::where('role', 'pengajar')->delete();
                        $resetMessages[] = "Data Pengajar ($deletedPengajar data)";
                        break;

                    case 'siswa':
                        $deletedSiswa = User::where('role', 'siswa')->count();
                        User::where('role', 'siswa')->delete(); 
                        $resetMessages[] = "Data Siswa ($deletedSiswa data)";
                        break;

                    case 'ujian':
                        $deletedUjian = Ujian::count();
                        Ujian::query()->delete();
                        $resetMessages[] = "Ujian ($deletedUjian data)";
                        break;

                    case 'kelas':
                        $deletedKelas = Kelas::count();
                        Kelas::query()->delete(); 
                        $resetMessages[] = "Kelas ($deletedKelas data)";
                        break;

                    case 'hasil_ujian':
                        $deletedHasil = HasilUjian::count();
                        $deletedJawaban = JawabanSiswa::count();
                        HasilUjian::query()->delete(); 
                        JawabanSiswa::query()->delete(); 
                        $resetMessages[] = "Hasil Ujian ($deletedHasil data) & Jawaban Siswa ($deletedJawaban data)";
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil direset: ' . implode(', ', $resetMessages)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Reset data error: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat reset data: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }
}