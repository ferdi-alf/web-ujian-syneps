<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// routes/console.php
Schedule::command('batch:update-status')->everyMinute();
Schedule::command('pembayaran:generate')->everyMinute();
