<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GitHubConnection extends Model
{
    protected $table = 'github_connections';

    protected $fillable = [
        'user_id',
        'access_token',
        'is_active',
        'last_tested_at',
    ];

    protected $casts = [
        'access_token' => 'encrypted',
        'is_active' => 'boolean',
        'last_tested_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}