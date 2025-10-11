<?php

namespace Bhhaskin\Pulse\Tests\Feature;

use Bhhaskin\Pulse\Jobs\ProcessPulseBatch;
use Bhhaskin\Pulse\PulseServiceProvider;
use Bhhaskin\Pulse\Tests\TestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;

class PulseEndpointTest extends TestCase
{
    #[Test]
    public function it_enqueues_batch_processing_job(): void
    {
        Queue::fake();

        $response = $this->postJson('/pulse', $this->samplePayload());

        $response->assertNoContent();

        $this->assertDatabaseCount('pulse_raw_batches', 1);
        $this->assertDatabaseCount('pulse_events', 0);

        Queue::assertPushed(ProcessPulseBatch::class, 1);
    }

    #[Test]
    public function it_allows_custom_prefix_configuration(): void
    {
        config(['pulse.prefix' => 'custom-pulse']);

        (new PulseServiceProvider($this->app))->boot();

        $response = $this->postJson('/custom-pulse', $this->samplePayload());

        $response->assertNoContent();

        $this->assertDatabaseCount('pulse_raw_batches', 1);
        $this->assertDatabaseCount('pulse_events', 3);
    }

    #[Test]
    public function it_normalizes_raw_events_when_processing_job_runs(): void
    {
        $payload = $this->samplePayload();

        $response = $this->postJson('/pulse', $payload);

        $response->assertNoContent();

        $this->assertDatabaseCount('pulse_raw_batches', 1);
        $this->assertDatabaseCount('pulse_events', 3);

        $devicePayload = Arr::get($payload, 'events.0.payload.device');
        $deviceHash = sha1(json_encode($devicePayload));

        $this->assertDatabaseHas('pulse_clients', ['uuid' => '0f91ffe5-6c3d-4a86-b7b6-2d8e6b2cd456']);
        $this->assertDatabaseHas('pulse_sessions', ['uuid' => '1df2a324-9a40-4c77-a3a0-1c7b8d9e0123']);
        $this->assertDatabaseHas('pulse_devices', ['hash' => $deviceHash]);
        $this->assertDatabaseHas('pulse_devices', [
            'hash' => $deviceHash,
            'category' => 'desktop',
            'os' => 'macos',
            'is_touch_capable' => 0,
            'view_port' => 'desktop',
            'browser_name' => 'chrome',
            'browser_version' => '123.0.0.0',
        ]);
        $this->assertDatabaseHas('pulse_events', ['event_name' => 'signup_completed']);
    }

    #[Test]
    public function it_skips_invalid_events(): void
    {
        $payload = [
            'events' => [
                [
                    'eventType' => 'auto',
                    // eventName intentionally omitted to trigger validation failure.
                    'clientUuid' => 'invalid-client',
                ],
            ],
        ];

        $response = $this->postJson('/pulse', $payload);

        $response->assertNoContent();

        $this->assertDatabaseCount('pulse_raw_batches', 1);
        $this->assertDatabaseCount('pulse_events', 0);
    }

    protected function samplePayload(): array
    {
        return [
            'events' => [
                [
                    'eventType' => 'auto',
                    'eventName' => 'page_view',
                    'payload' => [
                        'page_location' => 'https://example.com/pricing',
                        'page_path' => '/pricing',
                        'page_title' => 'Pricing | Example',
                        'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                        'device' => [
                            'category' => 'desktop',
                            'os' => 'macos',
                            'userAgent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
                            'isTouchCapable' => false,
                            'view_port' => 'desktop',
                            'touch' => false,
                            'pointer' => 'fine',
                            'hover' => 'hover',
                            'dpr' => 2,
                            'width' => 1440,
                            'height' => 900,
                            'orientation' => 'landscape',
                            'reduced_motion' => false,
                            'browser' => [
                                'name' => 'chrome',
                                'version' => '123.0.0.0',
                            ],
                        ],
                    ],
                    'sessionUuid' => '1df2a324-9a40-4c77-a3a0-1c7b8d9e0123',
                    'clientUuid' => '0f91ffe5-6c3d-4a86-b7b6-2d8e6b2cd456',
                    'url' => 'https://example.com/pricing',
                    'sentAt' => '2024-05-10T12:34:50.123Z',
                ],
                [
                    'eventType' => 'auto',
                    'eventName' => 'scroll',
                    'payload' => [
                        'scroll_y' => 412,
                    ],
                    'sessionUuid' => '1df2a324-9a40-4c77-a3a0-1c7b8d9e0123',
                    'clientUuid' => '0f91ffe5-6c3d-4a86-b7b6-2d8e6b2cd456',
                    'url' => 'https://example.com/pricing',
                    'sentAt' => '2024-05-10T12:35:05.987Z',
                ],
                [
                    'eventType' => 'custom',
                    'eventName' => 'signup_completed',
                    'payload' => [
                        'plan' => 'pro',
                        'value' => 49.99,
                    ],
                    'sessionUuid' => '1df2a324-9a40-4c77-a3a0-1c7b8d9e0123',
                    'clientUuid' => '0f91ffe5-6c3d-4a86-b7b6-2d8e6b2cd456',
                    'url' => 'https://example.com/pricing',
                    'sentAt' => '2024-05-10T12:35:12.456Z',
                ],
            ],
            'batchSentAt' => '2024-05-10T12:35:13.000Z',
        ];
    }
}
