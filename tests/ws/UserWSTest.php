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
            'password' => 'testPassword1'
        );

        $this->post($this->apiPrefix.'/login', $formValues);

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'id_token',
            'id',
            'username',
            'role',
            'api_token',
            'role',
            'created_at',
            'updated_at'
        ]);
        $this->seeHeader('Authorization');
        $this->seeHeader('Access-Control-Expose-Headers', 'Authorization');
    }

    public function testLoginWithWrongPassword()
    {
        $user = factory(User::class)->create();
        $formValues = array(
            'username' => $user->username,
            'password' => 'WrongPassword'
        );

        $this->post($this->apiPrefix.'/login', $formValues);

        $this->seeStatusCode(401);
        $this->seeJsonEquals(['message' => 'invalidCredentials']);
    }

    public function testLoginWithWrongUsername()
    {
        $user = factory(User::class)->create();
        $formValues = array(
            'username' => 'wrong@User.name',
            'password' => 'testPassword1'
        );

        $this->post($this->apiPrefix.'/login', $formValues);

        $this->seeStatusCode(401);
        $this->seeJsonEquals(['message' => 'invalidCredentials']);
    }

    public function testLogout()
    {
        $user = factory(User::class)->create();
        $loginFormValues = array(
            'username' => $user->username,
            'password' => 'testPassword1'
        );
        $loggedUser = $this->call('POST', $this->apiPrefix.'/login', $loginFormValues);

        $logoutFormValues = array(
            'api_token' => $loggedUser['api_token']
        );
        $this->post($this->apiPrefix.'/logout', $logoutFormValues);

        $this->seeStatusCode(200);
        $this->seeJsonEquals(['message' => 'logoutSuccessful']);
    }

    public function testLogoutWithWrongApiToken()
    {
        $logoutFormValues = array(
            'api_token' => time()
        );
        $this->post($this->apiPrefix.'/logout', $logoutFormValues);

        $this->seeStatusCode(401);
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
