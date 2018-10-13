<?php

namespace Tests;

use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /** @var \App\User current user from asUser() */
    protected $user;

    /** @var string token from asUser() */
    protected $token;

    public function asUser()
    {
        $this->user = factory(User::class)->create();

        $this->token = JWTAuth::fromUser($this->user);

        $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ]);

        return $this;
    }
}
