<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AdminResetController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ManajemenUjianController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\TambahUjianController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\KelasController;
use Illuminate\Support\Facades\Auth;


Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('login');

    Route::post('/', [AuthenticationController::class, 'store'])->name('store.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/leaderboard-siswa', [DashboardController::class, 'index'])->name('leaderboard.siswa');

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('/', 'update')->name('update');
    });

    Route::controller(UjianController::class)->prefix('ujian')->name('ujian.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{slug}/mulai', 'mulai')->name('mulai');
        Route::post('/{slug}/store', 'store')->name('store');
        Route::post('/{slug}/save-progress', 'saveProgress')->name('save-progress');
        Route::get('/{slug}/selesai', 'selesai')->name('selesai');
    });

    Route::controller(NilaiController::class)->prefix('nilai')->name('nilai.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/download', 'download')->name('download');
    });

    Route::middleware(['deny.roles:siswa'])->group(function () {
        Route::get('/leaderboard', [LeaderboardController::class, 'leaderboard'])->name('leaderboard');
        Route::post('/admin/reset-data', [AdminResetController::class, 'resetData'])->name('admin.reset.data');

        Route::controller(TambahUjianController::class)->prefix('tambah-ujian')->name('tambah-ujian.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::get('/progress', 'getProgress')->name('progress');
            Route::post('/clear-progress', 'clearProgress')->name('clear-progress');
        });

        Route::controller(BatchController::class)->prefix('batch')->name('batch.')->group(function () {
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::controller(ManajemenUjianController::class)->prefix('manajemen-ujian')->name('manajemen-ujian.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::controller(PesertaController::class)->prefix('peserta')->name('peserta.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    });

    // Routes accessible by admin only (deny siswa and pengajar)
    Route::middleware(['deny.roles:siswa,pengajar'])->group(function () {
        Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::controller(KelasController::class)->prefix('kelas')->name('kelas.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    });

    Route::get('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});