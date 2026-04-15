<?php

namespace App\Domain\Transaction\Application\Actions;

use App\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use App\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use Exception;
use Illuminate\Support\Facades\DB;

class CreateTransactionAction
{
    public function execute(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $prefix = PaymentPrefix::where('id', $data['prefix_id'])->where('is_active', true)->first();
            if (!$prefix) {
                throw new Exception("Payment prefix is not active or does not exist.");
            }

            $bankAccount = BankAccount::where('id', $data['bank_account_id'])->where('is_active', true)->first();
            if (!$bankAccount) {
                throw new Exception("Bank account is not active or does not exist.");
            }

            $transaction = new Transaction();
            $transaction->prefix_id = $data['prefix_id'];
            $transaction->bank_account_id = $data['bank_account_id'];
            $transaction->amount = $data['amount'];
            $transaction->user_id = $data['user_id'] ?? null;
            $transaction->sync_status = $data['sync_status'] ?? SyncStatusEnum::PENDING;
            $transaction->save();
            
            return $transaction->unsetRelation('prefix')->makeHidden('bankAccount');
        });
    }
}
