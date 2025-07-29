<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Batches;
use Illuminate\Http\Request;
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
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

       
        if ($request->status === 'active') {
            $activeBatch = Batches::where('status', 'active')->first();
            if ($activeBatch) {
                return redirect()->back()
                    ->with(AlertHelper::error('Gagal menambah batch. Sudah ada batch yang aktif: '. $activeBatch->nama, 'Error'))
                    ->withInput();
            }
        }

        try {
            Batches::create([
                'nama' => $request->nama,
                'status' => $request->status
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
            'status' => 'required|in:active,inactive'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $batch = Batches::findOrFail($id);

        if ($request->status === 'active') {
            $activeBatch = Batches::where('status', 'active')
                ->where('id', '!=', $id)
                ->first();
            
            if ($activeBatch) {
                return redirect()->back()
                    ->with(AlertHelper::error('Gagal menambah batch. Sudah ada batch yang aktif: '. $activeBatch->nama, 'Error'))
                    ->withInput();
            }
        }

        try {
            $batch->update([
                'nama' => $request->nama,
                'status' => $request->status
            ]);

            return redirect()->back()
                ->with(AlertHelper::success('Batch berhasil diperbarui!', 'Success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(AlertHelper::error('Terjadi kesalahan saat mengubah batch.', 'Error'));
        }
    }

    public function destroy($id)
    {
        try {
            $batch = Batches::findOrFail($id);
            
    

            $batch->delete();

            return redirect()->back()
                ->with(AlertHelper::success('Batch berhasil dihapus!', 'Success'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with(AlertHelper::error('Terjadi kesalahan saat menghapus batch.', 'Error'));
        }
    }
}