<?php

namespace Bhhaskin\Pulse\Database\Factories;

use Bhhaskin\Pulse\Models\PulseClient;
use Bhhaskin\Pulse\Models\PulseSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PulseSessionFactory extends Factory
{
    protected $model = PulseSession::class;

    public function definition(): array
    {
        $firstSeen = $this->faker->dateTimeBetween('-2 weeks', 'now');
        $lastSeen = $this->faker->dateTimeBetween($firstSeen, 'now');

        return [
            'uuid' => Str::uuid()->toString(),
            'client_id' => PulseClient::factory(),
            'first_seen_at' => $firstSeen,
            'last_seen_at' => $lastSeen,
        ];
    }
}
