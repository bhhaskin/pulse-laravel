<?php

use Bhhaskin\PulseLaravel\Http\Controllers\PulseController;
use Illuminate\Support\Facades\Route;

Route::post('/', [PulseController::class, 'metrics'])
    ->name('pulse-laravel.index');
