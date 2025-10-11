<?php

namespace Bhhaskin\Pulse\Models;

use Bhhaskin\Pulse\Database\Factories\PulseEventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseEvent extends Model
{
    use HasFactory;

    protected $table = 'pulse_events';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
    ];

    protected static function newFactory(): PulseEventFactory
    {
        return PulseEventFactory::new();
    }
}
