<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Hash;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    public function testLogin()
    {
        $user = factory(\App\User::class)->create(['password' => Hash::make('secret')]);

        $this->json('POST', route('auth.login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $this->assertResponseStatus(200);
        $this->seeJsonStructure(['token']);
    }

    public function testNoAccess()
    {
        $this->json('GET', route('user'));

        $this->assertResponseStatus(401);
    }

    public function testAccess()
    {
        $user = factory(\App\User::class)->create(['password' => Hash::make('secret')]);

        $this->json('POST', route('auth.login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $token = json_decode($this->response->getContent(), true)['token'];
        $this->json('GET', route('user'), [], ['HTTP_Authorization' => 'Bearer '. $token]);

        $this->assertResponseStatus(200);
    }
}
