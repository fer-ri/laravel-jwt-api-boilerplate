<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', '/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type',
                'expires_in',
            ]);
    }

    /* @test */
    public function login_validation()
    {
        $response = $this->json('POST', '/login', []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status_code',
                'message',
                'errors',
            ]);
    }

    /** @test */
    public function login_without_activation()
    {
        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        if (! $user instanceof MustVerifyEmail) {
            return $this->assertTrue(true);
        }

        $response = $this->json('POST', '/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure([
                'status_code',
                'message',
            ]);
    }

    /** @test */
    public function logout()
    {
        $response = $this->asUser()
            ->json('POST', 'logout');

        $response->assertStatus(204);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->json('GET', 'me');

        $response->assertStatus(401);
    }
}
