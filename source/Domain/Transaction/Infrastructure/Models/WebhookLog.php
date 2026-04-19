<?php

namespace Source\Domain\Transaction\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'url',
        'method',
        'status_code',
        'payload',
        'response_body',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
