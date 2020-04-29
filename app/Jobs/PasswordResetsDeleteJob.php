<?php

namespace App\Jobs;

use Illuminate\Support\Facades\DB;

class PasswordResetsDeleteJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Delete password resets older than 24 hours
     *
     * @return void
     */
    public function handle()
    {
        $yesterday = date('Y-m-d h:m:s', strtotime('-1 day'));
        DB::delete('delete from password_resets where created_at < ?', array($yesterday));
    }
}
