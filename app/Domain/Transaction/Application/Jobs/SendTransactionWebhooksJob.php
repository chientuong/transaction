<?php

namespace App\Domain\Transaction\Application\Jobs;

use App\Domain\System\Infrastructure\Models\Setting;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use App\Domain\Transaction\Infrastructure\Models\WebhookLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SendTransactionWebhooksJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $transactionId
    ) {}

    public function handle(): void
    {
        $transaction = Transaction::with(['bankAccount', 'prefix'])->find($this->transactionId);

        if (!$transaction) {
            return;
        }

        $webhooks = Setting::get('webhook_configs', []);

        if (empty($webhooks)) {
            Log::info("No webhooks configured for transaction {$transaction->transaction_code}");
            return;
        }

        $payload = [
            'event' => 'transaction.confirmed',
            'data' => [
                'transaction_code' => $transaction->transaction_code,
                'amount' => (float)$transaction->amount,
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

            if (!$url) continue;

            $log = WebhookLog::create([
                'transaction_id' => $transaction->id,
                'url' => $url,
                'method' => $method,
                'payload' => $payload,
            ]);

            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$apiKey}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])->send($method, $url, [
                    'data' => $payload,
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
                Log::error("Error sending webhook to {$url}: " . $e->getMessage());
            }
        }
    }
}
