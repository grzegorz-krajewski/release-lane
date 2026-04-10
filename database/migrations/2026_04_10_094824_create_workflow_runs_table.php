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
        Schema::create('workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('repository_id')->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('github_workflow_run_id')->unique();
            $table->unsignedBigInteger('github_workflow_id')->nullable();

            $table->string('name')->nullable();
            $table->string('display_title')->nullable();

            $table->unsignedBigInteger('run_number')->nullable();
            $table->unsignedBigInteger('run_attempt')->nullable();

            $table->string('status')->nullable();
            $table->string('conclusion')->nullable();
            $table->string('event')->nullable();

            $table->string('head_branch')->nullable();
            $table->string('head_sha')->nullable();

            $table->string('actor_login')->nullable();
            $table->string('html_url');
            $table->string('api_url')->nullable();

            $table->timestamp('run_started_at')->nullable();
            $table->timestamp('github_created_at')->nullable();
            $table->timestamp('github_updated_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();

            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['repository_id', 'status']);
            $table->index(['repository_id', 'conclusion']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_runs');
    }
};
