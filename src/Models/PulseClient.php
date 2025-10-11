<?php

namespace Bhhaskin\Pulse\Models;

use Bhhaskin\Pulse\Database\Factories\PulseClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseClient extends Model
{
    use HasFactory;

    protected $table = 'pulse_clients';

    protected $guarded = [];

    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    protected static function newFactory(): PulseClientFactory
    {
        return PulseClientFactory::new();
    }
}
