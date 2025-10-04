<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Batches;
use App\Models\Pembayaran;
use App\Models\SiswaDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    public function index() {
        $data = Pembayaran::with('siswaDetail')
            ->where('status', 'pending')->get();
        return view('Dashboard.Pembayaran-Masuk', compact('data'));
    }

 public function history()
{
    $batchesAktif = Batches::with(['kelas', 'siswaDetails.pembayarans', 'siswaDetails.siswa'])
        ->where('status', 'active')
        ->get();

    $historyData = [];

    foreach ($batchesAktif as $batch) {
        $tanggalMulai = Carbon::parse($batch->tanggal_mulai);
        $tanggalSelesai = Carbon::parse($batch->tanggal_selesai);
        
        $bulanPertama = $tanggalMulai->copy()->startOfMonth();
        $bulanTerakhir = $tanggalSelesai->copy()->startOfMonth();
        
        $today = Carbon::today();
        $nextMonth = $today->copy()->addMonth()->startOfMonth();
        
        if ($bulanTerakhir->lt($nextMonth)) {
            $bulanTerakhir = $nextMonth;
        }
        
        $bulanData = [];

        $currentMonth = $bulanPertama->copy();
        while ($currentMonth->lte($bulanTerakhir)) {
            $bulanNama = $currentMonth->locale('id')->translatedFormat('F Y');
            $bulanKey = $currentMonth->format('Y-m');
            
            $pembayaranBulanIni = Pembayaran::whereHas('siswaDetail', function($query) use ($batch) {
                $query->where('batch_id', $batch->id);
            })
            ->where('status', 'disetujui')
            ->whereYear('tanggal_jatuh_tempo', $currentMonth->year)
            ->whereMonth('tanggal_jatuh_tempo', $currentMonth->month)
            ->with(['siswaDetail.siswa'])
            ->get();

            if ($pembayaranBulanIni->isNotEmpty()) {
                $pembayaranDetail = [];
                
                foreach ($pembayaranBulanIni as $pembayaran) {
                    $pembayaranDetail[] = [
                        'nama' => $pembayaran->siswaDetail->nama_lengkap,
                        'email' => $pembayaran->siswaDetail->siswa->email ?? '-',
                        'total_tagihan' => 'Rp ' . number_format($pembayaran->jumlah_dibayar, 0, ',', '.'),
                        'cicilan_ke' => $pembayaran->cicilan_ke,
                        'tanggal_jatuh_tempo' => Carbon::parse($pembayaran->tanggal_jatuh_tempo)->format('d/m/Y'),
                        'bukti_pembayaran' => $pembayaran->bukti_pembayaran,
                    ];
                }

                $bulanData[] = [
                    'bulan' => $bulanNama, 
                    'bulan_key' => $bulanKey,
                    'total' => $pembayaranBulanIni->count(),
                    'pembayaran' => $pembayaranDetail
                ];
            }

            $currentMonth->addMonth();
        }

        if (!empty($bulanData)) {
            $historyData[] = [
                'title' => 'History Pembayaran ' . $batch->kelas->nama . ' - Batch ' . $batch->nama,
                'kelas_nama' => $batch->kelas->nama,
                'batch_nama' => $batch->nama,
                'data' => $bulanData
            ];
        }
    }

    return view('Dashboard.History-Pembayaran', compact('historyData'));
}

    public function show($id)
    {
        try {
            $pembayaran = Pembayaran::findOrFail($id);
            $user = Auth::user()->siswaDetail->id;

            if ($pembayaran->siswa_detail_id !== $user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak berhak mengakses data ini'
                ], 403);
            }
            
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

        // dd($request->all());
        $pembayaran = Pembayaran::findOrFail($id);
        $user = Auth::user();
        $siswaDetail = SiswaDetail::where('id', $pembayaran->siswa_detail_id)->first();

        if ($user->role === 'admin') {
            if ($act === "approve") {
                $pembayaran->update([
                    'status' => 'disetujui',
                ]);
                $newCicilan = max($siswaDetail->jumlah_cicilan - 1, 0);
                $newTagihan = max($siswaDetail->total_tagihan - $pembayaran->jumlah_dibayar, 0);
                $siswaDetail->update([
                        'jumlah_cicilan' => $newCicilan,
                        'total_tagihan' => $newTagihan,
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
                'bukti_pembayaran' => 'required|image|max:5048',
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

            return redirect()->back()->with(AlertHelper::success('Bukti pembayaran berhasil diupload, menunggu konfirmasi', 'Success'));
        }

        return back()->withErrors(AlertHelper::error('Role tidak valid', 'Error'));
    }

}
