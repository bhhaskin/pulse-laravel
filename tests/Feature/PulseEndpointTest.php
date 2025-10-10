<?php

namespace Bhhaskin\Pulse\Tests\Feature;

use Bhhaskin\Pulse\PulseServiceProvider;
use Bhhaskin\Pulse\Tests\TestCase;
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
        config(['pulse.prefix' => 'custom-pulse']);

        (new PulseServiceProvider($this->app))->boot();

        $response = $this->post('/custom-pulse', []);

        $response->assertNoContent();
    }
}
