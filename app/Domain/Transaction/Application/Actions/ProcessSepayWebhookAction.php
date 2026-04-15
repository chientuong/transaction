<?php

namespace App\Domain\Transaction\Application\Actions;

use App\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use App\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Domain\Transaction\Infrastructure\Models\SepayTransaction;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessSepayWebhookAction
{
    public function execute(array $data): void
    {
        DB::transaction(function () use ($data) {
            // 1. Log the incoming transaction
            $sepayLog = SepayTransaction::create([
                'sepay_id' => $data['id'] ?? null,
                'gateway' => $data['gateway'] ?? null,
                'transaction_date' => $data['transactionDate'] ?? null,
                'account_number' => $data['accountNumber'] ?? null,
                'sub_account' => $data['subAccount'] ?? null,
                'amount_in' => ($data['transferType'] ?? '') === 'in' ? ($data['transferAmount'] ?? 0) : 0,
                'amount_out' => ($data['transferType'] ?? '') === 'out' ? ($data['transferAmount'] ?? 0) : 0,
                'accumulated' => $data['accumulated'] ?? 0,
                'code' => $data['code'] ?? null,
                'content' => $data['content'] ?? null,
                'reference_code' => $data['referenceCode'] ?? null,
                'description' => $data['description'] ?? null,
                'raw_data' => $data,
            ]);

            // 2. Logic to match with existing transactions
            $content = $data['content'] ?? '';
            $amount = $data['transferAmount'] ?? 0;

            if (($data['transferType'] ?? '') !== 'in') {
                return;
            }

            // 3. Find the corresponding BankAccount
            $gateway = $data['gateway'] ?? '';
            $accountNumber = $data['accountNumber'] ?? '';

            $bankAccount = BankAccount::where('bank_code', $gateway)
                ->where('account_number', $accountNumber)
                ->where('is_active', true)
                ->first();

            if (!$bankAccount) {
                Log::warning("No active bank account found matching Gateway: {$gateway} and Account Number: {$accountNumber}");
                return;
            }

            // Try to find a PENDING transaction
            $transaction = Transaction::where('bank_account_id', $bankAccount->id)
                ->where('sync_status', SyncStatusEnum::PENDING)
                ->where('amount', (float)$amount)
                ->get()
                ->first(function ($t) use ($content) {
                    return stripos($content, $t->transaction_code) !== false;
                });

            if ($transaction) {
                $transaction->update([
                    'sync_status' => SyncStatusEnum::RECEIVED_SIGNAL,
                    'ops_note' => ($transaction->ops_note ? $transaction->ops_note . "\n" : "") . "Sepay Signal Received (ID: {$sepayLog->sepay_id})",
                ]);
                
                Log::info("Transaction {$transaction->transaction_code} updated to RECEIVED_SIGNAL via SePay Webhook.");
            }
        });
    }
}
