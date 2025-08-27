<?php

namespace App\Http\Controllers;

use App\Models\Lamaran;
use App\Models\Lowongan;
use Illuminate\Http\Request;
use App\Helpers\AlertHelper;
use Illuminate\Support\Facades\Auth;

class LamaranController extends Controller
{
    public function store(Request $request, Lowongan $lowongan)
    {
        $request->validate([
            'resume' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'surat_lamaran' => 'required|string',
        ]);

        if ($lowongan->lamaran()->where('user_id', Auth::id())->exists()) {
            return AlertHelper::alertError('Anda sudah pernah melamar pada lowongan ini.', 409);
        }

        $resumePath = $request->file('resume')->store('resumes', 'public');

        $lowongan->lamaran()->create([
            'user_id' => Auth::id(),
            'resume_path' => $resumePath,
            'surat_lamaran' => $request->surat_lamaran,
            'status' => 'Pending',
        ]);

        return AlertHelper::alertSuccess('Lamaran Anda berhasil dikirim.');
    }
}
