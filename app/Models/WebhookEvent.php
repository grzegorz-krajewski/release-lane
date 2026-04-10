<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookEvent extends Model
{
    protected $fillable = [
        'user_id',
        'repository_id',
        'provider',
        'event_type',
        'action',
        'delivery_id',
        'repository_full_name',
        'is_valid_signature',
        'received_at',
        'headers',
        'payload',
    ];

    protected $casts = [
        'is_valid_signature' => 'boolean',
        'received_at' => 'datetime',
        'headers' => 'array',
        'payload' => 'array',
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