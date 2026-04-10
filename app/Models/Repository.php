<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Repository extends Model
{
    protected $fillable = [
        'user_id',
        'github_repository_id',
        'name',
        'full_name',
        'owner_login',
        'description',
        'is_private',
        'is_fork',
        'default_branch',
        'html_url',
        'api_url',
        'language',
        'stargazers_count',
        'watchers_count',
        'forks_count',
        'github_created_at',
        'github_updated_at',
        'github_pushed_at',
        'last_synced_at',
        'raw_payload',
    ];

    protected $casts = [
        'is_private' => 'boolean',
        'is_fork' => 'boolean',
        'github_created_at' => 'datetime',
        'github_updated_at' => 'datetime',
        'github_pushed_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}