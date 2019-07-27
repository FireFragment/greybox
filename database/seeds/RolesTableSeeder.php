<?php

use Illuminate\Database\Seeder;
use App\Role;


class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultRoles = ['Debatér', 'Rozhodčí', 'Pedagogický dozor', 'Organizátor'];
        foreach ($defaultRoles as $name) {
            Role::create([
                'name' => $name
            ]);
        }
    }
}
