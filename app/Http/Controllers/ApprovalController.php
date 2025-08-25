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
        
        try {
            DB::beginTransaction();
            $mode = env('REGISTER_SENDING_MODE', 'email');
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

          
            $peserta->update(['status' => 'confirmed']);
            $token = Str::random(60);
            Log::info('Generated token: ' . $token);
            $cacheKey = "registration_token_{$token}";
            cache()->put($cacheKey, $peserta->id, now()->addHours(24));
          
            try {
                if ($mode === 'whatsapp') {
                    Log::info('Sending registration link via WhatsApp');
                    $link = env('APP_URL') . route('registration.form', ['token' => $token], false);
                    Log::info('WhatsApp link: ' . $link);
                    $peserta->link_register = $link;
                    $peserta->save();
                } else {
                    Log::info('Sending registration link via Email');
                    Mail::to($peserta->email)->send(new RegistrationLinkMail($peserta, $token));
                }
            } catch (\Exception $mailException) {
                Log::error('Email sending failed: ' . $mailException->getMessage());
                Log::error('Email exception trace: ' . $mailException->getTraceAsString());
                throw $mailException;
            }

            DB::commit();

            return back()->with(AlertHelper::success('Peserta berhasil dikonfirmasi' . $mode === "email" ? 'Link registrasi telah dikirim ke email peserta.' : 'Link Registrasi telah dibuat', 'Success'));
            
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
            $mode = env('REGISTER_SENDING_MODE', 'email');
            
            if ($peserta->status !== 'confirmed') {
                return back()->with('error', 'Peserta belum dikonfirmasi');
            }
            $token = Str::random(60);
            cache()->put("registration_token_{$token}", $peserta->id, now()->addHours(24));
            
            if($mode === "email") {
                Mail::to($peserta->email)->send(new RegistrationLinkMail($peserta, $token));
            }else {
                Log::info('Sending registration link via WhatsApp');
                $link = url(route('registration.form', ['token' => $token], false));
                Log::info('WhatsApp link: ' . $link);
                $peserta->link_register = $link;
                $peserta->save();
            }

            return back()->with(AlertHelper::success($mode === "email" ? 'Link registrasi telah dikirim ulang ke email peserta.' : 'Link Registrasi telah diperbarui', 'Success'));
            
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
