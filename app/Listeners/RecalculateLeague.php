<?php

namespace App\Listeners;

use App\Events\DebateDeletedEvent;
use App\Models\Debate;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecalculateLeague
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  DebateDeletedEvent  $event
     * @return void
     */
    public function handle(DebateDeletedEvent $event)
    {
        $deletedDebate = $event->debate;
        // TODO: Prepare the actual recalculate league functionality
        // Debate::where('date', '>=', $deletedDebate->date)->update(['affirmativeWinner' => false]);
    }
}