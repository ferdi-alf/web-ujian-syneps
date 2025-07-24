<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        return view('Dashboard.Profile');
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Validasi dasar
        $rules = [
            'name' => 'required|string|max:255',
        ];

        // Tambah validation untuk nama_lengkap jika user adalah siswa atau pengajar
        if (in_array($user->role, ['siswa', 'pengajar'])) {
            $rules['nama_lengkap'] = 'required|string|max:255';
        }

        // Tambah validation untuk password jika diisi
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        try {
            // Update data user
            $user->update([
                'name' => $validated['name'],
            ]);

            // Update password jika diisi
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($validated['password'])
                ]);
            }

            // Update nama_lengkap di tabel detail sesuai role
            if (isset($validated['nama_lengkap'])) {
                if ($user->role === 'siswa') {
                    // Update atau create siswa detail
                    $user->siswaDetail()->updateOrCreate(
                        ['siswa_id' => $user->id],
                        ['nama_lengkap' => $validated['nama_lengkap']]
                    );
                } elseif ($user->role === 'pengajar') {
                    // Update atau create pengajar detail
                    $user->pengajarDetail()->updateOrCreate(
                        ['pengajar_id' => $user->id],
                        ['nama_lengkap' => $validated['nama_lengkap']]
                    );
                }
            }

            return redirect()->back()->with('success', 'Profile berhasil diperbarui!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profile.'])
                ->withInput();
        }
    }
}