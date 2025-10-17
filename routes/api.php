<?php

use Bhhaskin\Pulse\Http\Controllers\PulseController;
use Illuminate\Support\Facades\Route;

Route::post(config('pulse.endpoint', 'pulse'), [PulseController::class, 'metrics'])
    ->name('pulse.index');
