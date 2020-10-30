<?php

namespace SteadfastCollective\Fabric\Tests;

use Illuminate\Support\Facades\Config;
use LaravelZero\Framework\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('app.env', 'testing');
    }
}
