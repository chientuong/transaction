<?php

namespace Database\Factories;

use App\Domain\Transaction\Infrastructure\Models\BankAccount;
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
            'account_number' => $this->faker->numerify('##########'),
            'account_holder' => $this->faker->name(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
