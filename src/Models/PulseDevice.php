<?php

namespace Bhhaskin\Pulse\Models;

use Bhhaskin\Pulse\Database\Factories\PulseDeviceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseDevice extends Model
{
    use HasFactory;

    protected $table = 'pulse_devices';

    protected $guarded = [];

    protected $casts = [
        'is_touch_capable' => 'boolean',
        'touch' => 'boolean',
        'dpr' => 'float',
        'width' => 'integer',
        'height' => 'integer',
        'reduced_motion' => 'boolean',
    ];

    protected static function newFactory(): PulseDeviceFactory
    {
        return PulseDeviceFactory::new();
    }
}
