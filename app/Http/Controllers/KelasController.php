<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KelasController extends Controller
{
    public function index() {
        $kelas = Kelas::select('id', 'nama', 'harga', 'dp_persen', 'type', 'durasi_belajar', 'waktu_magang', 'created_at')->get();
        return view('Dashboard.Kelas', compact('kelas'));
    }


    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string',
            'price_numeric' => 'required|numeric|min:0',
            'dp_persen' => 'required|numeric|min:0|max:100',
            'type' => 'nullable|string|in:intensif,partime',
            'durasi_belajar' => 'required|numeric|min:1',
            'waktu_magang' => 'nullable|numeric|min:0'
        ], [
            'name.required' => 'Nama Kelas tidak boleh kosong',
            'price_numeric.required' => 'Harga kelas tidak boleh kosong',
            'price_numeric.numeric' => 'Harga harus berupa angka',
            'price_numeric.min' => 'Harga tidak boleh kurang dari 0',
            'dp_persen.required' => 'DP persen tidak boleh kosong',
            'dp_persen.numeric' => 'DP persen harus berupa angka',
            'dp_persen.min' => 'DP persen tidak boleh kurang dari 0',
            'dp_persen.max' => 'DP persen tidak boleh lebih dari 100',
            'type.in' => 'Type kelas harus intensif atau partime',
            'durasi_belajar.required' => 'Durasi belajar tidak boleh kosong',
            'durasi_belajar.numeric' => 'Durasi belajar harus berupa angka',
            'durasi_belajar.min' => 'Durasi belajar minimal 1 bulan',
            'waktu_magang.numeric' => 'Durasi magang harus berupa angka',
            'waktu_magang.min' => 'Durasi magang tidak boleh kurang dari 0'
        ]);

        try {
            Kelas::create([
                'nama' => $request->name,
                'harga' => $request->price_numeric,
                'dp_persen' => $request->dp_persen,
                'type' => $request->type,
                'durasi_belajar' => $request->durasi_belajar,
                'waktu_magang' => $request->waktu_magang ?? 0  // Menggunakan default 0 jika null
            ]);
            return back()->with(AlertHelper::success('Berhasil menambahkan kelas '. $request->name , 'Success'));
        } catch (\Throwable $th) {
            Log::error('gagal menambahkan data' . $th->getMessage());
            return back()->withErrors(AlertHelper::error('gagal menambahkan data' . $th->getMessage(), 'Error'));
        }
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|string',
            'price_numeric' => 'required|numeric|min:0',
            'dp_persen' => 'required|numeric|min:0|max:100',
            'type' => 'nullable|string|in:intensif,partime',
            'durasi_belajar' => 'required|numeric|min:1',
            'waktu_magang' => 'nullable|numeric|min:0'
        ], [
            'name.required' => 'Nama Kelas tidak boleh kosong',
            'price_numeric.required' => 'Harga kelas tidak boleh kosong',
            'price_numeric.numeric' => 'Harga harus berupa angka',
            'price_numeric.min' => 'Harga tidak boleh kurang dari 0',
            'dp_persen.required' => 'DP persen tidak boleh kosong',
            'dp_persen.numeric' => 'DP persen harus berupa angka',
            'dp_persen.min' => 'DP persen tidak boleh kurang dari 0',
            'dp_persen.max' => 'DP persen tidak boleh lebih dari 100',
            'type.in' => 'Type kelas harus intensif atau partime',
            'durasi_belajar.required' => 'Durasi belajar tidak boleh kosong',
            'durasi_belajar.numeric' => 'Durasi belajar harus berupa angka',
            'durasi_belajar.min' => 'Durasi belajar minimal 1 bulan',
            'waktu_magang.numeric' => 'Durasi magang harus berupa angka',
            'waktu_magang.min' => 'Durasi magang tidak boleh kurang dari 0'
        ]);

        try {
            $kelas = Kelas::find($id);

            if (!$kelas) {
                return back()->withErrors(AlertHelper::error('Kelas tidak ditemukan', 'Error'));
            }

            $kelas->update([
                'nama' => $request->name,
                'harga' => $request->price_numeric,
                'dp_persen' => $request->dp_persen,
                'type' => $request->type,
                'durasi_belajar' => $request->durasi_belajar,
                'waktu_magang' => $request->waktu_magang ?? 0  
            ]);

            return back()->with(AlertHelper::success('Berhasil memperbarui kelas ' . $request->name, 'Success'));
        } catch (\Throwable $th) {
            Log::error('Gagal memperbarui data: ' . $th->getMessage());
            return back()->withErrors(AlertHelper::error('Gagal memperbarui data: ' . $th->getMessage(), 'Error'));
        }
    }


    public function destroy($id)
        {
            $kelas = Kelas::find($id);

            if (!$kelas) {
                return back()->withErrors(AlertHelper::error('Kelas tidak ditemukan', 'Error'));
            }

            $kelas->delete(); 

            return back()->with(AlertHelper::success('Kelas ' . $kelas->nama . ' berhasil dihapus', 'Success'));
    }

}
