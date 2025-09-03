<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MateriController extends Controller
{
    public function index() {
        $role = Auth::user()->role;
         switch ($role) {
            case 'siswa':
                # code...
                break;
            
            default:
                $user = Auth::user();
                $kelas = collect();

            if ($user->role === 'admin') {
                    $kelas = Kelas::select('id', 'nama', 'type')->get();
                } elseif ($user->role === 'pengajar') {
                    $pengajarDetail = $user->pengajarDetail;
                    $kelasIds = $pengajarDetail ? $pengajarDetail->kelas()->pluck('kelas.id')->toArray() : [];
                    $kelas = Kelas::select('id', 'nama', 'type')->whereIn('id', $kelasIds)->get();
                }
                break;
         }
        return view('Dashboard.Materi', compact('kelas'));
    }
}
