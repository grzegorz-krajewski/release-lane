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
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('github_repository_id')->unique();
            $table->string('name');
            $table->string('full_name');
            $table->string('owner_login');
            $table->text('description')->nullable();
            $table->boolean('is_private')->default(false);
            $table->boolean('is_fork')->default(false);
            $table->string('default_branch')->nullable();
            $table->string('html_url');
            $table->string('api_url')->nullable();
            $table->string('language')->nullable();
            $table->integer('stargazers_count')->default(0);
            $table->integer('watchers_count')->default(0);
            $table->integer('forks_count')->default(0);
            $table->timestamp('github_created_at')->nullable();
            $table->timestamp('github_updated_at')->nullable();
            $table->timestamp('github_pushed_at')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};
