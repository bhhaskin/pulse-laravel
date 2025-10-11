<?php

namespace Bhhaskin\Pulse\Database\Factories;

use Bhhaskin\Pulse\Models\PulseClient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PulseClientFactory extends Factory
{
    protected $model = PulseClient::class;

    public function definition(): array
    {
        $firstSeen = $this->faker->dateTimeBetween('-1 month', 'now');
        $lastSeen = $this->faker->dateTimeBetween($firstSeen, 'now');

        return [
            'uuid' => Str::uuid()->toString(),
            'first_seen_at' => $firstSeen,
            'last_seen_at' => $lastSeen,
        ];
    }
}
