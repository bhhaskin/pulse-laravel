<?php

namespace Bhhaskin\Pulse\Tests;

use Bhhaskin\Pulse\PulseServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * Register package service providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            PulseServiceProvider::class,
        ];
    }

    /**
     * Perform environment setup tailored for the package tests.
     *
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('app.key', 'base64:4xQGvS25l5Kc1X9nC1uhGQ==');
        $app['config']->set('app.url', 'http://localhost');
    }
}
