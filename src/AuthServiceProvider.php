<?php

namespace Bengr\Auth;

use Bengr\Auth\Facades\Auth;
use Illuminate\Auth\RequestGuard;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AuthServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('auth')->hasMigration('create_auth_tokens_table');
    }

    public function packageBooted()
    {
    }

    /**
     * Configure the authentication guard.
     *
     * @return void
     */
    protected function configureGuard()
    {
        Auth::resolved(function ($auth) {
            $auth->extend('token', function ($app, $name, array $config) use ($auth) {
                return tap($this->createGuard($auth, $config), function ($guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Register the guard.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @param  array  $config
     * @return RequestGuard
     */
    protected function createGuard($auth, $config)
    {
        return new RequestGuard(
            new Guard($auth, config('auth.tokens')[$config['provider']]['expiration'], $config['provider']),
            request(),
            $auth->createUserProvider($config['provider'] ?? null)
        );
    }
}
