<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pulse_raw_batches', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->json('events');
            $table->timestamp('batch_sent_at')->nullable()->index();
            $table->timestamp('processed_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pulse_raw_batches');
    }
};
