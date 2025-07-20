<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KelasController extends Controller
{
    public function index() {
        $kelas = Kelas::select('id', 'nama', 'created_at')->get();
        return view('Dashboard.Kelas', compact('kelas'));
    }


    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string'
        ],[
            'name.required' => 'Nama Kelas Tidak Boleh Kosong'
        ]);

        try {
            Kelas::create([
                'nama' =>  $request->name
            ]);
            return back()->with(AlertHelper::success('Berhasil menambahkan kelas ', $request->name, 'Success'));
        } catch (\Throwable $th) {
            Log::error('gagal menambahkan data' . $th->getMessage());
            AlertHelper::error('gagal menambahkan data' . $th->getMessage(), 'Error');
        }
    }

            public function update(Request $request, $id)
        {
            $request->validate([
                'name' => 'required|string'
            ],[
                'name.required' => 'Nama Kelas Tidak Boleh Kosong'
            ]);

            try {
                $kelas = Kelas::find($id);

                if (!$kelas) {
                    return back()->withErrors(AlertHelper::error('Kelas tidak ditemukan', 'Error'));
                }

                $kelas->nama = $request->name;
                $kelas->save();

                return back()->with(AlertHelper::success('Berhasil memperbarui kelas ', $request->name, 'Success'));
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
