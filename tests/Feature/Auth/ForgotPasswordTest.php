<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function forgot_password()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $response = $this->json('POST', 'forgot-password', ['email' => $user->email]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** @test */
    public function forgot_password_not_existing_user()
    {
        Notification::fake();

        $response = $this->json('POST', 'forgot-password', ['email' => '404@mail.com']);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        Notification::assertNotSentTo([], ResetPassword::class);
    }

    /** @test */
    public function forgot_password_validation()
    {
        $response = $this->json('POST', 'forgot-password', []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status_code',
                'message',
                'errors',
            ]);
    }
}
