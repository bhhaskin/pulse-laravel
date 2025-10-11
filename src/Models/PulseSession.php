<?php

namespace Bhhaskin\Pulse\Models;

use Bhhaskin\Pulse\Database\Factories\PulseSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseSession extends Model
{
    use HasFactory;

    protected $table = 'pulse_sessions';

    protected $guarded = [];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    protected static function newFactory(): PulseSessionFactory
    {
        return PulseSessionFactory::new();
    }
}
