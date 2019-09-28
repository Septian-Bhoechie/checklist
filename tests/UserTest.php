<?php

use Bhoechie\Checklist\Models\User;
use Laravel\Lumen\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * A basic test.
     *
     * @return void
     */
    public function testResponse()
    {
        $this->get('/');
        $this->assertEquals(
            $this->app->version(), $this->response->getContent()
        );

        // $response = $this->get('/');
        $this->assertEquals(
            200, $this->response->getStatusCode()
        );
    }

    /**
     * A user auth test.
     *
     * @return void
     */
    public function testSuccessAuthenticate()
    {
        $password = str_random(8);
        $user = User::create([
            'name' => 'Septian',
            'email' => 'septian.bhoechie@gmail.com',
            'password' => app('hash')->make($password),
        ]);

        //test wrong password
        $this->post('api/user/login', ['email' => $user->email, 'password' => 1]);
        $user->refresh();
        $this->seeJsonEquals([
            'status' => 'fail',
        ]);
        $this->assertEquals(
            401, $this->response->getStatusCode()
        );

        //test correct password
        $this->post('api/user/login', ['email' => $user->email, 'password' => $password]);
        $user->refresh();
        $this->seeJsonEquals([
            'data' => [
                'attributes' => [
                    'status' => 'success',
                    'token' => $user->token,
                ],
            ],
        ]);

        //test no auth header
        $this->get("api/user/show/$user->id");
        $this->assertEquals(
            401, $this->response->getStatusCode()
        );

        //test auth header
        $this->get("api/user/show/$user->id", ['HTTP_Authorization' => $user->token]);
        // dd($this->response->getContent());
        $user->addHidden(['id']);
        $this->seeJsonEquals(
            [
                'data' => [
                    'id' => $user->id,
                    'attributes' => $user->toArray(),
                ],
            ]
        );
    }
}
