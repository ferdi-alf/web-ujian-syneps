<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Batches;
use App\Models\Kelas;
use Illuminate\Bus\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BatchController extends Controller
{
    /**
     * Store a newly created batch
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,registration',
            'kelas_id' => 'required|exists:kelas,id'

        ]);

        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

      if($request->status === 'registration') {
        $registrationBatch =  Batches::where('status', 'registration')
            ->where('kelas_id', $request->kelas_id)
            ->first();
        $kelas = Kelas::where('id', $request->kelas_id)->first();
        $namaKelas = $kelas ? $kelas->nama : 'Kelas tidak ditemukan';
        if ($registrationBatch) {
            return redirect()->back()
                 ->with(AlertHelper::error(
                        'Gagal menambahkan batch. Sudah ada batch yang aktif untuk kelas: ' . $namaKelas,
                        'Error'
                    ))
                    ->withInput();;
        }
      }

      if ($request->status === 'active') {
            $activeBatch = Batches::where('status', 'active')
                ->where('kelas_id', $request->kelas_id)
                ->first();
            $kelas = Kelas::where('id', $request->kelas_id)->first();
            $namaKelas = $kelas ? $kelas->nama : 'kelas tidak ditemukan';

            if ($activeBatch) {
                return redirect()->back()
                    ->with(AlertHelper::error(
                        'Gagal menambahkan batch. Sudah ada batch yang aktif untuk kelas: ' . $namaKelas,
                        'Error'
                    ))
                    ->withInput();
            }
        }
        try {
            Batches::create([
                'nama' => $request->nama,
                'status' => $request->status,
                'kelas_id' => $request->kelas_id
            ]);

            return redirect()->back()
                ->with(AlertHelper::success('Batch berhasil ditambahkan!', 'Success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(AlertHelper::error('Terjadi kesalahan saat menambah batch.', 'Error'))
                ->withInput();
        }
    }

    /**
     * Update the specified batch
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required|string|max:255',
            'status' => 'required|in:active,inactive,finished, registration',
     
        ]);
         if ($request->kelas_id) {
            return back()->withErrors(AlertHelper::error('Kelas tidak bisa dirubah', 'Error'));
        }
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $batch = Batches::findOrFail($id);

            if ($request->status === 'active') {
                $kelasId = $batch->kelas_id; 
                $kelas = Kelas::where('id', $kelasId)->first();
                $namaKelas = $kelas ? $kelas->nama : 'kelas tidak ditemukan';
                $activeBatch = Batches::where('status', 'active')
                    ->where('id', '!=', $id)
                    ->where('kelas_id', $kelasId)
                    ->first();
    
                if ($activeBatch) {
                    return redirect()->back()
                        ->with(AlertHelper::error(
                            'Gagal mengubah batch. Sudah ada batch yang aktif untuk kelas ini: ' . $namaKelas,
                            'Error'
                        ))
                        ->withInput();
                }
        }



        try {
            $batch->update([
                'nama' => $request->nama,
                'status' => $request->status,
            ]);

            if ($request->status === "finished") {
                $siswaDetails = $batch->siswaDetails;

                foreach ($siswaDetails as $siswaDetail) {
                    $siswaDetail->update([
                        'status' => 'alumni'
                    ]);

                    $jumlahSiswa = $siswaDetails->count();
                    DB::commit();

                    return redirect()->back()
                        ->with(AlertHelper::success( "Batch berhasil diperbarui menjadi finished! {$jumlahSiswa} siswa telah diubah statusnya menjadi alumni.", 
                    'Success'));
                }
            }
             DB::commit();
            return redirect()->back()
                ->with(AlertHelper::success('Batch berhasil diperbarui!', 'Success'));
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with(AlertHelper::error("Terjadi kesalahan saat mengubah batch. {$e->getMessage()}", 'Error'));
        }
    }


   public function destroy($id)
{
    try {
        $batch = Batches::with(['siswaDetails.siswa', 'ujians'])->findOrFail($id);

        // Hapus user yang terhubung ke siswaDetails
        foreach ($batch->siswaDetails as $siswaDetail) {
            if ($siswaDetail->siswa) {
                $siswaDetail->siswa->delete();
            }
        }


        $batch->siswaDetails()->delete();


        $batch->ujians()->delete();


        $batch->delete();

        return redirect()->back()
            ->with(AlertHelper::success('Batch dan seluruh data terkait berhasil dihapus!', 'Success'));
    } catch (\Exception $e) {
        Log::error($e->getMessage());

        return redirect()->back()
            ->with(AlertHelper::error('Terjadi kesalahan saat menghapus batch.', 'Error'));
    }
}


}