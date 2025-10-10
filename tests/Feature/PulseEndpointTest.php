<?php

namespace Bhhaskin\PulseLaravel\Tests\Feature;

use Bhhaskin\PulseLaravel\PulseServiceProvider;
use Bhhaskin\PulseLaravel\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PulseEndpointTest extends TestCase
{
    #[Test]
    public function it_acknowledges_beacon_requests_without_content(): void
    {
        $response = $this->post('/pulse', []);

        $response->assertNoContent();
    }

    #[Test]
    public function it_allows_custom_prefix_configuration(): void
    {
        config(['pulse-laravel.prefix' => 'custom-pulse']);

        (new PulseServiceProvider($this->app))->boot();

        $response = $this->post('/custom-pulse', []);

        $response->assertNoContent();
    }
}
