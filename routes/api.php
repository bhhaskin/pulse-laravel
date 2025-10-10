<?php

use Bhhaskin\Pulse\Http\Controllers\PulseController;
use Illuminate\Support\Facades\Route;

Route::post('/', [PulseController::class, 'metrics'])
    ->name('pulse.index');
