<?php

namespace Bhhaskin\Pulse\Jobs;

use Bhhaskin\Pulse\Models\PulseClient;
use Bhhaskin\Pulse\Models\PulseDevice;
use Bhhaskin\Pulse\Models\PulseEvent;
use Bhhaskin\Pulse\Models\PulseRawBatch;
use Bhhaskin\Pulse\Models\PulseSession;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\DatabaseManager;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class ProcessPulseBatch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $batchId)
    {
    }

    public function handle(): void
    {
        $batch = PulseRawBatch::find($this->batchId);

        if (! $batch || $batch->processed_at) {
            return;
        }

        $events = $batch->events ?? [];

        /** @var DatabaseManager $db */
        $db = app(DatabaseManager::class);

        foreach ($events as $event) {
            if (! is_array($event)) {
                continue;
            }

            $validated = $this->validateEvent($event);

            if (! $validated) {
                continue;
            }

            $db->connection()->transaction(function () use ($batch, $validated): void {
                $payload = Arr::get($validated, 'payload', []);
                $timestamp = $this->parseDate(Arr::get($validated, 'sentAt')) ?? CarbonImmutable::now();
                $userAgent = Arr::get($payload, 'user_agent')
                    ?? Arr::get($payload, 'device.userAgent');

                $client = $this->resolveClient($validated, $timestamp);
                $session = $this->resolveSession($validated, $client, $timestamp);
                $device = $this->resolveDevice($payload, $userAgent, $client, $session);

                PulseEvent::create([
                    'batch_id' => $batch->id,
                    'client_id' => $client?->id,
                    'session_id' => $session?->id,
                    'device_id' => $device?->id,
                    'event_type' => Arr::get($validated, 'eventType'),
                    'event_name' => Arr::get($validated, 'eventName'),
                    'url' => Arr::get($validated, 'url'),
                    'payload' => $payload,
                    'sent_at' => $timestamp,
                ]);
            });
        }

        $batch->update(['processed_at' => CarbonImmutable::now()]);
    }

    protected function validateEvent(array $event): ?array
    {
        $validator = Validator::make($event, [
            'eventType' => ['required', 'string', 'max:255'],
            'eventName' => ['required', 'string', 'max:255'],
            'payload' => ['nullable', 'array'],
            'payload.user_agent' => ['nullable', 'string', 'max:1024'],
            'payload.device' => ['nullable', 'array'],
            'payload.device.category' => ['nullable', 'string', 'max:255'],
            'payload.device.os' => ['nullable', 'string', 'max:255'],
            'payload.device.userAgent' => ['nullable', 'string'],
            'payload.device.isTouchCapable' => ['nullable', 'boolean'],
            'payload.device.view_port' => ['nullable', 'string', 'max:255'],
            'payload.device.touch' => ['nullable', 'boolean'],
            'payload.device.pointer' => ['nullable', 'string', 'max:255'],
            'payload.device.hover' => ['nullable', 'string', 'max:255'],
            'payload.device.dpr' => ['nullable', 'numeric'],
            'payload.device.width' => ['nullable', 'integer'],
            'payload.device.height' => ['nullable', 'integer'],
            'payload.device.orientation' => ['nullable', 'string', 'max:255'],
            'payload.device.reduced_motion' => ['nullable', 'boolean'],
            'payload.device.browser' => ['nullable', 'array'],
            'payload.device.browser.name' => ['nullable', 'string', 'max:255'],
            'payload.device.browser.version' => ['nullable', 'string', 'max:255'],
            'clientUuid' => ['nullable', 'string', 'max:255'],
            'sessionUuid' => ['nullable', 'string', 'max:255'],
            'url' => ['nullable', 'string', 'max:2048'],
            'sentAt' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return null;
        }

        return $validator->validated();
    }

    protected function parseDate(?string $value): ?CarbonImmutable
    {
        if (! $value) {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function resolveClient(array $event, CarbonImmutable $timestamp): ?PulseClient
    {
        $uuid = Arr::get($event, 'clientUuid');

        if (! $uuid) {
            return null;
        }

        /** @var PulseClient $client */
        $client = PulseClient::firstOrCreate(
            ['uuid' => $uuid],
            ['first_seen_at' => $timestamp, 'last_seen_at' => $timestamp]
        );

        $this->updateSeenTimestamps($client, $timestamp);

        return $client;
    }

    protected function resolveSession(array $event, ?PulseClient $client, CarbonImmutable $timestamp): ?PulseSession
    {
        $uuid = Arr::get($event, 'sessionUuid');

        if (! $uuid) {
            return null;
        }

        /** @var PulseSession $session */
        $session = PulseSession::firstOrCreate(
            ['uuid' => $uuid],
            [
                'client_id' => $client?->id,
                'first_seen_at' => $timestamp,
                'last_seen_at' => $timestamp,
            ]
        );

        if ($client && $session->client_id !== $client->id) {
            $session->client_id = $client->id;
        }

        $this->updateSeenTimestamps($session, $timestamp);

        return $session;
    }

    protected function resolveDevice(array $payload, ?string $userAgent, ?PulseClient $client, ?PulseSession $session): ?PulseDevice
    {
        $devicePayload = Arr::get($payload, 'device');

        if (! is_array($devicePayload) || empty($devicePayload)) {
            return null;
        }

        $hash = sha1(json_encode($devicePayload));

        /** @var PulseDevice $device */
        $device = PulseDevice::firstOrNew(['hash' => $hash]);

        if (! $device->exists) {
            $device->client_id = $client?->id;
            $device->session_id = $session?->id;
        }

        if ($client && $device->client_id === null) {
            $device->client_id = $client->id;
        }

        if ($session && $device->session_id === null) {
            $device->session_id = $session->id;
        }

        $device->fill([
            'user_agent' => $userAgent,
            'category' => Arr::get($devicePayload, 'category'),
            'os' => Arr::get($devicePayload, 'os'),
            'is_touch_capable' => Arr::get($devicePayload, 'isTouchCapable'),
            'view_port' => Arr::get($devicePayload, 'view_port'),
            'touch' => Arr::get($devicePayload, 'touch'),
            'pointer' => Arr::get($devicePayload, 'pointer'),
            'hover' => Arr::get($devicePayload, 'hover'),
            'dpr' => Arr::get($devicePayload, 'dpr'),
            'width' => Arr::get($devicePayload, 'width'),
            'height' => Arr::get($devicePayload, 'height'),
            'orientation' => Arr::get($devicePayload, 'orientation'),
            'reduced_motion' => Arr::get($devicePayload, 'reduced_motion'),
            'browser_name' => Arr::get($devicePayload, 'browser.name'),
            'browser_version' => Arr::get($devicePayload, 'browser.version'),
        ]);

        $device->save();

        return $device;
    }

    protected function updateSeenTimestamps(PulseClient|PulseSession $model, CarbonImmutable $timestamp): void
    {
        if ($model->first_seen_at === null || $timestamp->lt($model->first_seen_at)) {
            $model->first_seen_at = $timestamp;
        }

        if ($model->last_seen_at === null || $timestamp->gt($model->last_seen_at)) {
            $model->last_seen_at = $timestamp;
        }

        $model->save();
    }
}
