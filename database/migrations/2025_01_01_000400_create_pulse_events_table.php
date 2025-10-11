<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pulse_events', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->foreignId('batch_id')->constrained('pulse_raw_batches')->cascadeOnDelete();
            $table->foreignId('client_id')->nullable()->constrained('pulse_clients')->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('pulse_sessions')->cascadeOnDelete();
            $table->foreignId('device_id')->nullable()->constrained('pulse_devices')->cascadeOnDelete();
            $table->string('event_type')->nullable()->index();
            $table->string('event_name')->nullable()->index();
            $table->text('url')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pulse_events');
    }
};
