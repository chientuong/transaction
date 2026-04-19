<?php

namespace Source\Domain\Transaction\Application\Jobs;

use Source\Domain\System\Infrastructure\Models\Setting;
use Source\Domain\Transaction\Infrastructure\Models\Transaction;
use Source\Domain\Transaction\Infrastructure\Models\WebhookLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendTransactionWebhooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $transactionId,
        public bool $isSyncChanged = true,
        public bool $isOpsChanged = true
    ) {}

    public function handle(): void
    {
        $transaction = Transaction::with(['bankAccount', 'prefix'])->find($this->transactionId);

        if (! $transaction) {
            return;
        }

        $webhooks = Setting::get('webhook_configs', []);

        if (empty($webhooks)) {
            Log::info("No webhooks configured for transaction {$transaction->transaction_code}");

            return;
        }

        $triggerSyncStatus = (array) Setting::get('trigger_sync_status', []);
        $triggerOpsStatus = (array) Setting::get('trigger_ops_status', []);
        $triggerMode = Setting::get('trigger_mode', 'AND');

        $syncMatched = empty($triggerSyncStatus) || in_array($transaction->sync_status->value, $triggerSyncStatus, true);
        $opsMatched = empty($triggerOpsStatus) || in_array($transaction->ops_status->value, $triggerOpsStatus, true);

        if ($triggerMode === 'OR') {
            // In OR mode, at least one of the configured sets must match.
            // AND specifically: only fire for a set IF it was the one that changed.
            $syncTriggered = $this->isSyncChanged && ! empty($triggerSyncStatus) && $syncMatched;
            $opsTriggered = $this->isOpsChanged && ! empty($triggerOpsStatus) && $opsMatched;
            $isTriggered = $syncTriggered || $opsTriggered;
        } else {
            // AND mode: if both are empty, we don't fire. Otherwise, both must match (empty=wildcard).
            $isTriggered = $syncMatched && $opsMatched;
            if (empty($triggerSyncStatus) && empty($triggerOpsStatus)) {
                $isTriggered = false;
            }
        }

        if (! $isTriggered) {
            Log::info("Skipping webhooks for transaction {$transaction->transaction_code} due to trigger condition mismatch (Mode: {$triggerMode}).");

            return;
        }

        $payload = [
            'event' => 'transaction.confirmed',
            'data' => [
                'transaction_code' => $transaction->transaction_code,
                'amount' => (float) $transaction->amount,
                'transfer_content' => $transaction->transfer_content,
                'ops_status' => $transaction->ops_status->value,
                'sync_status' => $transaction->sync_status->value,
                'confirmed_at' => $transaction->confirmed_at?->toIso8601String(),
                'bank_account' => $transaction->bankAccount?->only(['account_number', 'bank_name', 'account_holder']),
            ],
        ];

        foreach ($webhooks as $webhook) {
            $url = $webhook['url'] ?? null;
            $method = strtoupper($webhook['method'] ?? 'POST');
            $apiKey = $webhook['api_key'] ?? null;
            $dataKey = $webhook['payload_data_key'] ?? 'data';

            if (! $url) {
                continue;
            }

            $currentPayload = $payload;
            $currentPayload['event'] = 'transaction.status_changed';

            $log = WebhookLog::create([
                'transaction_id' => $transaction->id,
                'url' => $url,
                'method' => $method,
                'payload' => $currentPayload,
            ]);

            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->send($method, $url, [
                    'json' => [
                        $dataKey => $currentPayload,
                    ],
                ]);

                $log->update([
                    'status_code' => $response->status(),
                    'response_body' => $response->body(),
                ]);

                if ($response->successful()) {
                    Log::info("Webhook sent successfully to {$url} for transaction {$transaction->transaction_code}");
                } else {
                    Log::error("Failed to send webhook to {$url} for transaction {$transaction->transaction_code}. Status: {$response->status()}");
                }
            } catch (Exception $e) {
                $log->update([
                    'error_message' => $e->getMessage(),
                ]);
                Log::error("Error sending webhook to {$url}: ".$e->getMessage());
            }
        }
    }
}
