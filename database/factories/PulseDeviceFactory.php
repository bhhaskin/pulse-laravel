<?php

namespace Bhhaskin\Pulse\Database\Factories;

use Bhhaskin\Pulse\Models\PulseClient;
use Bhhaskin\Pulse\Models\PulseDevice;
use Bhhaskin\Pulse\Models\PulseSession;
use Illuminate\Database\Eloquent\Factories\Factory;

class PulseDeviceFactory extends Factory
{
    protected $model = PulseDevice::class;

    public function definition(): array
    {
        $userAgent = $this->faker->userAgent();
        $category = $this->faker->randomElement(['desktop', 'mobile', 'tablet']);
        $os = $this->faker->randomElement(['macos', 'windows', 'ios', 'android', 'linux']);
        $isTouchCapable = $this->faker->boolean();
        $viewPort = $this->faker->randomElement(['desktop', 'mobile']);
        $touch = $this->faker->boolean();
        $pointer = $this->faker->randomElement(['fine', 'coarse']);
        $hover = $this->faker->randomElement(['hover', 'none']);
        $dpr = $this->faker->randomFloat(1, 1, 3);
        $width = $this->faker->numberBetween(320, 1920);
        $height = $this->faker->numberBetween(568, 1080);
        $orientation = $this->faker->randomElement(['portrait', 'landscape']);
        $reducedMotion = $this->faker->boolean();
        $browserName = $this->faker->randomElement(['chrome', 'safari', 'firefox', 'edge']);
        $browserVersion = $this->faker->numerify('###.##.#');

        $devicePayload = [
            'category' => $category,
            'os' => $os,
            'userAgent' => $userAgent,
            'isTouchCapable' => $isTouchCapable,
            'view_port' => $viewPort,
            'touch' => $touch,
            'pointer' => $pointer,
            'hover' => $hover,
            'dpr' => $dpr,
            'width' => $width,
            'height' => $height,
            'orientation' => $orientation,
            'reduced_motion' => $reducedMotion,
            'browser' => [
                'name' => $browserName,
                'version' => $browserVersion,
            ],
        ];

        return [
            'hash' => sha1(json_encode($devicePayload)),
            'client_id' => PulseClient::factory(),
            'session_id' => PulseSession::factory(),
            'user_agent' => $userAgent,
            'category' => $category,
            'os' => $os,
            'is_touch_capable' => $isTouchCapable,
            'view_port' => $viewPort,
            'touch' => $touch,
            'pointer' => $pointer,
            'hover' => $hover,
            'dpr' => $dpr,
            'width' => $width,
            'height' => $height,
            'orientation' => $orientation,
            'reduced_motion' => $reducedMotion,
            'browser_name' => $browserName,
            'browser_version' => $browserVersion,
        ];
    }
}
