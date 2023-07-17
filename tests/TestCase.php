<?php

namespace Bengr\Auth\Tests;

use Bengr\Auth\AuthServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            AuthServiceProvider::class,
        ];
    }
}
