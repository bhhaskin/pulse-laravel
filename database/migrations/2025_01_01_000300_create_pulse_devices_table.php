<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pulse_devices', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('hash')->unique();
            $table->foreignId('client_id')->nullable()->constrained('pulse_clients')->cascadeOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('pulse_sessions')->cascadeOnDelete();
            $table->text('user_agent')->nullable();
            $table->string('category')->nullable();
            $table->string('os')->nullable();
            $table->boolean('is_touch_capable')->nullable();
            $table->string('view_port')->nullable();
            $table->boolean('touch')->nullable();
            $table->string('pointer')->nullable();
            $table->string('hover')->nullable();
            $table->float('dpr')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('orientation')->nullable();
            $table->boolean('reduced_motion')->nullable();
            $table->string('browser_name')->nullable();
            $table->string('browser_version')->nullable();
            $table->timestamps();

            $table->index('client_id');
            $table->index('session_id');
            $table->index(['client_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pulse_devices');
    }
};
