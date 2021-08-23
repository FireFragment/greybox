<?php

namespace App\Providers;

use App\Models\Token;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        Gate::policy('App\User', 'App\Policies\UserPolicy');
        Gate::policy('App\Registration', 'App\Policies\RegistrationPolicy');
        Gate::policy('App\Event', 'App\Policies\EventPolicy');
        Gate::policy('App\Client', 'App\Policies\ClientPolicy');

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $token = Token::where('api_token', $request->header('Authorization'))->first();
                if (null !== $token && new \DateTime() < new \DateTime($token->valid_until)) {
                    return $token->user()->first();
                }
            }
        });
    }
}
