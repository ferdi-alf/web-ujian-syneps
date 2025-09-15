<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use App\Models\Kelas;
use App\Models\PengajarDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller 
{
    public function index() 
    {
        $users = User::with('pengajarDetail.kelas')->get();
        
        $admins = $users->where('role', 'admin');
        $pengajars = $users->where('role', 'pengajar');
        $kelas = Kelas::all();
        
        return view('Dashboard.Users', compact('admins', 'pengajars', 'kelas'));
    }

    public function show($id)
    {
        try {
            $user = User::with('pengajarDetail.kelas')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'pengajar_detail' => $user->pengajarDetail ? [
                        'id' => $user->pengajarDetail->id,
                        'nama_lengkap' => $user->pengajarDetail->nama_lengkap,
                        'kelas' => $user->pengajarDetail->kelas->map(function($kelas) {
                            return [
                                'id' => $kelas->id,
                                'nama' => $kelas->nama
                            ];
                        })
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    
    public function store(Request $request) 
    {
       
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,pengajar',
            'password' => 'required|min:6',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id'
        ]);
        
        $avatar = 'avatar-' . rand(1, 10) . '.png';
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => bcrypt($request->password),
            'avatar' => $avatar,
        ]);
        
        if ($request->role === 'pengajar' && $request->has('kelas_id') && !empty($request->kelas_id)) {
            $pengajarDetail = PengajarDetail::create([
                'pengajar_id' => $user->id,
            ]);
            
            Log::info('PengajarDetail created:', ['id' => $pengajarDetail->id]);
            
            $pengajarDetail->kelas()->attach($request->kelas_id);
            
            Log::info('Kelas attached:', [
                'pengajar_detail_id' => $pengajarDetail->id,
                'kelas_ids' => $request->kelas_id,
                'count_after_attach' => $pengajarDetail->kelas()->count()
            ]);
        }
        
        return redirect()->back()->with(AlertHelper::success('User berhasil ditambahkan', 'Success'));
    }
    
    
    public function update(Request $request, $id) 
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,pengajar',
            'password' => 'nullable|min:6',
            'kelas_id' => 'nullable|array',
            'kelas_id.*' => 'exists:kelas,id'
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
            
            if (!$pengajarDetail) {
                $pengajarDetail = PengajarDetail::create([
                    'pengajar_id' => $user->id,
                ]);
            }
            
            if ($request->has('kelas_id')) {
                $pengajarDetail->kelas()->sync($request->kelas_id);
            } else {
                $pengajarDetail->kelas()->detach();
            }
        } else {
            $pengajarDetail = PengajarDetail::where('pengajar_id', $user->id)->first();
            if ($pengajarDetail) {
                $pengajarDetail->kelas()->detach();
                $pengajarDetail->delete();
            }
        }
        
        return redirect()->back()->with(AlertHelper::success('User berhasil diperbarui', 'Success'));
    }
    
    public function destroy($id) 
    {
        try {
            $user = User::findOrFail($id);
            
            $pengajarDetail = PengajarDetail::where('pengajar_id', $user->id)->first();
            if ($pengajarDetail) {
                $pengajarDetail->kelas()->detach();
                $pengajarDetail->delete();
            }
            
            $user->delete();
            
            return redirect()->back()->with(AlertHelper::success('User berhasil dihapus', 'Success'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with(AlertHelper::error('Gagal menghapus user', 'Error'));
        }
    }
}