<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function resend()
    {
        Notification::fake();

        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        $response = $this->json('POST', 'email/resend', ['email' => $user->email]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    /** @test */
    public function resend_email_not_exists()
    {
        Notification::fake();

        $response = $this->json('POST', 'email/resend', ['email' => 'notexists@mail.com']);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        Notification::assertNotSentTo(
            [], VerifyEmail::class
        );
    }

    /** @test */
    public function resend_validation()
    {
        $response = $this->json('POST', 'email/resend', []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status_code',
                'message',
                'errors',
            ]);
    }

    /** @test */
    public function verify()
    {
        $user = factory(User::class)->create([
            'email_verified_at' => null,
        ]);

        $url = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), ['id' => $user->getKey()]
        );

        $response = $this->json('GET', $url);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        $this->assertDatabaseMissing('users', [
            'email' => $user->email,
            'email_verified_at' => null,
        ]);
    }

    /** @test */
    public function verify_invalid_signature()
    {
        $url = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), ['id' => 3]
        );

        $response = $this->json('GET', $url);

        $response
            ->assertStatus(403)
            ->assertJsonStructure([
                'status_code',
                'message',
            ]);
    }
}
