<?php

namespace App\Http\Controllers;

use App\Models\PendaftaranPeserta;
use Illuminate\Http\Request;

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

}
