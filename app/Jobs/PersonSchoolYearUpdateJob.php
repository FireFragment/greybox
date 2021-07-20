<?php

namespace App\Jobs;

use App\Person;

class PersonSchoolYearUpdateJob extends Job
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
     * To be run annually
     * Null all school year 13 people because they quit school
     * Get list of all people with school_year value filled and add 1 to it
     *
     * @return void
     */
    public function handle()
    {
        Person::where('school_year', 13)->update(['school_year' => null]);
        Person::whereNotNull('school_year')->increment('school_year');
    }
}
