<?php

namespace Bhhaskin\Pulse\Database\Seeders;

use Bhhaskin\Pulse\Jobs\ProcessPulseBatch;
use Bhhaskin\Pulse\Models\PulseClient;
use Bhhaskin\Pulse\Models\PulseDevice;
use Bhhaskin\Pulse\Models\PulseRawBatch;
use Bhhaskin\Pulse\Models\PulseSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PulseDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();

        PulseClient::factory()->count(5)->create()->each(function (PulseClient $client) use ($faker): void {
            PulseSession::factory()->count(3)->create([
                'client_id' => $client->id,
            ])->each(function (PulseSession $session) use ($client, $faker): void {
                $device = PulseDevice::factory()->create([
                    'client_id' => $client->id,
                    'session_id' => $session->id,
                ]);

                $events = collect(range(1, 4))->map(function () use ($client, $session, $device, $faker) {
                    return [
                        'eventType' => 'auto',
                        'eventName' => Arr::random(['page_view', 'scroll', 'signup_completed']),
                        'payload' => [
                            'user_agent' => $device->user_agent,
                            'device' => $this->buildDevicePayload($device->toArray()),
                        ],
                        'clientUuid' => $client->uuid,
                        'sessionUuid' => $session->uuid,
                        'url' => $faker->url(),
                        'sentAt' => now()->toIso8601String(),
                    ];
                })->all();

                $batch = PulseRawBatch::create([
                    'events' => $events,
                    'batch_sent_at' => now(),
                ]);

                (new ProcessPulseBatch($batch->id))->handle();
            });
        });
    }

    protected function buildDevicePayload(array $attributes): array
    {
        return [
            'category' => $attributes['category'] ?? null,
            'os' => $attributes['os'] ?? null,
            'userAgent' => $attributes['user_agent'] ?? null,
            'isTouchCapable' => $attributes['is_touch_capable'] ?? null,
            'view_port' => $attributes['view_port'] ?? null,
            'touch' => $attributes['touch'] ?? null,
            'pointer' => $attributes['pointer'] ?? null,
            'hover' => $attributes['hover'] ?? null,
            'dpr' => $attributes['dpr'] ?? null,
            'width' => $attributes['width'] ?? null,
            'height' => $attributes['height'] ?? null,
            'orientation' => $attributes['orientation'] ?? null,
            'reduced_motion' => $attributes['reduced_motion'] ?? null,
            'browser' => [
                'name' => $attributes['browser_name'] ?? null,
                'version' => $attributes['browser_version'] ?? null,
            ],
        ];
    }
}
