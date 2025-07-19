<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Helpers\AlertHelper;

class AuthHelper
{
    /**
     * Cek apakah user sudah login.
     *
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public static function checkAuthenticated()
    {
        if (!Auth::check()) {
            return Redirect::route('login')
                ->with(AlertHelper::error('Anda harus login untuk mengakses halaman ini.', 'Unauthorized'));
        }

        return null; // aman
    }

    /**
     * Cek role user (boleh single role atau multiple roles)
     *
     * @param string|array $roles
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public static function checkRole($roles)
    {
        $userRole = Auth::user()->role;

        $allowedRoles = is_array($roles) ? $roles : [$roles];

        if (!in_array($userRole, $allowedRoles)) {
            return Redirect::route('dashboard')
                ->with(AlertHelper::error('Anda tidak memiliki izin mengakses halaman ini.', 'Permission Denied'));
        }

        return null;
    }

    /**
     * Fungsi gabungan cek login dan role sekaligus.
     *
     * @param string|array $roles
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public static function checkAuthAndRole($roles)
    {
        if ($redirect = self::checkAuthenticated()) {
            return $redirect;
        }

        if ($redirect = self::checkRole($roles)) {
            return $redirect;
        }

        return null;
    }
}
