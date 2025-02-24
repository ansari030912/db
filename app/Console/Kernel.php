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
        \App\Console\Commands\GetVendor::class,

        \App\Console\Commands\UpdateCertificationsCommand::class,

        \App\Console\Commands\FetchExamData::class,

        \App\Console\Commands\FetchExamCerts::class,

        \App\Console\Commands\UpdateExams::class,

        \App\Console\Commands\FetchHotExams::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('vendors:update')->hourly();

        $schedule->command('certifications:update')->hourly();

        $schedule->command('exams:update')->hourly();

        $schedule->command('certs:update')->hourly();

        $schedule->command('product:update')->daily();

        $schedule->command('fetch:hot-exams')->daily();

        $schedule->command('banner:fetch')->everyFifteenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
