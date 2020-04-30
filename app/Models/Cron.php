<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{
    protected $primaryKey = 'job';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job', 'next_run', 'last_run'
    ];

    /**
     * Checks with DB whether the scheduled job should already run
     *
     * @param string $job
     * @param int $nextInHours
     * @return bool
     */
    public static function shouldRun(string $job, int $nextInHours): bool
    {
        $cron = Cron::find($job);

        if (empty($cron)) return false;
        if (self::isPassed($cron->next_run))
        {
            self::updateCron($cron, $nextInHours);
            return true;
        }

        return false;
    }

    /**
     * Checks whether the scheduled time is in past.
     * @param string $scheduledTime
     * @return bool
     */
    private static function isPassed(string $scheduledTime): bool
    {
        if (time() > strtotime($scheduledTime)) return true;
        return false;
    }

    /**
     * Updates cron last run with current time and next run with the difference in hours
     * @param Cron $cron
     * @param int $nextInHours
     */
    private static function updateCron(Cron $cron, int $nextInHours): void
    {
        $nextRunTimestamp = time() + $nextInHours * 60 * 60;
        $nextRunDateTime = date('Y-m-d H:i:s', $nextRunTimestamp);

        $cron->next_run = $nextRunDateTime;
        $cron->last_run = date('Y-m-d H:i:s');
        $cron->save();
    }
}
