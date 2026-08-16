<?php

namespace Database\Factories;

use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockPrice>
 */
class StockPriceFactory extends Factory
{
    public function definition(): array
    {
        $open = fake()->randomFloat(2, 1000, 10000);
        $close = $open * fake()->randomFloat(4, 0.95, 1.05);
        $high = max($open, $close) * fake()->randomFloat(4, 1, 1.02);
        $low = min($open, $close) * fake()->randomFloat(4, 0.98, 1);

        return [
            'stock_id' => Stock::factory(),
            'trading_date' => fake()->unique()->dateTimeBetween('-6 months', 'now'),
            'open' => $open,
            'high' => $high,
            'low' => $low,
            'close' => $close,
            'volume' => fake()->numberBetween(100_000, 50_000_000),
            'source' => 'factory:test',
        ];
    }
}
