<?php

namespace Tests\Feature\Domain\Transaction\Application\Jobs;

use Source\Domain\System\Infrastructure\Models\Setting;
use Source\Domain\Transaction\Domain\Enums\OpsStatusEnum;
use Source\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use Source\Domain\Transaction\Infrastructure\Models\Transaction;
use Source\Domain\Transaction\Application\Jobs\SendTransactionWebhooksJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WebhookTriggerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
    }

    public function test_it_sends_webhook_when_statuses_match_and_mode(): void
    {
        Setting::set('trigger_mode', 'AND');
        Setting::set('trigger_sync_status', [SyncStatusEnum::RECEIVED_SIGNAL->value]);
        Setting::set('trigger_ops_status', [OpsStatusEnum::CONFIRMED->value]);
        Setting::set('webhook_configs', [
            [
                'url' => 'https://example.com/webhook',
                'payload_data_key' => 'transaction_details',
            ],
        ]);

        // Creation handles the dispatch
        Transaction::factory()->create([
            'sync_status' => SyncStatusEnum::RECEIVED_SIGNAL,
            'ops_status' => OpsStatusEnum::CONFIRMED,
        ]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://example.com/webhook' &&
                   isset($request->data()['transaction_details']);
        });
    }

    public function test_it_does_not_send_webhook_when_one_fails_in_and_mode(): void
    {
        Setting::set('trigger_mode', 'AND');
        Setting::set('trigger_sync_status', [SyncStatusEnum::RECEIVED_SIGNAL->value]);
        Setting::set('trigger_ops_status', [OpsStatusEnum::CONFIRMED->value]);
        Setting::set('webhook_configs', [['url' => 'https://example.com/webhook']]);

        // Creation handles the dispatch
        Transaction::factory()->create([
            'sync_status' => SyncStatusEnum::RECEIVED_SIGNAL, // Matches
            'ops_status' => OpsStatusEnum::UNREVIEWED,       // Fails
        ]);

        Http::assertNothingSent();
    }

    public function test_it_sends_webhook_when_one_matches_in_or_mode(): void
    {
        Setting::set('trigger_mode', 'OR');
        Setting::set('trigger_sync_status', [SyncStatusEnum::RECEIVED_SIGNAL->value]);
        Setting::set('trigger_ops_status', [OpsStatusEnum::CONFIRMED->value]);
        Setting::set('webhook_configs', [['url' => 'https://example.com/webhook']]);

        // Scenario 1: Sync matches, Ops fails
        // Create with matching Sync status - should trigger via created hook
        Transaction::factory()->create([
            'sync_status' => SyncStatusEnum::RECEIVED_SIGNAL,
            'ops_status' => OpsStatusEnum::UNREVIEWED,
        ]);

        Http::assertSentCount(1);
    }

    public function test_it_sends_webhook_on_transaction_creation(): void
    {
        Setting::set('trigger_mode', 'AND');
        Setting::set('trigger_sync_status', [SyncStatusEnum::RECEIVED_SIGNAL->value]);
        Setting::set('trigger_ops_status', [OpsStatusEnum::CONFIRMED->value]);
        Setting::set('webhook_configs', [['url' => 'https://example.com/creation-webhook']]);

        // Creating the transaction should trigger the job automatically via created hook
        Transaction::factory()->create([
            'sync_status' => SyncStatusEnum::RECEIVED_SIGNAL,
            'ops_status' => OpsStatusEnum::CONFIRMED,
        ]);

        Http::assertSent(fn ($request) => $request->url() === 'https://example.com/creation-webhook');
    }

    public function test_it_only_triggers_for_changed_field_in_or_mode(): void
    {
        Setting::set('trigger_mode', 'OR');
        Setting::set('trigger_sync_status', [SyncStatusEnum::RECEIVED_SIGNAL->value]);
        Setting::set('trigger_ops_status', [OpsStatusEnum::CONFIRMED->value]);
        Setting::set('webhook_configs', [['url' => 'https://example.com/or-webhook']]);

        // Scenario: Sync matches but DID NOT change. Ops changed but DOES NOT match.
        // Result: Should NOT trigger.
        $transaction = Transaction::factory()->create([
            'sync_status' => SyncStatusEnum::RECEIVED_SIGNAL,
            'ops_status' => OpsStatusEnum::UNREVIEWED,
        ]);

        Http::fake(); // Reset recorded

        // Simulate update where only ops_status changed (but sync_status still matches target)
        SendTransactionWebhooksJob::dispatchSync($transaction->id, false, true);

        Http::assertNothingSent();

        // Scenario: Sync changed and matches.
        // Result: Should trigger.
        SendTransactionWebhooksJob::dispatchSync($transaction->id, true, false);
        Http::assertSentCount(1);
    }
}
