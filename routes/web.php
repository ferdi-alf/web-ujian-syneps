<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\ManajemenUjianController;
use App\Http\Controllers\TambahUjianController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('login');

    Route::post('/', [AuthenticationController::class, 'store'])->name('store.login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

      Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
        Route::get('/', 'index')->name('index');        
        Route::post('/', 'store')->name('store');        
        Route::put('/{id}', 'update')->name('update');  
        Route::delete('/{id}', 'destroy')->name('destroy'); 
    });

    Route::controller(ManajemenUjianController::class)->prefix('manajemen-ujian')->name('manajemen-ujian.')->group(function () {
        Route::get('/', 'index')->name('index');        
        Route::post('/', 'store')->name('store');        
        Route::put('/{id}', 'update')->name('update');  
        Route::delete('/{id}', 'destroy')->name('destroy'); 
    });

   
    Route::controller(TambahUjianController::class)->prefix('tambah-ujian')->name('tambah-ujian.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::get('/progress', 'getProgress')->name('progress');
        Route::post('/clear-progress', 'clearProgress')->name('clear-progress'); // Route baru
    });

    Route::get('/leaderboard-siswa', [DashboardController::class, 'index'])->name('leaderboard.siswa');
    Route::get('/siswa', [DashboardController::class, 'index'])->name('siswa');

    Route::controller(KelasController::class)->prefix('kelas')->name('kelas.')->group(function () {
        Route::get('/', 'index')->name('index');        
        Route::post('/', 'store')->name('store');        
        Route::put('/{id}', 'update')->name('update');  
        Route::delete('/{id}', 'destroy')->name('destroy'); 
    });

});
