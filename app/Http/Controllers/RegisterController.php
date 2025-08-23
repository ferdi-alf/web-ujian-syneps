<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Mail\VerificationCodeMail;
use App\Models\PendaftaranPeserta;
use App\Models\SiswaDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
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

    public function processRegistration(Request $request, $token)
    {
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
            'password' => ['required', Password::min(8)],
            'confirmPassword' => 'required|same:password',
        ]);
        
        try {
            DB::beginTransaction();
            $mode = env('REGISTER_SENDING_MODE', 'email');
            
            $user = User::create([
                'avatar' => 'avatar-' . rand(1, 10) . '.png',
                'name' => $request->name,
                'email' => $peserta->email,
                'password' => Hash::make($request->password),
            ]);

            if ($mode === 'email') {
                // MODE EMAIL - Kirim verification code
                $verificationToken = Str::random(64);
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                
                cache()->put("verification_token_{$verificationToken}", [
                    'user_id' => $user->id,
                    'code' => $verificationCode,
                    'attempts' => 0,
                    'created_at' => now(),
                    'last_resend' => now(), 
                ], now()->addMinutes(15)); 
                
                cache()->forget("registration_token_{$token}");
                
                Mail::to($user->email)->send(new VerificationCodeMail($user, $verificationCode));
                
                DB::commit();
                
                Log::info('Verification code sent to user email', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'token' => $verificationToken, 
                ]);
                
                return redirect()->route('verification.show', ['token' => $verificationToken])
                    ->with(AlertHelper::success('Registrasi berhasil! Silakan verifikasi email Anda.', 'Success'));
                    
            } else {
                // MODE WHATSAPP - Langsung create SiswaDetail dan login
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
                
                // Hapus data pendaftaran dan registration token
                $peserta->delete();
                cache()->forget("registration_token_{$token}");
                
                // Set email as verified (karena tidak perlu verifikasi)
                $user->update(['email_verified_at' => now()]);
                
                DB::commit();
                
                // Login user dan redirect ke dashboard
                Auth::login($user);
                
                Log::info('User registered successfully via WhatsApp mode', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
                
                return redirect()->route('dashboard')
                    ->with(AlertHelper::success('Registrasi berhasil! Selamat datang.', 'Success'));
            }
            
        } catch (\Throwable $th) {
            DB::rollback();
            
            Log::error('Registration process failed', [
                'error' => $th->getMessage(),
                'mode' => $mode ?? 'unknown',
            ]);
            
            return back()->with(AlertHelper::error('Terjadi kesalahan saat proses registrasi: ' . $th->getMessage(), 'Error'));
        }
    }
    

     public function showVerificationForm($token)
    {
        $verificationData = cache()->get("verification_token_{$token}");
        
        if (!$verificationData) {
            return redirect()->route('login')->with('error', 'Token verifikasi tidak valid atau sudah kedaluwarsa');
        }
        
        $user = User::find($verificationData['user_id']);
        
        if (!$user) {
            cache()->forget("verification_token_{$token}");
            return redirect()->route('login')->with('error', 'User tidak ditemukan');
        }
        
        if ($user->email_verified_at) {
            return redirect()->route('dashboard')->with('success', 'Email sudah terverifikasi');
        }
        
        $lastResend = Carbon::parse($verificationData['last_resend']);
        $cooldownSeconds = 30;
        $timePassed = $lastResend->diffInSeconds(now());
        $remainingCooldown = max(0, (int) ($cooldownSeconds - $timePassed));
        
        return view('auth.verification-form', compact('user', 'token', 'remainingCooldown'));
    }

     public function processVerification(Request $request, $token)
    {
        $key = 'verify_attempts:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'verification_code' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik."
            ]);
        }
        
        $verificationData = cache()->get("verification_token_{$token}");
        
        if (!$verificationData) {
            return redirect()->route('login')->with('error', 'Token verifikasi tidak valid atau sudah kedaluwarsa');
        }
        
        $user = User::find($verificationData['user_id']);
        
        if (!$user) {
            cache()->forget("verification_token_{$token}");
            return redirect()->route('login')->with('error', 'User tidak ditemukan');
        }
        
        $request->validate([
            'verification_code' => 'required|string|size:6',
        ]);
        
        RateLimiter::hit($key);
        $verificationData['attempts']++;
        if ($verificationData['attempts'] > 3) {
            cache()->forget("verification_token_{$token}");
            return redirect()->route('login')->with('error', 'Terlalu banyak percobaan verifikasi. Silakan registrasi ulang.');
        }
        
        if ($request->verification_code !== $verificationData['code']) {
            cache()->put("verification_token_{$token}", $verificationData, now()->addMinutes(15));
            
            $remainingAttempts = 3 - $verificationData['attempts'];
            return back()->withErrors([
                'verification_code' => "Kode verifikasi salah. Sisa percobaan: {$remainingAttempts}"
            ]);
        }
        
        try {
            DB::beginTransaction();
            
            $user->update([
                'email_verified_at' => now(),
            ]);
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
                        
            RateLimiter::clear($key);
            cache()->forget("verification_token_{$token}");
            
            DB::commit();
            Auth::login($user);
            
            return redirect()->route('dashboard')
                ->with(AlertHelper::success('Email berhasil diverifikasi!', 'Success'));
                
        } catch (\Throwable $th) {
            DB::rollback();
            Log::error('Email verification failed', [
                'error' => $th->getMessage(),
                'user_id' => $user->id,
            ]);
            return back()->with('error', 'Terjadi kesalahan saat verifikasi email');
        }
    }
    
    public function resendVerificationCode(Request $request, $token)
    {
        $resendKey = 'resend_code:' . request()->ip();
        if (RateLimiter::tooManyAttempts($resendKey, 3)) {
            $seconds = RateLimiter::availableIn($resendKey);
            return back()->with('error', "Terlalu banyak permintaan kirim ulang. Coba lagi dalam {$seconds} detik.");
        }
        
        $verificationData = cache()->get("verification_token_{$token}");
        
        if (!$verificationData) {
            return redirect()->route('login')->with('error', 'Token verifikasi tidak valid atau sudah kedaluwarsa');
        }
        
        $user = User::find($verificationData['user_id']);
        
        if (!$user) {
            cache()->forget("verification_token_{$token}");
            return redirect()->route('login')->with('error', 'User tidak ditemukan');
        }
        
        $lastResend = Carbon::parse($verificationData['last_resend']);
        if ($lastResend->diffInSeconds(now()) < 30) {
            $remainingSeconds = 30 - $lastResend->diffInSeconds(now());
            return back()->with('error', "Tunggu {$remainingSeconds} detik sebelum mengirim ulang kode.");
        }
        
        $newVerificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $verificationData['code'] = $newVerificationCode;
        $verificationData['last_resend'] = now();
        $verificationData['attempts'] = 0; // Reset attempts on resend
        
        cache()->put("verification_token_{$token}", $verificationData, now()->addMinutes(15));
        
        try {
            Mail::to($user->email)->send(new VerificationCodeMail($user, $newVerificationCode));
            
            RateLimiter::hit($resendKey);
            
            Log::info('Verification code resent', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            
            return back()->with(AlertHelper::success('Kode verifikasi baru telah dikirim!', 'Success'));
            
        } catch (\Throwable $th) {
            Log::error('Failed to resend verification code', [
                'error' => $th->getMessage(),
                'user_id' => $user->id,
            ]);
            return back()->with('error', 'Gagal mengirim kode verifikasi');
        }
    }
}
