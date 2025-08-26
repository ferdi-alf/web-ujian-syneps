<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\AdminResetController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\ForumAlumniController;
use App\Http\Controllers\LowonganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ManajemenUjianController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\TambahUjianController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\RegisterController;
use App\Mail\MyTestEmail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Mail;

//fatih - LANDING PAGE INTEGRATION ACTIVATED
Route::get('/', [LandingController::class, 'index'])->name('index');
Route::get('/kelas/{id}', [LandingController::class, 'kelasDetail'])->name('kelas.detail');
Route::post('/daftar', [LandingController::class, 'daftar'])->name('daftar.store');

Route::get('/testroute', function () {
    $name = "Funny Coder";
    Mail::to('test.08@gmail.com')->send(new MyTestEmail($name));
});

Route::middleware(['web', 'guest'])->group(function () {
    Route::controller(RegisterController::class)->prefix('register')->name('register.')->group(function () {
        Route::get('/', function () {
            return view('auth.register');
        })->name('index');
    
        Route::post('/', [RegisterController::class, 'regsiter'])->name('store');
    });

    
        Route::controller(RegisterController::class)->group(function () {
            Route::get('/register/{token}', 'showRegistrationForm')->name('registration.form');
            Route::post('/register/{token}', 'processRegistration')->name('registration.process');
            
            Route::get('/verify/{user}', 'showVerificationForm')->name('verification.show');
            Route::post('/verify/{user}', 'processVerification')->name('verification.process');
            Route::post('/verify/{user}/resend', 'resendVerificationCode')->name('verification.resend');
        });


    // jangan di kucak login
    Route::controller(AuthenticationController::class)->prefix('login')->name('login.')->group(function () {
        Route::get('/', function () {
            return view('auth.login');
        })->name('index');
    
        Route::post('/', [AuthenticationController::class, 'store'])->name('store');
    });
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



    Route::controller(ForumAlumniController::class)->prefix('forum-alumni')->name('forum-alumni.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::post('/{id}', 'balasan')->name('balasan');
        Route::delete('/{id}', 'delete')->name('delete');
    });

    Route::controller(LowonganController::class)->prefix('lowongan')->name('lowongan.')->group(function () {
        Route::get('/', 'index')->name('index');
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
            Route::get('/approval', 'approval')->name('approval');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    });

    Route::middleware(['deny.roles:siswa,pengajar'])->group(function () {
        Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        Route::controller(PembayaranController::class)->prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/history', 'history')->name('history');
            Route::put('/{id}', 'store')->name('store');
        });

        Route::controller(ApprovalController::class)->prefix('approval')->name('approval.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::patch('/{id}', 'update')->name('update');
            Route::post('/{id}/resend', 'resend')->name('resend');
            Route::delete('/{id}', 'delete')->name('delete'); 
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