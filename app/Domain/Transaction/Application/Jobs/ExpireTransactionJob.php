<?php

namespace App\Domain\Transaction\Application\Jobs;

use App\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExpireTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected int $transactionId
    ) {}

    public function handle(): void
    {
        $transaction = Transaction::find($this->transactionId);

        if (!$transaction) {
            return;
        }

        // Only expire if still in PENDING status
        if ($transaction->sync_status === SyncStatusEnum::PENDING) {
            $transaction->update([
                'sync_status' => SyncStatusEnum::EXPIRED,
                'expired_at' => now(),
                'ops_note' => 'SYSTEM_TIMEOUT',
            ]);
        }
    }
}
