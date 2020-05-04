<?php

declare(strict_types=1);

use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;

class UserWSTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test Login User WS
     *
     * @return void
     */
    public function testLogin()
    {
        $user = factory(User::class)->create();
        $formValues = array(
            'username' => $user->username,
            'password' => 'testPassword'
        );

        $this->post($this->apiPrefix.'/login', $formValues);

        $this->seeStatusCode(200);
    }

    public function testCreateUser()
    {
        $formValues = array(
            'username' => 'testmail2@debatovani.cz',
            'password' => 'TestPassword1',
            'password_confirmation' => 'TestPassword1'
        );
        $this->post($this->apiPrefix.'/user', $formValues);
        $this->seeStatusCode(201);
    }
}
