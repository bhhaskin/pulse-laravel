<?php

namespace Bhhaskin\Pulse\Http\Controllers;

use Bhhaskin\Pulse\Jobs\ProcessPulseBatch;
use Bhhaskin\Pulse\Models\PulseRawBatch;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class PulseController
{
    /**
     * Acknowledge Pulse beacon requests without returning a payload.
     */
    public function metrics(Request $request): Response
    {
        $batchPayload = $this->extractPayload($request);

        $validator = Validator::make($batchPayload, [
            'events' => ['required', 'array'],
            'batchSentAt' => ['nullable', 'date'],
        ]);

        if ($validator->fails()) {
            return response()->noContent();
        }

        $validated = $validator->validated();

        $rawBatch = PulseRawBatch::create([
            'events' => $validated['events'],
            'batch_sent_at' => $this->parseDate(Arr::get($batchPayload, 'batchSentAt')),
        ]);

        ProcessPulseBatch::dispatch($rawBatch->id);

        return response()->noContent();
    }

    protected function extractPayload(Request $request): array
    {
        $content = $request->getContent();

        if ($content !== '') {
            $decoded = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return $request->all();
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
}
