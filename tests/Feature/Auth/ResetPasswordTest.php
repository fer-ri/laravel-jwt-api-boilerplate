<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function reset_password()
    {
        Event::fake();

        $user = factory(User::class)->create();

        $token = Password::createToken($user);

        $response = $this->json('POST', 'reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        Event::assertDispatched(PasswordReset::class);
    }

    /** @test */
    public function reset_password_invalid_token()
    {
        $user = factory(User::class)->create();

        $token = str_random(20);

        $response = $this->json('POST', 'reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response
            ->assertStatus(403)
            ->assertJsonStructure([
                'status_code',
                'message',
            ]);
    }

    /** @test */
    public function reset_password_validation()
    {
        $response = $this->json('POST', 'reset-password', []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status_code',
                'message',
                'errors',
            ]);
    }
}
