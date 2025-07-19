<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('login');

    Route::post('/', [AuthenticationController::class, 'store'])->name('store.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [DashboardController::class, 'index'])->name('users');
    Route::get('/manajemen-ujian', [DashboardController::class, 'index'])->name('manajemen.ujian');
    Route::get('/tambah-ujian', [DashboardController::class, 'index'])->name('tambah.ujian');
    Route::get('/leaderboard-siswa', [DashboardController::class, 'index'])->name('leaderboard.siswa');
    Route::get('/siswa', [DashboardController::class, 'index'])->name('siswa');

    Route::controller(KelasController::class)->prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', 'index')->name('index');        
        Route::post('/', 'store')->name('store');        
        Route::put('/{id}', 'update')->name('update');  
        Route::delete('/{id}', 'destroy')->name('destroy'); 
    });

});
