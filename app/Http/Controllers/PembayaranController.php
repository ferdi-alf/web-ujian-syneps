<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index() {
        return view('Dashboard.Pembayaran-Masuk');
    }
    public function history() {
        return view('Dashboard.History-Pembayaran');
    }

    public function show($id)
    {
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $pembayaran->id,
                    'bukti_pembayaran' => $pembayaran->bukti_pembayaran,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id, $act = null)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'admin') {
            if ($act === "approve") {
                $pembayaran->update([
                    'status' => 'disetujui',
                ]);
                return redirect()->back()->with(AlertHelper::success('Pembayaran disetujui'));
            } elseif ($act === "reject") {
                $pembayaran->update([
                    'status' => 'ditolak',
                ]);
                return redirect()->back()->with(AlertHelper::success('Pembayaran ditolak', 'Success'));
            }
        }

       
        if ($user->role === 'siswa') {
        
            if ($pembayaran->siswa_detail_id !== $user->siswaDetail->id) {
                return back()->withErrors(AlertHelper::error('Data Tidak Valid', 'Error'));
            }

            $validated = $request->validate([
                'bukti_pembayaran' => 'required|image|max:2048',
            ]);


            if ($request->hasFile('bukti_pembayaran')) {
                if ($pembayaran->bukti_pembayaran) {
                    $oldPath = str_replace('/storage/', '', $pembayaran->bukti_pembayaran);
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('bukti_pembayaran')->store('pembayaran-bulanan', 'public');
                $pembayaran->bukti_pembayaran = '/storage/' . $path;
            }

            $pembayaran->status = 'pending';
            $pembayaran->save();

            return redirect()->back()->with('success', 'Bukti pembayaran berhasil diupload, menunggu konfirmasi');
        }

        return back()->withErrors(AlertHelper::error('Role tidak valid', 'Error'));
    }

}
