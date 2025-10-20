<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Kelas;
use App\Models\Batches;
use App\Models\PendaftaranPeserta;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LandingController extends Controller
{
    /**
     * Halaman utama dengan daftar kelas
     */
    public function index()
    {
        try {
            $kelasWithActiveBatch = Kelas::with(['batches' => function($query) {
            $query->where('status', 'registration');
            }])->get();
            
            $kelas = $kelasWithActiveBatch->filter(function($kelasItem) {
                return $kelasItem->hasRegistrationBatch();
            });
        
            return view('welcome', compact('kelas'));
        } catch (\Exception $e) {
           
            Log::error('Error loading landing page: ' . $e->getMessage());
            $kelas = collect(); // Empty collection
            return view('welcome', compact('kelas'));
        }
    }
    
    public function kelasDetail($slug)
    {
        try {
            Log::info('KelasDetail method called with slug: ' . $slug);
            
            $registrationBatch = Batches::findRegistrationBySlug($slug);
            
            if (!$registrationBatch) {
                Log::warning('No registration batch found for slug: ' . $slug);
                abort(404, 'Kelas tidak ditemukan atau tidak tersedia.');
            }
            
            $kelasDetail = $registrationBatch->kelas;
            Log::info('Kelas found: ' . $kelasDetail->nama);
            Log::info('registration batch found: ' . $registrationBatch->nama);
            
            $showKelasDetail = true;
            $hideNavFooter = true;
            
            return view('welcome', compact('kelasDetail', 'registrationBatch', 'showKelasDetail', 'hideNavFooter'));
            
        } catch (\Exception $e) {
            Log::error('Error loading kelas detail: ' . $e->getMessage());
            abort(404, 'Kelas tidak ditemukan.');
        }
    }
    
    /**
     * Handle form pendaftaran dari landing page
     */
    public function daftar(Request $request)
    {
        try {
            $request->validate([
                'kelas_id' => 'required|exists:kelas,id',
                'nama_lengkap' => 'required|string|max:255',
                'email' => 'required|email|unique:pendaftaran_peserta,email',
                'no_hp' => 'required|string|max:20',
                'alamat' => 'required|string',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'pendidikan_terakhir' => 'nullable|string|max:255',
                'mengetahui_program_dari' => 'required|in:Instagram,Tiktok,Facebook,Website,Teman/Keluarga,Google,Lain-lain',
                'bukti_pembayaran_dp' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            ]);


            $user = User::where('email', $request->email)->first();

            if ($user) {
                return back()->with(AlertHelper::error('Email yang anda masukan sudah terdaftar', 'Error'));
            }

            $activeBatch = Batches::where('kelas_id', $request->kelas_id)
                                ->where('status', 'registration')
                                ->first();

            if (!$activeBatch) {
                return redirect()->route('welcome')
                    ->with(AlertHelper::error('Tidak ada batch aktif untuk kelas ini', 'Error'));
            }

            $kelas = Kelas::findOrFail($request->kelas_id);
            $harga = $kelas->harga;
            $dpPersen = $kelas->dp_persen ?? 0;
            $dp = ($harga * $dpPersen) / 100;
            $sisaTagihan = $harga - $dp;

            $totalBulan = ($kelas->durasi_belajar ?? 0) + ($kelas->waktu_magang ?? 0);
            $tagihanPerBulan = $totalBulan > 0 ? $sisaTagihan / $totalBulan : 0;

            $buktiPath = null;
            if ($request->hasFile('bukti_pembayaran_dp')) {
                $buktiPath = $request->file('bukti_pembayaran_dp')->store('dp', 'public');
            }

            PendaftaranPeserta::create([
                'batch_id' => $activeBatch->id,
                'kelas_id' => $kelas->id,
                'nama_lengkap' => $request->nama_lengkap,
                'email' => $request->email,
                'no_hp' => $request->no_hp,
                'alamat' => $request->alamat,
                'jenis_kelamin' => $request->jenis_kelamin,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'mengetahui_program_dari' => $request->mengetahui_program_dari,
                'tagihan_per_bulan' => $tagihanPerBulan,
                'status' => 'pending',
                'total_tagihan' => $sisaTagihan,
                'jumlah_cicilan' => $totalBulan,
                'bukti_pembayaran_dp' => $buktiPath,
            ]);

            return redirect()->route('index')
                ->with(AlertHelper::success('Pendaftaran berhasil! Tim kami akan menghubungi Anda dalam 1x24 jam.', 'Success'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            Log::error('Error during registration: ' . $e->getMessage());
            return redirect()->route('index')
                ->with(AlertHelper::error('Terjadi kesalahan sistem: ' . $e->getMessage(), 'Error'));
        }
    }

}