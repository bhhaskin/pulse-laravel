<?php

namespace Bhhaskin\PulseLaravel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PulseController
{
    /**
     * Acknowledge Pulse beacon requests without returning a payload.
     */
    public function metrics(Request $request): Response
    {
        // TODO: Capture beacon payload for downstream processing as Pulse evolves.
        return response()->noContent();
    }
}
