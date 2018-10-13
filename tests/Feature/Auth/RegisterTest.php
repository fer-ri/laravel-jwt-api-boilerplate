<?php

namespace Tests\Feature\Auth;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function register()
    {
        Notification::fake();

        $user = factory(User::class)->make();

        $response = $this->json('POST', '/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ]);

        $user = User::where('email', $user->email)->first();

        if ($user instanceof MustVerifyEmail) {
            Notification::assertSentTo($user, VerifyEmail::class);
        }

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    /** @test */
    public function register_validation()
    {
        $response = $this->json('POST', '/register', []);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status_code',
                'message',
                'errors',
            ]);
    }
}
