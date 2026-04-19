<?php

namespace Tests\Feature\Domain\Transaction\Presentation\API;

use Source\Domain\System\Infrastructure\Models\Setting;
use Source\Domain\Transaction\Application\Actions\ProcessSepayWebhookAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

class SepayWebhookAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Setting::set('api_key_sepay', 'test_key_123');
    }

    public function test_it_allows_request_with_correct_apikey_format(): void
    {
        $this->mock(ProcessSepayWebhookAction::class, function (MockInterface $mock) {
            $mock->shouldReceive('execute')->once();
        });

        $response = $this->postJson('/api/sepay-webhook', ['id' => 'WS1'], [
            'Authorization' => 'Apikey test_key_123',
        ]);

        $response->assertStatus(200);
    }

    public function test_it_rejects_request_with_incorrect_apikey(): void
    {
        $response = $this->postJson('/api/sepay-webhook', ['id' => 'WS1'], [
            'Authorization' => 'Apikey WRONG_KEY',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['success' => false, 'message' => 'Unauthorized']);
    }

    public function test_it_rejects_request_with_missing_apikey_prefix(): void
    {
        // Even if the token is correct, if 'Apikey ' prefix is missing, it should fail
        $response = $this->postJson('/api/sepay-webhook', ['id' => 'WS1'], [
            'Authorization' => 'test_key_123',
        ]);

        $response->assertStatus(401);
    }

    public function test_it_rejects_request_with_no_authorization_header(): void
    {
        $response = $this->postJson('/api/sepay-webhook', ['id' => 'WS1']);

        $response->assertStatus(401);
    }
}
