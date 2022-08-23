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
        $user = User::factory()->create();
        $formValues = array(
            'username' => $user->username,
            'password' => 'testPassword1'
        );

        $this->post($this->apiPrefix.'/login', $formValues);

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'id',
            'username',
            'role',
            'apiToken',
            'created_at',
            'updated_at'
        ]);
        $this->seeHeader('Authorization');
        $this->seeHeader('Access-Control-Expose-Headers', 'Authorization');
    }

    public function testLoginWithWrongPassword()
    {
        $user = User::factory()->create();
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
        $user = User::factory()->create();
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
        $user = User::factory()->create();
        $loginFormValues = array(
            'username' => $user->username,
            'password' => 'testPassword1'
        );
        $loggedUser = $this->call('POST', $this->apiPrefix.'/login', $loginFormValues);

        $this->post($this->apiPrefix.'/logout', array(), ['Authorization' => $loggedUser['apiToken']]);

        $this->seeStatusCode(200);
        $this->seeJsonEquals(['message' => 'logoutSuccessful']);
    }

    public function testLogoutWithWrongApiToken()
    {
        $this->post($this->apiPrefix.'/logout', array(), ['Authorization' => time()]);

        $this->seeStatusCode(401);
    }

    public function testShowAllAdmin()
    {
        $user = User::factory()->create();
        $admin = User::factory()->isAdmin()->create();
        $loginFormValues = array(
            'username' => $admin->username,
            'password' => 'testPassword1'
        );
        $loggedAdmin = $this->call('POST', $this->apiPrefix.'/login', $loginFormValues);

        $this->get($this->apiPrefix.'/user', ['Authorization' => $loggedAdmin['apiToken']]);
        $response = json_decode($this->response->content(), true);

        $this->seeStatusCode(200);
        $this->assertGreaterThanOrEqual(2, count($response));
        $this->seeJsonStructure([
            'id',
            'username',
            'created_at',
            'updated_at'
        ], $response[0]);
        $this->seeJsonStructure([
            'id',
            'username',
            'created_at',
            'updated_at'
        ], $response[1]);
    }

    public function testShowAllNormalUser()
    {
        $normalUser = User::factory()->create();
        $loginFormValues = array(
            'username' => $normalUser->username,
            'password' => 'testPassword1'
        );
        $loggedNormalUser = $this->call('POST', $this->apiPrefix.'/login', $loginFormValues);

        $this->get($this->apiPrefix.'/user', ['Authorization' => $loggedNormalUser['apiToken']]);
        $response = json_decode($this->response->content(), true);

        $this->seeStatusCode(200);
        $this->assertEquals(1, count($response));
        $this->seeJsonStructure([
            'id',
            'username',
            'created_at',
            'updated_at'
        ], $response[0]);
    }

    public function testShowAllNotLogged()
    {
        $this->get($this->apiPrefix.'/user');
        $this->seeStatusCode(401);
    }

    // TODO: testShowOne, testCreate, test

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
