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
        \App\Console\Commands\FetchBannerCommand::class,

        \App\Console\Commands\FetchCoupons::class,

        \App\Console\Commands\FetchExamCertificates::class,

        \App\Console\Commands\FetchExamData::class,

        \App\Console\Commands\FetchHotExams::class,

        \App\Console\Commands\FetchRecentlyUpdatedExams::class,

        \App\Console\Commands\FetchSingleCertificates::class,

        \App\Console\Commands\FetchSingleTrainingCourse::class,

        \App\Console\Commands\GetCertificates::class,

        \App\Console\Commands\GetSingleExam::class,

        \App\Console\Commands\GetTrainingCourses::class,

        \App\Console\Commands\GetVendor::class,

        \App\Console\Commands\UpdateCoupons::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('banner:fetch')->hourly();

        $schedule->command('fetch:coupons')->daily();

        $schedule->command('fetch:exam-certificates')->hourly();

        $schedule->command('exams:update')->hourly();

        $schedule->command('fetch:hot-exams')->hourly();

        $schedule->command('fetch:recently-updated')->hourly();

        $schedule->command('fetch:single-cert')->hourly();

        $schedule->command('certifications:update')->hourly();

        $schedule->command('product:update')->daily();

        $schedule->command('fetch:training-courses')->daily();

        $schedule->command('vendors:update')->daily();

        $schedule->command('coupons:update')->everyFifteenMinutes();
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
