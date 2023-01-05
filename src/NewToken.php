<?php

namespace Bengr\Auth;

class NewToken
{
    protected string $name = "";

    protected string $access_token = "";

    protected string $refresh_token = "";

    public function __construct($token, $plainAccessToken, $plainRefreshToken)
    {
        $this->name = $token->name;
        $this->access_token = $plainAccessToken;
        $this->refresh_token = $plainRefreshToken;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAccessToken(): string
    {
        return $this->access_token;
    }

    public function getRefreshToken(): string
    {
        return $this->refresh_token;
    }
}
