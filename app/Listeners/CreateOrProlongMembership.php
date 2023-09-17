<?php

namespace App\Listeners;

use App\Membership,
    App\Events\MembershipInvoiced;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateOrProlongMembership implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param  MembershipInvoiced $event
     * @return void
     */
    public function handle(MembershipInvoiced $event)
    {
        $endDate = Membership::setEndDate(date('Y'), date('n'));

        if (null === $event->membership) {
            Membership::create([
                'person' => $event->person->id,
                'beginning' => date('Y-m-d'),
                'end' => $endDate
            ]);
            return;
        }
        $event->membership->update([
            'end' => $endDate
        ]);
    }
}