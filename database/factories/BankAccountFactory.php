<?php

namespace Database\Factories;

use Source\Domain\Transaction\Infrastructure\Models\BankAccount;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BankAccount>
 */
class BankAccountFactory extends Factory
{
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_code' => $this->faker->word(),
            'bank_branch' => $this->faker->city(),
            'account_number' => $this->faker->unique()->numerify('##########'),
            'account_holder' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'is_default' => false,
            'created_by' => User::factory(),
        ];
    }
}
