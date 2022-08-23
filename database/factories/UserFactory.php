<?php

namespace Database\Factories;

use App\User;

class UserFactory extends \Illuminate\Database\Eloquent\Factories\Factory
{

    protected $model = User::class;

    /**
     * @inheritDoc
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->unique()->safeEmail,
            'password' => app()->make('hash')->make('testPassword1')
        ];
    }

    public function isAdmin()
    {
        return $this->state(function (array $attributes) {
            return [
                'admin' => true
            ];
        });
    }
}