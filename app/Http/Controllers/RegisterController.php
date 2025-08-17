<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Mail\VerificationCodeMail;
use App\Models\PendaftaranPeserta;
use App\Models\SiswaDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
     public function showRegistrationForm($token) {
        $pesertaId = cache()->get("registration_token_{$token}");
        if (!$pesertaId) {
            return redirect()->route('login.index')->with(AlertHelper::error('Link registrasi tidak valid atau sudah kedaluwarsa', 'Error'));
        }

        $peserta = PendaftaranPeserta::find($pesertaId);
        if(!$peserta || $peserta->status !== 'confirmed') {
            return redirect()->route('index')->with(AlertHelper::error('Data pendaftaran tidak ditemukan', 'Error'));
        }


        return view('auth.register-form', compact('peserta', 'token'));
    }

    public function processRegistration(Request $request, $token) {
       $pesertaId = cache()->get("registration_token_{$token}");
        
        if (!$pesertaId) {
            return back()->with('error', 'Link registrasi tidak valid atau sudah kedaluwarsa');
        }

        $peserta = PendaftaranPeserta::find($pesertaId);
        
        if (!$peserta || $peserta->status !== 'confirmed') {
            return back()->with('error', 'Data pendaftaran tidak ditemukan');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'avatar' => 'avatar-' . rand(1, 10) . '.png',
                'name' => $request->name,
                'email' => $peserta->email,
                'password' => Hash::make($request->password),
            ]);

             $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
             cache()->put("verification_code_{$user->id}", $verificationCode, now()->addMinutes(10));
             cache()->forget("registration_token_{$token}");
             Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));
             DB::commit();
            
             return redirect()->route('verification.show', ['user' => $user->id])
                ->with(AlertHelper::success('Registrasi berhasil! Silakan verifikasi email Anda.', 'Success'));
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with(AlertHelper::error('Terjadi kesalahan saat proses registrasi: ' . $th->getMessage(), 'Error'));
        }
    }

    public function showVerificationForm($userId) {
         $user = User::findOrFail($userId);
        
        if ($user->email_verified_at) {
            return redirect()->route('dashboard')->with('info', 'Email sudah terverifikasi');
        }

        return view('auth.verification-form', compact('user'));
    }

    public function processVerification(Request $request, $userId) {
         $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);

        $user = User::findOrFail($userId);
        
        if ($user->email_verified_at) {
            return redirect()->route('dashboard')->with('info', 'Email sudah terverifikasi');
        }

        $storedCode = cache()->get("verification_code_{$user->id}");
        
        if (!$storedCode || $storedCode !== $request->verification_code) {
            return back()->with('error', 'Kode verifikasi salah atau sudah kedaluwarsa');
        }

        try {
            DB::beginTransaction();

            $user->update(['email_verified_at' => now()]);
            $peserta = PendaftaranPeserta::where('email', $user->email)->first();
            
            if ($peserta) {
                SiswaDetail::create([
                    'siswa_id' => $user->id,
                    'kelas_id' => $peserta->kelas_id,
                    'batch_id' => $peserta->batch_id,
                    'nama_lengkap' => $peserta->nama_lengkap,
                    'no_hp' => $peserta->no_hp,
                    'status' => 'active',
                    'alamat' => $peserta->alamat,
                    'pendidikan_terakhir' => $peserta->pendidikan_terakhir,
                    'jenis_kelamin' => $peserta->jenis_kelamin,
                    'mengetahui_program_dari' => $peserta->mengetahui_program_dari,
                    'total_tagihan' => $peserta->total_tagihan,
                    'jumlah_cicilan' => $peserta->jumlah_cicilan,
                    'tagihan_per_bulan' => $peserta->tagihan_per_bulan,
                    'ikut_magang' => false,
                ]);

                $peserta->delete();
            }

            cache()->forget("verification_code_{$user->id}");

            DB::commit();
            Auth::login($user);

            return redirect()->route('dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang di platform kami.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function resendVerificationCode($userId) {
        $user = User::findOrFail($userId);
          if ($user->email_verified_at) {
            return back()->with('info', 'Email sudah terverifikasi');
        }

        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        cache()->put("verification_code_{$user->id}", $verificationCode, now()->addMinutes(10));

        Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));
         return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda');
    }
}
