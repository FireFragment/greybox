<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \App\Events\DebateDeletedEvent::class => [
            \App\Listeners\RecalculateLeague::class,
        ],
        \App\Events\TeamsRegisteredEvent::class => [
            \App\Listeners\SendTeamRulesBreachWarning::class,
        ]
    ];
}
