<?php

namespace Database\Factories;

use App\Domain\Transaction\Domain\Enums\OpsStatusEnum;
use App\Domain\Transaction\Domain\Enums\SyncStatusEnum;
use App\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use App\Domain\Transaction\Infrastructure\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'prefix_id' => PaymentPrefix::factory(),
            'amount' => $this->faker->randomFloat(2, 1000, 1000000),
            'bank_account_id' => BankAccount::factory(),
            'transfer_content' => $this->faker->sentence(),
            'sync_status' => SyncStatusEnum::PENDING,
            'ops_status' => OpsStatusEnum::UNREVIEWED,
            'expires_at' => now()->addMinutes(15),
        ];
    }
}
