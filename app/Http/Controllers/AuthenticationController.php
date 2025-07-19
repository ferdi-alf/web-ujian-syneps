<?php

namespace App\Http\Controllers;

use App\Helpers\AlertHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
