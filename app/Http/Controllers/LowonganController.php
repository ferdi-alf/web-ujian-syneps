<?php

namespace App\Http\Controllers;

use App\Models\Lowongan;
use Illuminate\Http\Request;
use App\Helpers\AlertHelper;
use Illuminate\Support\Facades\Auth;

class LowonganController extends Controller
{
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            $lowonganData = Lowongan::withCount('lamaran')->latest()->get();
            $lowonganForTable = $lowonganData->map(function ($item) {
                return [
                    'id' => $item->id,
                    'posisi' => $item->posisi,
                    'perusahaan' => $item->perusahaan,
                    'lokasi' => $item->lokasi,
                    'deadline' => \Carbon\Carbon::parse($item->deadline)->format('d F Y'),
                    'gaji' => 'Rp ' . number_format($item->gaji, 0, ',', '.'),
                    'pelamar' => '<a href="#" class="text-blue-500">' . $item->lamaran_count . ' Orang</a>',
                    'status' => '<span class="px-2 py-1 text-sm font-semibold rounded-full ' . ($item->status === 'Aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') . '">' . $item->status . '</span>',
                    'action' => view('components.action-buttons', [
                        'drawerId' => 'drawer-detail-lowongan-' . $item->id,
                        'modalId' => 'edit-lowongan-modal-' . $item->id,
                        'deleteRoute' => route('lowongan.destroy', $item->id),
                    ])->render(),
                ];
            });

            return view('Dashboard.Lowongan-Admin', [
                'lowonganData' => $lowonganData,
                'lowonganForTable' => $lowonganForTable,
            ]);
        } else {
            $lowongan = Lowongan::where('status', 'Aktif')->latest()->get();
            return view('Dashboard.Lowongan-Alumni', compact('lowongan'));
        }
    }

    public function store(Request $request)
    {
        if ($request->has('gaji_numeric')) {
            $request->merge(['gaji' => $request->input('gaji_numeric')]);
        }

        $request->validate([
            'posisi' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'gaji' => 'nullable|numeric',
            'deskripsi' => 'required|string',
            'status' => 'required|in:Aktif,Ditutup',
            'deadline' => 'required|date',
        ]);

        Lowongan::create($request->except('gaji_numeric'));

        return redirect()->back()->with(AlertHelper::success('Data lowongan berhasil ditambahkan.', 'Success'));
    }

    public function update(Request $request, Lowongan $lowongan)
    {
        $validated = $request->validate([
            'posisi' => 'required|string|max:255',
            'perusahaan' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'gaji' => 'nullable|string',
            'deskripsi' => 'required|string',
            'status' => 'required|in:Aktif,Ditutup',
            'deadline' => 'required|date',
        ]);

        // Handle currency format for 'gaji'
        if (isset($validated['gaji'])) {
            $validated['gaji'] = str_replace('.', '', $validated['gaji']);
        }

        $lowongan->update($validated);

        return redirect()->back()->with(AlertHelper::success('Data lowongan berhasil diperbarui.', 'Success'));
    }

    public function show(Lowongan $lowongan)
    {
        return view('Dashboard.Lowongan-Detail', compact('lowongan'));
    }

    public function destroy(Lowongan $lowongan)
    {
        $lowongan->delete();
        return redirect()->back()->with(AlertHelper::success('Data lowongan berhasil dihapus.', 'Success'));
    }
}
