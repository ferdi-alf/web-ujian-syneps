<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Batches;
use App\Models\PendaftaranPeserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LandingController extends Controller
{
    /**
     * Halaman utama dengan daftar kelas
     */
    public function index()
    {
        try {
            // Ambil semua kelas dari database
            $kelas = Kelas::all();
            
            // Log untuk debugging
            Log::info('Landing page loaded with ' . $kelas->count() . ' kelas');
            
            return view('welcome', compact('kelas'));
        } catch (\Exception $e) {
            // Jika ada error, tetap tampilkan halaman dengan data kosong
            Log::error('Error loading landing page: ' . $e->getMessage());
            $kelas = collect(); // Empty collection
            return view('welcome', compact('kelas'));
        }
    }
    
    public function kelasDetail($id)
    {
        try {
            Log::info('KelasDetail method called with ID: ' . $id);
            
            $kelasDetail = Kelas::findOrFail($id);
            Log::info('Kelas found: ' . $kelasDetail->nama);
            
            $kelas = Kelas::all();
            Log::info('Total kelas for carousel: ' . $kelas->count());
            
            $activeBatch = Batches::where('kelas_id', $id)
                                 ->where('status', 'registration')
                                 ->first();
            
            if (!$activeBatch) {
                $activeBatch = (object) [
                    'id' => 'dummy-batch-' . $id,
                    'nama' => 'Batch Testing - ' . $kelasDetail->nama,
                    'status' => 'registration',
                    'kelas_id' => $id
                ];
                Log::info('Created dummy batch: ' . $activeBatch->nama);
            } else {
                Log::info('Found active batch: ' . $activeBatch->nama);
            }
            
            $showKelasDetail = true;
            $hideNavFooter = true;
            Log::info('Rendering welcome view with showKelasDetail: ' . ($showKelasDetail ? 'true' : 'false'));
            
            return view('welcome', compact('kelasDetail', 'activeBatch', 'showKelasDetail', 'hideNavFooter'));
            
        } catch (\Exception $e) {
            Log::error('Error loading kelas detail: ' . $e->getMessage());
            return redirect()->route('index')->with('error', 'Kelas tidak ditemukan.');
        }
    }
    
    /**
     * Handle form pendaftaran dari landing page
     */
    public function daftar(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'nama_lengkap' => 'required|string|max:255',
                'email' => 'required|email|unique:pendaftaran_peserta,email',
                'no_hp' => 'required|string|max:20',
                'alamat' => 'required|string',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'pendidikan_terakhir' => 'nullable|string|max:255',
                'mengetahui_program_dari' => 'required|in:Instagram,Tiktok,Facebook,Website,Teman/Keluarga,Google,Lain-lain',
                'total_tagihan' => 'required|numeric',
                'jumlah_cicilan' => 'required|integer|min:1|max:12',
                'tagihan_per_bulan' => 'nullable|numeric',
            ], [
                'kelas_id.required' => 'Kelas harus dipilih',
                'kelas_id.exists' => 'Kelas tidak valid',
                'nama_lengkap.required' => 'Nama lengkap harus diisi',
                'email.required' => 'Email harus diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'no_hp.required' => 'Nomor HP harus diisi',
                'alamat.required' => 'Alamat harus diisi',
                'jenis_kelamin.required' => 'Jenis kelamin harus dipilih',
                'mengetahui_program_dari.required' => 'Pilih dari mana Anda mengetahui program ini',
                'total_tagihan.required' => 'Total tagihan harus ada',
                'jumlah_cicilan.required' => 'Jumlah cicilan harus dipilih',
            ]);

            // Cari batch yang sedang buka pendaftaran untuk kelas ini
            $activeBatch = Batches::where('kelas_id', $request->kelas_id)
                                 ->where('status', 'registration')
                                 ->first();

            // Jika tidak ada batch aktif, buat dummy untuk testing
            if (!$activeBatch) {
                Log::warning('No active batch found during registration, using dummy batch');
                // Untuk testing, kita tetap proses tapi dengan batch dummy
                $dummyBatchId = 'dummy-batch-' . $request->kelas_id;
            } else {
                $dummyBatchId = $activeBatch->id;
            }

            // Simpan data pendaftaran
            PendaftaranPeserta::create([
                'batch_id' => $dummyBatchId,
                'kelas_id' => $request->kelas_id,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'mengetahui_program_dari' => $request->mengetahui_program_dari,
                'total_tagihan' => $request->total_tagihan,
                'jumlah_cicilan' => $request->jumlah_cicilan,
                'tagihan_per_bulan' => $request->tagihan_per_bulan,
                'status' => 'pending',
            ]);

            Log::info('New registration: ' . $request->email . ' for kelas ID: ' . $request->kelas_id);

            return back()->with('success', 'Pendaftaran berhasil! Tim kami akan menghubungi Anda dalam 1x24 jam.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validation errors akan otomatis di-handle Laravel
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Terjadi kesalahan sistem. Silakan coba lagi.'])
                       ->withInput();
        }
    }
}