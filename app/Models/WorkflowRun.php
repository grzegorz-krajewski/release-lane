<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkflowRun extends Model
{
    protected $fillable = [
        'user_id',
        'repository_id',
        'github_workflow_run_id',
        'github_workflow_id',
        'name',
        'display_title',
        'run_number',
        'run_attempt',
        'status',
        'conclusion',
        'event',
        'head_branch',
        'head_sha',
        'actor_login',
        'html_url',
        'api_url',
        'run_started_at',
        'github_created_at',
        'github_updated_at',
        'last_synced_at',
        'raw_payload',
    ];

    protected $casts = [
        'run_started_at' => 'datetime',
        'github_created_at' => 'datetime',
        'github_updated_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }
}