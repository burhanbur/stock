<?php

namespace Tests\Unit\Support;

use App\Support\Stocks\RiskScoreCalculator;
use Tests\TestCase;

class RiskScoreCalculatorTest extends TestCase
{
    public function test_it_returns_insufficient_data_below_thirty_one_points(): void
    {
        $result = RiskScoreCalculator::calculate(array_fill(0, 30, 100.0));

        $this->assertNull($result['score']);
        $this->assertSame('Data belum cukup', $result['label']);
    }

    public function test_it_scores_low_daily_swings_as_low_risk(): void
    {
        // 30 daily returns alternating +/-0.5% -> ~7.94% annualized volatility.
        $closes = $this->alternatingReturns(0.005);

        $result = RiskScoreCalculator::calculate($closes);

        $this->assertSame(100, $result['score']);
        $this->assertSame('Risiko Rendah', $result['label']);
        $this->assertSame(7.94, $result['annualized_volatility_percent']);
    }

    public function test_it_scores_large_daily_swings_as_high_risk(): void
    {
        // 30 daily returns alternating +/-5% -> ~79.37% annualized volatility.
        $closes = $this->alternatingReturns(0.05);

        $result = RiskScoreCalculator::calculate($closes);

        $this->assertSame(0, $result['score']);
        $this->assertSame('Risiko Tinggi', $result['label']);
        $this->assertSame(79.37, $result['annualized_volatility_percent']);
    }

    /**
     * Builds 31 closes whose 30 daily returns alternate exactly between
     * +$amplitude and -$amplitude, so the mean return is exactly zero and
     * the variance is exactly $amplitude^2 — a clean, hand-checkable input.
     *
     * @return array<int, float>
     */
    private function alternatingReturns(float $amplitude): array
    {
        $closes = [100.0];

        foreach (range(1, 30) as $i) {
            $factor = $i % 2 === 1 ? 1 + $amplitude : 1 - $amplitude;
            $closes[] = end($closes) * $factor;
        }

        return $closes;
    }
}
