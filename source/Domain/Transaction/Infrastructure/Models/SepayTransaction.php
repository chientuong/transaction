<?php

namespace Source\Domain\Transaction\Infrastructure\Models;

use Illuminate\Database\Eloquent\Model;

class SepayTransaction extends Model
{
    protected $fillable = [
        'sepay_id',
        'gateway',
        'transaction_date',
        'account_number',
        'sub_account',
        'amount_in',
        'amount_out',
        'accumulated',
        'code',
        'content',
        'reference_code',
        'description',
        'raw_data',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'amount_in' => 'decimal:2',
        'amount_out' => 'decimal:2',
        'accumulated' => 'decimal:2',
        'raw_data' => 'array',
    ];
}
