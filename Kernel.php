<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Register the closure-based / class-based commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define the application's command schedule.
     *
     * These two jobs are the heartbeat of the Warehouse API integration:
     * one keeps local stock numbers fresh, the other makes sure every order
     * eventually reaches the 3rd-party fulfillment system, even if the first
     * push attempt (right after checkout) failed or the API was briefly down.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check warehouse inventory every 15 minutes — adjust frequency once you know
        // your provider's rate limits and how often their stock data actually changes.
        $schedule->command('warehouse:sync-inventory')
            ->everyFifteenMinutes()
            ->withoutOverlapping()
            ->onOneServer();

        // Retry pushing any orders that failed or were queued, every 5 minutes.
        $schedule->command('warehouse:sync-orders')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onOneServer();
    }
}
