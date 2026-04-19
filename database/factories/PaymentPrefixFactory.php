<?php

namespace Database\Factories;

use Source\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentPrefixFactory extends Factory
{
    protected $model = PaymentPrefix::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'prefix_code' => $this->faker->unique()->lexify('??????'),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'created_by' => User::factory(),
        ];
    }
}
