<?php

namespace Bengr\Auth;

use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use App\Modules\Guard\Traits\HasAuthTokens;

class Guard
{
    /**
     * The authentication factory implementation.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * The number of minutes tokens should be allowed to remain valid.
     *
     * @var int
     */
    protected $expiration;

    /**
     * The provider name.
     *
     * @var string
     */
    protected $provider;

    public function __construct(AuthFactory $auth, $expiration = null, $provider = null)
    {
        $this->auth = $auth;
        $this->expiration = $expiration;
        $this->provider = $provider;
    }

    public function __invoke(Request $request)
    {
        if ($token = $this->getTokenFromRequest($request)) {
            $model = config('auth.tokens')[$this->provider]['model'];
            $accessToken = $model::findToken($token);

            if (!$this->isValidAccessToken($accessToken) || !$this->supportsTokens($accessToken->tokenable)) {
                return;
            }

            $tokenable = $accessToken->tokenable->withAccessToken(
                $accessToken
            );

            if (
                method_exists($accessToken->getConnection(), 'hasModifiedRecords') &&
                method_exists($accessToken->getConnection(), 'setRecordModificationState')
            ) {
                tap($accessToken->getConnection()->hasModifiedRecords(), function ($hasModifiedRecords) use ($accessToken) {
                    $accessToken->forceFill(['last_used_at' => now()])->save();

                    $accessToken->getConnection()->setRecordModificationState($hasModifiedRecords);
                });
            } else {
                $accessToken->forceFill(['last_used_at' => now()])->save();
            }

            return $tokenable;
        }
    }

    public function getTokenFromRequest($request)
    {
        return $request->bearerToken();
    }

    public function supportsTokens($tokenable = null)
    {
        return $tokenable && in_array(HasAuthTokens::class, class_uses_recursive(get_class($tokenable)));
    }

    public function isValidAccessToken($accessToken)
    {
        if (!$accessToken) {
            return false;
        }



        $isValid =
            (!$this->expiration || $accessToken->created_at->gt(now()->subMinutes($this->expiration)))
            && (!$accessToken->expires_at || !$accessToken->expires_at->isPast())
            && $this->hasValidProvider($accessToken->tokenable);

        return $isValid;
    }

    protected function hasValidProvider($tokenable)
    {

        if (is_null($this->provider)) {
            return true;
        }

        $model = config("auth.providers.{$this->provider}.model");


        return $tokenable instanceof $model;
    }
}
