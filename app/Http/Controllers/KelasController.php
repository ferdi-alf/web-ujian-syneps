<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KelasController extends Controller
{
    public function index() {
        return view('Dashboard.Kelas');
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
}
