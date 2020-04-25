<?php

namespace App\Jobs;

use App\Client;
use App\Services\FakturoidClientService as Fakturoid;

class FakturoidClientUpdateJob extends Job
{
    private $fakturoid;

    /**
     * Create a new job instance.
     *
     * @param Fakturoid $fakturoid
     * @return void
     */
    public function __construct(Fakturoid $fakturoid)
    {
        $this->fakturoid = $fakturoid;
    }

    /**
     * Get list of all Fakturoid subjects (= client in greybox)
     * and update the clients table on changes of name, street, or email
     *
     * @return void
     */
    public function handle()
    {
        $subjects = $this->fakturoid->getAllSubjects();

        foreach ($subjects as $subject) {
            $client = Client::where('fakturoid_id', $subject->id)->first();
            if (!empty($client)) {
                if ($client->isDifferentFromSubject($subject)) {
                    $client->updateFromFakturoid($subject);
                    $client->save();
                }
            }
        }
    }
}
