<?php

namespace Bengr\Auth;

class NewToken
{
    public string $name = "";

    public string $access_token = "";

    public string $refresh_token = "";

    public function __construct($token, $plainAccessToken, $plainRefreshToken)
    {
        $this->name = $token->name;
        $this->access_token = $plainAccessToken;
        $this->refresh_token = $plainRefreshToken;
    }
}
