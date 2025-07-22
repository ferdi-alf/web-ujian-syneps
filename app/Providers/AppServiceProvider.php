<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Schema::defaultStringLength(191);

        //     // Paksa semua URL menjadi HTTPS jika diakses via ngrok atau env tertentu
        //     if (app()->environment('local') && str_contains(config('app.url'), 'ngrok')) {
        //         URL::forceScheme('https');
        //     }
    }
}
