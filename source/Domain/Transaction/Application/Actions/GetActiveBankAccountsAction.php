<?php

namespace Source\Domain\Transaction\Application\Actions;

use Source\Domain\Transaction\Infrastructure\Models\BankAccount;
use Illuminate\Database\Eloquent\Collection;

class GetActiveBankAccountsAction
{
    public function execute(): Collection
    {
        return BankAccount::where('is_active', true)
            ->get(['id', 'bank_name', 'bank_branch', 'account_number', 'account_holder']);
    }
}
