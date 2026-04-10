<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('repository_id')->nullable()->constrained()->nullOnDelete();

            $table->string('provider')->default('github');
            $table->string('event_type');
            $table->string('action')->nullable();

            $table->string('delivery_id')->nullable()->unique();
            $table->string('repository_full_name')->nullable();

            $table->boolean('is_valid_signature')->default(false);
            $table->timestamp('received_at')->nullable();

            $table->json('headers')->nullable();
            $table->json('payload')->nullable();

            $table->timestamps();

            $table->index(['provider', 'event_type']);
            $table->index(['repository_id', 'received_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_events');
    }
};
