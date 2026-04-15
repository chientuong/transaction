<?php

namespace App\Domain\Transaction\Application\Actions;

use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use Illuminate\Database\Eloquent\Collection;

class GetActivePaymentPrefixesAction
{
    public function execute(): Collection
    {
        return PaymentPrefix::where('is_active', true)
            ->get(['id', 'name', 'prefix_code', 'description']);
    }
}
