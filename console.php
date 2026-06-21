<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Warehouse API Sync Schedule
|--------------------------------------------------------------------------
| Laravel 11+ projects scaffolded without app/Console/Kernel.php read the
| schedule from here directly. We've also kept app/Console/Kernel.php in
| this project (classic structure) for clarity — if your installation uses
| the new bootstrap/app.php approach, this file is the one that's active.
*/
Schedule::command('warehouse:sync-inventory')
    ->everyFifteenMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('warehouse:sync-orders')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();
