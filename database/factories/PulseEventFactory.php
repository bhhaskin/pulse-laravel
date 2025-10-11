<?php

namespace Bhhaskin\Pulse\Database\Factories;

use Bhhaskin\Pulse\Models\PulseClient;
use Bhhaskin\Pulse\Models\PulseDevice;
use Bhhaskin\Pulse\Models\PulseEvent;
use Bhhaskin\Pulse\Models\PulseRawBatch;
use Bhhaskin\Pulse\Models\PulseSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class PulseEventFactory extends Factory
{
    protected $model = PulseEvent::class;

    public function definition(): array
    {
        return [
            'batch_id' => PulseRawBatch::factory(),
            'client_id' => PulseClient::factory(),
            'session_id' => PulseSession::factory(),
            'device_id' => PulseDevice::factory(),
            'event_type' => $this->faker->randomElement(['auto', 'custom']),
            'event_name' => $this->faker->randomElement(['page_view', 'scroll', 'signup_completed']),
            'url' => $this->faker->url(),
            'payload' => ['foo' => 'bar'],
            'sent_at' => $this->faker->dateTimeBetween('-1 hour', 'now'),
        ];
    }
}
