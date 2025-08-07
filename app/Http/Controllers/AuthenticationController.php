<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthenticationController extends Controller
{
    public function store(Request $request) {
         $request->validate([
            'nameOrEmail' => 'required|string',
            'password' => 'required|string',
            'remember' => 'nullable|in:on'
        ]);

        $login = $request->input('nameOrEmail');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $credentials = filter_var($login, FILTER_VALIDATE_EMAIL)
        ? ['email' => $login, 'password' => $password]
        : ['name' => $login, 'password' => $password];

        if (Auth::attempt($credentials, $remember)) {
            AlertHelper::success('Selamat datang ' . Auth::user()->name . '!', 'Login Berhasil');
            return redirect()->intended('dashboard');
         }

        AlertHelper::error('Nama/Email atau password salah!', 'Login Gagal');
        return back()->withInput($request->except('password'));
    }

    public function apiLogin(Request $request) {
    try {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $login = $request->input('email');
        $password = $request->input('password');

        $user = \App\Models\User::where(function ($query) use ($login) {
            $query->where('email', $login)->orWhere('name', $login);
        })->with(['siswaDetail'])->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Nama/Email atau password salah!'
            ], 401);
        }

        if ($user->role !== 'siswa') {
            return response()->json([
                'success' => false,
                'message' => 'Name/Email atau Password salah!'
            ], 403);
        }

        $token = $user->createToken('flutter-app')->plainTextToken;

        $fullName = $user->name; 
        
        
        if ($user->role === 'siswa' && $user->siswaDetail) {
            $fullName = $user->siswaDetail->nama_lengkap ?? $user->name;
        }
        $batch = $user->siswaDetail->batches->nama;
        $kelas = $user->siswaDetail->kelas->nama;
        Log::info('Login Response Data:', [
            'user_name' => $user->name,
            'full_name' => $fullName,
            'siswa_detail' => $user->siswaDetail ? $user->siswaDetail->nama_lengkap : null,
            'role' => $user->role,
            'batch' => $batch,
            'kelas' =>  $kelas
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'fullname' => $fullName, 
                'email' => $user->email,
                'role' => $user->role ?? 'siswa',
                'batch' => $batch,
                'kelas'=> $kelas
            ]
        ]);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Data tidak valid',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        Log::error('Login Error: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan server',
            'error' => $e->getMessage()
        ], 500);
    }
}



    public function apiLogout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout'
            ], 500);
        }
    }

    // Method untuk get user info API
    public function apiUser(Request $request)
    {
        try {
            $user = $request->user();
            
            return response()->json([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role ?? 'siswa',
                    // Tambah field lain sesuai kebutuhan
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user'
            ], 500);
        }
    }
}
