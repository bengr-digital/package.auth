<?php

namespace Bengr\Auth\Concerns;

use Bengr\Auth\Models\AuthToken;
use Bengr\Auth\NewToken;
use Illuminate\Support\Str;

trait HasAuthTokens
{
    protected $accessToken;

    public function tokens()
    {
        return $this->morphMany(AuthToken::class, 'tokenable');
    }

    public function createToken(string $name)
    {
        $token = $this->tokens()->create([
            'name' => $name,
            'access_token' => hash('sha256', $plainAccessToken = Str::random(40)),
            'refresh_token' => hash('sha256', $plainRefreshToken = Str::random(40)),
            'expires_at' => null
        ]);

        return new NewToken($token, $plainAccessToken, $plainRefreshToken);
    }

    public function withAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }
}
