<?php

namespace Tests\Feature\Domain\Transaction\Application\Actions;

use App\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DefaultBankAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        \App\Domain\System\Infrastructure\Models\Setting::set('api_system', 'system-token');
    }

    public function test_it_uses_default_bank_account_when_none_provided(): void
    {
        $user = \App\Models\User::factory()->create();
        $prefix = PaymentPrefix::factory()->create(['is_active' => true, 'created_by' => $user->id]);

        $defaultAccount = BankAccount::factory()->create([
            'is_active' => true,
            'is_default' => true,
            'created_by' => $user->id,
        ]);

        $otherAccount = BankAccount::factory()->create([
            'is_active' => true,
            'is_default' => false,
            'created_by' => $user->id,
        ]);

        $response = $this->postJson('/api/transactions', [
            'prefix_id' => $prefix->id,
            'amount' => 1000,
        ], [
            'Authorization' => 'Bearer system-token',
        ]);

        $response->assertStatus(201);
        $this->assertEquals($defaultAccount->id, $response->json('data.bank_account_id'));
    }

    public function test_it_throws_exception_when_no_default_account_and_id_missing(): void
    {
        $user = \App\Models\User::factory()->create();
        $prefix = PaymentPrefix::factory()->create(['is_active' => true, 'created_by' => $user->id]);

        BankAccount::factory()->create([
            'is_active' => true,
            'is_default' => false,
            'created_by' => $user->id,
        ]);

        $response = $this->postJson('/api/transactions', [
            'prefix_id' => $prefix->id,
            'amount' => 1000,
        ], [
            'Authorization' => 'Bearer system-token',
        ]);

        $response->assertStatus(400);
        $response->assertJsonFragment(['message' => 'No default bank account found. Please provide a bank_account_id.']);
    }

    public function test_it_ensures_only_one_bank_account_is_active_and_default(): void
    {
        $user = \App\Models\User::factory()->create();
        $account1 = BankAccount::factory()->create(['is_active' => true, 'is_default' => true, 'created_by' => $user->id]);
        $this->assertTrue($account1->fresh()->is_default);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Đã có tài khoản mặc định đang hoạt động rồi.");

        BankAccount::factory()->create(['is_active' => true, 'is_default' => true, 'created_by' => $user->id]);
    }

    public function test_it_blocks_updating_second_account_to_active_and_default(): void
    {
        $user = \App\Models\User::factory()->create();
        $account1 = BankAccount::factory()->create(['is_active' => true, 'is_default' => true, 'created_by' => $user->id]);
        $account2 = BankAccount::factory()->create(['is_active' => true, 'is_default' => false, 'created_by' => $user->id]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Đã có tài khoản mặc định đang hoạt động rồi.");

        $account2->update(['is_default' => true]);
    }

    public function test_it_allows_multiple_defaults_if_only_one_is_active(): void
    {
        $user = \App\Models\User::factory()->create();
        $account1 = BankAccount::factory()->create(['is_active' => false, 'is_default' => true, 'created_by' => $user->id]);
        $account2 = BankAccount::factory()->create(['is_active' => true, 'is_default' => true, 'created_by' => $user->id]);
        
        $this->assertTrue($account1->fresh()->is_default);
        $this->assertTrue($account2->fresh()->is_default);
        $this->assertFalse($account1->fresh()->is_active);
        $this->assertTrue($account2->fresh()->is_active);
    }
}
