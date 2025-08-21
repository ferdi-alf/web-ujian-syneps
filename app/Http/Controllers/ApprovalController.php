<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Mail\RegistrationLinkMail;
use App\Models\PendaftaranPeserta;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ApprovalController extends Controller
{
    public function index()
    {
        $peserta = PendaftaranPeserta::with([
            'kelas:id,nama,type,durasi_belajar,waktu_magang',
            'batches:id,nama'
        ])->get();

        

        return view('Dashboard.Approval', compact('peserta'));
    }


    public function update(Request $request, $id) 
{
    Log::info('=== START EMAIL REGISTRATION PROCESS ===');
    Log::info('Request ID: ' . $id);
    Log::info('Request data: ', $request->all());
    
    try {
        DB::beginTransaction();
        Log::info('Database transaction started');
        
        $peserta = PendaftaranPeserta::findOrFail($id);
        Log::info('Peserta found: ', [
            'id' => $peserta->id,
            'name' => $peserta->name ?? 'N/A',
            'email' => $peserta->email,
            'current_status' => $peserta->status
        ]);

        if($peserta->status !== 'pending') {
            Log::warning('Peserta status is not pending: ' . $peserta->status);
            return redirect()->back()->with('error', 'Status peserta tidak dapat diubah.');
        }

        // Update status
        $updateResult = $peserta->update(['status' => 'confirmed']);
        Log::info('Status update result: ' . ($updateResult ? 'SUCCESS' : 'FAILED'));
        
        // Generate token
        $token = Str::random(60);
        Log::info('Generated token: ' . $token);
        
        // Store token in cache
        $cacheKey = "registration_token_{$token}";
        cache()->put($cacheKey, $peserta->id, now()->addHours(24));
        Log::info('Token stored in cache with key: ' . $cacheKey);
        Log::info('Cache verification: ' . (cache()->has($cacheKey) ? 'SUCCESS' : 'FAILED'));

        Log::info('Current mail configuration: ', [
            'mailer' => config('mail.default'),
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'encryption' => config('mail.mailers.smtp.encryption'),
            'username' => config('mail.mailers.smtp.username'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name')
        ]);

        $registrationUrl = route('registration.form', $token);
        Log::info('Generated registration URL: ' . $registrationUrl);

        Log::info('Attempting to send email to: ' . $peserta->email);
        
        try {
            Mail::to($peserta->email)->send(new RegistrationLinkMail($peserta, $token));
            Log::info('Email sent successfully to: ' . $peserta->email);
        } catch (\Exception $mailException) {
            Log::error('Email sending failed: ' . $mailException->getMessage());
            Log::error('Email exception trace: ' . $mailException->getTraceAsString());
            throw $mailException;
        }

        DB::commit();
        Log::info('Database transaction committed successfully');
        Log::info('=== EMAIL REGISTRATION PROCESS COMPLETED SUCCESSFULLY ===');

        return back()->with(AlertHelper::success('Peserta berhasil dikonfirmasi. Link registrasi telah dikirim ke email peserta.', 'Success'));
        
    } catch (\Throwable $th) {
        DB::rollback();
        Log::error('=== EMAIL REGISTRATION PROCESS FAILED ===');
        Log::error('Error message: ' . $th->getMessage());
        Log::error('Error file: ' . $th->getFile() . ' at line ' . $th->getLine());
        Log::error('Full error trace: ' . $th->getTraceAsString());
        
        return back()->with(AlertHelper::error('Terjadi kesalahan saat mengupdate status peserta: ' . $th->getMessage(), 'Error'));
    }
}

    public function resend(Request $request, $id)
    {
        try {
            $peserta = PendaftaranPeserta::findOrFail($id);
            
            if ($peserta->status !== 'confirmed') {
                return back()->with('error', 'Peserta belum dikonfirmasi');
            }

            $token = Str::random(60);
            cache()->put("registration_token_{$token}", $peserta->id, now()->addHours(24));
            Mail::to($peserta->email)->send(new RegistrationLinkMail($peserta, $token));

            return back()->with(AlertHelper::success('Link registrasi telah dikirim ulang ke email peserta.', 'Success'));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }


    public function delete($id)
    {
        try {
            $peserta = PendaftaranPeserta::findOrFail($id);
            
            if ($peserta->bukti_pembayaran_dp) {
                $filePath = public_path('uploads/images/dp/' . $peserta->bukti_pembayaran_dp);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            
            $peserta->delete();
            
            return back()->with('success', 'Pendaftaran berhasil ditolak dan data telah dihapus');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

}
