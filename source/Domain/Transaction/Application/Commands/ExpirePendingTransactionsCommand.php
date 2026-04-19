<?php

namespace Source\Domain\Transaction\Application\Commands;

use Illuminate\Console\Command;
use Source\Domain\Transaction\Infrastructure\Models\Transaction;
use Source\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use Illuminate\Support\Carbon;

class ExpirePendingTransactionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and expire PENDING transactions that are past their 15m TTL.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredCount = 0;

        $transactions = Transaction::where('sync_status', SyncStatusEnum::PENDING->value)
            ->where('expires_at', '<=', Carbon::now())
            ->get();

        foreach ($transactions as $transaction) {
            $transaction->update([
                'sync_status' => SyncStatusEnum::EXPIRED,
                'expired_at' => Carbon::now(),
            ]);
            $expiredCount++;
        }

        $this->info("Successfully expired {$expiredCount} transactions.");
    }
}
