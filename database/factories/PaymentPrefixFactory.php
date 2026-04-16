<?php

namespace Database\Factories;

use App\Domain\Transaction\Infrastructure\Models\PaymentPrefix;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentPrefixFactory extends Factory
{
    protected $model = PaymentPrefix::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'prefix_code' => $this->faker->unique()->lexify('????'),
            'description' => $this->faker->sentence(),
            'is_active' => true,
        ];
    }
}
