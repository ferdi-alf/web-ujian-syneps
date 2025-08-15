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
    
    /**
     * Halaman detail kelas
     */
    public function kelasDetail($id)
    {
        try {
            // Cari kelas berdasarkan ID
            $kelas = Kelas::findOrFail($id);
            
            // Cek batch yang sedang buka pendaftaran untuk kelas ini
            $activeBatch = Batches::where('kelas_id', $id)
                                 ->where('status', 'registration')
                                 ->first();
            
            Log::info('Kelas detail loaded: ' . $kelas->nama);
            
            return view('kelas-detail', compact('kelas', 'activeBatch'));
        } catch (\Exception $e) {
            Log::error('Error loading kelas detail: ' . $e->getMessage());
            
            // Redirect ke homepage dengan error message
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
            ]);

            // Cari batch yang sedang buka pendaftaran untuk kelas ini
            $activeBatch = Batches::where('kelas_id', $request->kelas_id)
                                 ->where('status', 'registration')
                                 ->first();

            if (!$activeBatch) {
                return back()->withErrors(['error' => 'Maaf, pendaftaran untuk kelas ini sedang tidak dibuka.'])
                           ->withInput();
            }

            // Simpan data pendaftaran
            PendaftaranPeserta::create([
                'batch_id' => $activeBatch->id,
                'kelas_id' => $request->kelas_id,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'mengetahui_program_dari' => $request->mengetahui_program_dari,
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