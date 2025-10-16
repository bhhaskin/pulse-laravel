<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pulse_sessions', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('uuid')->unique();
            $table->foreignId('client_id')->nullable()->constrained('pulse_clients')->cascadeOnDelete();
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();

            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pulse_sessions');
    }
};
