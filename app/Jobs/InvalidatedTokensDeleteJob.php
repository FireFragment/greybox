<?php

namespace App\Jobs;

use App\Models\Token;
use Illuminate\Support\Facades\DB;

class InvalidatedTokensDeleteJob extends Job
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
        Token::where('valid_until', '<', new \DateTime())->delete();
    }
}
