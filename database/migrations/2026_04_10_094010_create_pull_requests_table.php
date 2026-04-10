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
        Schema::create('pull_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('repository_id')->constrained()->cascadeOnDelete();

            $table->unsignedBigInteger('github_pull_request_id')->unique();
            $table->unsignedInteger('github_number');
            $table->string('title');
            $table->string('state');
            $table->boolean('is_draft')->default(false);

            $table->string('author_login')->nullable();
            $table->string('author_avatar_url')->nullable();

            $table->string('source_branch')->nullable();
            $table->string('target_branch')->nullable();

            $table->string('html_url');
            $table->string('api_url')->nullable();

            $table->timestamp('github_created_at')->nullable();
            $table->timestamp('github_updated_at')->nullable();
            $table->timestamp('github_closed_at')->nullable();
            $table->timestamp('github_merged_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();

            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['repository_id', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pull_requests');
    }
};
