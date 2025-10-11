<?php

namespace Bhhaskin\Pulse\Database\Factories;

use Bhhaskin\Pulse\Models\PulseRawBatch;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PulseRawBatchFactory extends Factory
{
    protected $model = PulseRawBatch::class;

    public function definition(): array
    {
        $sentAt = $this->faker->dateTimeBetween('-1 hour', 'now');

        return [
            'events' => [
                [
                    'eventType' => 'auto',
                    'eventName' => 'page_view',
                    'payload' => [
                        'user_agent' => $this->faker->userAgent(),
                        'device' => [
                            'category' => $this->faker->randomElement(['desktop', 'mobile']),
                            'os' => $this->faker->randomElement(['macos', 'windows', 'ios', 'android', 'linux']),
                            'userAgent' => $this->faker->userAgent(),
                        ],
                    ],
                    'clientUuid' => Str::uuid()->toString(),
                    'sessionUuid' => Str::uuid()->toString(),
                    'url' => $this->faker->url(),
                    'sentAt' => $sentAt->format(DATE_ATOM),
                ],
            ],
            'batch_sent_at' => $sentAt,
        ];
    }
}
