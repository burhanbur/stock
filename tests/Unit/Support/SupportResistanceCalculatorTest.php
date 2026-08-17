<?php

namespace Tests\Unit\Support;

use App\Support\Stocks\SupportResistanceCalculator;
use Tests\TestCase;

class SupportResistanceCalculatorTest extends TestCase
{
    /** @param array<int, float> $values */
    private function flatOhlc(array $values): array
    {
        return array_map(fn (float $v) => ['high' => $v, 'low' => $v], $values);
    }

    public function test_it_returns_empty_levels_with_insufficient_data(): void
    {
        $prices = $this->flatOhlc(array_fill(0, 10, 100.0));

        $result = SupportResistanceCalculator::calculate($prices, currentPrice: 100.0);

        $this->assertSame([], $result['support']);
        $this->assertSame([], $result['resistance']);
    }

    public function test_it_finds_a_support_level_from_repeated_swing_lows(): void
    {
        // Three isolated dips to 100, each padded with enough flat 110s on
        // both sides to be confirmed as a swing low (window = 5) and to
        // keep each dip's window from overlapping the next.
        $values = array_merge(
            array_fill(0, 6, 110.0),
            [100.0],
            array_fill(0, 11, 110.0),
            [100.0],
            array_fill(0, 11, 110.0),
            [100.0],
            array_fill(0, 6, 110.0),
        );

        $result = SupportResistanceCalculator::calculate($this->flatOhlc($values), currentPrice: 105.0);

        $level = collect($result['support'])->firstWhere('level', 100.0);

        $this->assertNotNull($level);
        $this->assertSame(3, $level['touches']);
    }

    public function test_it_finds_a_resistance_level_from_repeated_swing_highs(): void
    {
        $values = array_merge(
            array_fill(0, 6, 90.0),
            [120.0],
            array_fill(0, 11, 90.0),
            [120.0],
            array_fill(0, 6, 90.0),
        );

        $result = SupportResistanceCalculator::calculate($this->flatOhlc($values), currentPrice: 100.0);

        $level = collect($result['resistance'])->firstWhere('level', 120.0);

        $this->assertNotNull($level);
        $this->assertSame(2, $level['touches']);
    }

    public function test_support_levels_stay_below_current_price_and_resistance_above(): void
    {
        $values = array_merge(
            array_fill(0, 6, 90.0),
            [80.0],
            array_fill(0, 11, 90.0),
            [120.0],
            array_fill(0, 6, 90.0),
        );

        $result = SupportResistanceCalculator::calculate($this->flatOhlc($values), currentPrice: 100.0);

        foreach ($result['support'] as $level) {
            $this->assertLessThan(100.0, $level['level']);
        }
        foreach ($result['resistance'] as $level) {
            $this->assertGreaterThan(100.0, $level['level']);
        }
    }
}
