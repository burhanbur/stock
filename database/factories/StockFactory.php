<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stock>
 */
class StockFactory extends Factory
{
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'ticker' => strtoupper(fake()->unique()->lexify('????')),
            'exchange' => 'IDX',
            'currency' => 'IDR',
            'listed_at' => fake()->dateTimeBetween('-20 years', '-1 year'),
            'is_active' => true,
        ];
    }
}
