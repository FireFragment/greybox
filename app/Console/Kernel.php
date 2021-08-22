<?php

namespace App\Console;

use App\Jobs\FakturoidClientUpdateJob;
use App\Jobs\FakturoidInvoiceUpdateJob;
use App\Jobs\PasswordResetsDeleteJob;
use App\Jobs\PersonSchoolYearUpdateJob;
use App\Services\FakturoidClientService;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;
use App\Models\Cron;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new FakturoidClientUpdateJob(new FakturoidClientService()))
            ->description('Update clients data from Fakturoid subjects.')
            ->everyMinute() // hack for Heroku
            ->when(function() {
                return Cron::shouldRun('FakturoidClientUpdateJob', 24*30);
            });

        $schedule->job(new FakturoidInvoiceUpdateJob(new FakturoidClientService()))
            ->description('Update invoices data from Fakturoid.')
            ->everyMinute() // hack for Heroku
            ->when(function() {
                return Cron::shouldRun('FakturoidInvoiceUpdateJob', 24);
            });

        $schedule->job(new PasswordResetsDeleteJob())
            ->description('Delete password resets older than 24 hours.')
            ->everyMinute() // hack for Heroku
            ->when(function() {
                return Cron::shouldRun('PasswordResetsDeleteJob', 24);
            });

        $schedule->job(new PersonSchoolYearUpdateJob())
            ->description('Increment school years of all pupils.')
            ->everyMinute() // hack for Heroku
            ->when(function() {
                return Cron::shouldRun('PersonSchoolYearUpdateJob', 8760);
            });
    }
}
