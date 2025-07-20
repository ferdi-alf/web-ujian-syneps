<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Kelas;
use App\Models\PengajarDetail;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        $users = User::with('pengajarDetail.kelas')->get();

        $admins = $users->where('role', 'admin');
        $pengajars = $users->where('role', 'pengajar');
        $kelas = Kelas::all();

        return view('Dashboard.Users', compact('admins', 'pengajars', 'kelas'));
    }

    public function store(Request $request) {
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'role' => 'required|in:admin,pengajar',
        'password' => 'required|min:6',
        'kelas_id' => 'nullable|exists:kelas,id'
    ]);

    $avatar = 'avatar-' . rand(1, 10) . '.png';

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'password' => bcrypt($request->password),
        'avatar' => $avatar, 
    ]);

    if ($request->role === 'pengajar') {
        PengajarDetail::create([
            'pengajar_id' => $user->id,
            'kelas_id' => $request->kelas_id,
        ]);
    }

    return redirect()->back()->with(AlertHelper::success('User berhasuk ditambahkan', 'Success'));
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,pengajar',
            'password' => 'nullable|min:6',
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $userData['password'] = bcrypt($request->password);
        }

        $user->update($userData);

        if ($request->role === 'pengajar') {
            $pengajarDetail = PengajarDetail::where('pengajar_id', $user->id)->first();
            
            if ($pengajarDetail) {
                $pengajarDetail->update([
                    'kelas_id' => $request->kelas_id,
                ]);
            } else {
                PengajarDetail::create([
                    'pengajar_id' => $user->id,
                    'kelas_id' => $request->kelas_id,
                ]);
            }
        } else {
            PengajarDetail::where('pengajar_id', $user->id)->delete();
        }

        return redirect()->back()->with(AlertHelper::success('User berhasil diperbarui', 'Success'));
    }   

    public function destroy($id) {
        try {
            $user = User::findOrFail($id);
            
            PengajarDetail::where('pengajar_id', $user->id)->delete();
            
            $user->delete();
            
            return redirect()->back()->with(AlertHelper::success('User berhasil dihapus', 'Success'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with(AlertHelper::error('Gagal menghapus user', 'Error'));
        }
    }


}
