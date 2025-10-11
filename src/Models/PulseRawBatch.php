<?php

namespace Bhhaskin\Pulse\Models;

use Bhhaskin\Pulse\Database\Factories\PulseRawBatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PulseRawBatch extends Model
{
    use HasFactory;

    protected $table = 'pulse_raw_batches';

    protected $guarded = [];

    protected $casts = [
        'events' => 'array',
        'batch_sent_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function newFactory(): PulseRawBatchFactory
    {
        return PulseRawBatchFactory::new();
    }
}
