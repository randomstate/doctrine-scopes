<?php

namespace RandomState\DoctrineScopes\Tests;

use LaravelDoctrine\ORM\DoctrineServiceProvider;

class TestCase extends \Tests\TestCase
{
    public function setUp()
    {
        parent::setUp();
        $config = require __DIR__.'/doctrine.php';
        config()->set('doctrine', $config);

        $this->app->register(DoctrineServiceProvider::class);
    }
}