<?php

namespace Tests\Unit\Support;

use App\Support\Stocks\MomentumScoreCalculator;
use Tests\TestCase;

class MomentumScoreCalculatorTest extends TestCase
{
    public function test_it_returns_insufficient_data_below_fifty_points(): void
    {
        $result = MomentumScoreCalculator::calculate(array_fill(0, 49, 100.0));

        $this->assertNull($result['score']);
        $this->assertSame('Data belum cukup', $result['label']);
    }

    public function test_it_scores_a_steady_uptrend_as_beli(): void
    {
        // 60 closes rising steadily from 100 to 159.
        $closes = array_map(fn (int $i) => 100.0 + $i, range(0, 59));

        $result = MomentumScoreCalculator::calculate($closes);

        $this->assertGreaterThanOrEqual(65, $result['score']);
        $this->assertSame('Beli', $result['label']);
    }

    public function test_it_scores_a_steady_downtrend_as_jual(): void
    {
        // 60 closes falling steadily from 159 to 100.
        $closes = array_map(fn (int $i) => 159.0 - $i, range(0, 59));

        $result = MomentumScoreCalculator::calculate($closes);

        $this->assertLessThanOrEqual(35, $result['score']);
        $this->assertSame('Jual', $result['label']);
    }

    public function test_it_scores_a_flat_price_as_tahan(): void
    {
        $closes = array_fill(0, 60, 100.0);

        $result = MomentumScoreCalculator::calculate($closes);

        $this->assertSame(50, $result['score']);
        $this->assertSame('Tahan', $result['label']);
        $this->assertSame(0.0, $result['momentum_percent']);
    }

    public function test_it_computes_moving_averages(): void
    {
        $closes = array_fill(0, 60, 100.0);

        $result = MomentumScoreCalculator::calculate($closes);

        $this->assertSame(100.0, $result['sma20']);
        $this->assertSame(100.0, $result['sma50']);
    }
}
