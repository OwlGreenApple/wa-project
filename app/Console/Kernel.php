<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\SendMessage::class,
        Commands\CheckCounter::class,
        Commands\GetKey::class,
        Commands\ResetMessageCounter::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

         $schedule->command('check:membership')->dailyAt('07:00');
         $schedule->command('reset:m')->dailyAt('01:00');
         $schedule->command('get:key')->everyMinute();
         $schedule->command('send:message')->everyMinute();
         $schedule->command('check:counter')->everyMinute();
         // $schedule->command('check:wa')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
