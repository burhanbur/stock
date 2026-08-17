<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keeps stock_prices fresh without a manual "Sync Data" click. Runs after
// IDX market close (16:00 WIB); Yahoo Finance's unofficial endpoint is
// best-effort, so a missed run just means the next day's sync catches up.
Schedule::command('stocks:sync-prices')
    ->dailyAt('17:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
