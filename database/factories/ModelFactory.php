<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

use Faker\Generator;
use App\User;

$factory->define(User::class, function (Generator $faker) {
    return [
        'username' => $faker->unique()->safeEmail,
        'password' => app()->make('hash')->make('testPassword1')
    ];
});

$factory->state(User::class, 'admin', function (Generator $faker) use ($factory) {
    $user = $factory->raw(User::class);
    return array_merge($user, ['admin' => true]);
});
