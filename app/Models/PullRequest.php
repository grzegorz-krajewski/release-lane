<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PullRequest extends Model
{
    protected $fillable = [
        'user_id',
        'repository_id',
        'github_pull_request_id',
        'github_number',
        'title',
        'state',
        'is_draft',
        'author_login',
        'author_avatar_url',
        'source_branch',
        'target_branch',
        'html_url',
        'api_url',
        'github_created_at',
        'github_updated_at',
        'github_closed_at',
        'github_merged_at',
        'last_synced_at',
        'raw_payload',
    ];

    protected $casts = [
        'is_draft' => 'boolean',
        'github_created_at' => 'datetime',
        'github_updated_at' => 'datetime',
        'github_closed_at' => 'datetime',
        'github_merged_at' => 'datetime',
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