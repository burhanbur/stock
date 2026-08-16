<?php

namespace Tests\Unit\Support;

use App\Support\Stocks\PriceChangeCalculator;
use Tests\TestCase;

class PriceChangeCalculatorTest extends TestCase
{
    public function test_it_calculates_a_positive_change(): void
    {
        $result = PriceChangeCalculator::calculate(current: 10_100.0, previous: 10_000.0);

        $this->assertSame(100.0, $result['change']);
        $this->assertSame(1.0, $result['change_percent']);
    }

    public function test_it_calculates_a_negative_change(): void
    {
        $result = PriceChangeCalculator::calculate(current: 9_800.0, previous: 10_000.0);

        $this->assertSame(-200.0, $result['change']);
        $this->assertSame(-2.0, $result['change_percent']);
    }

    public function test_it_rounds_to_two_decimal_places(): void
    {
        $result = PriceChangeCalculator::calculate(current: 10_033.333, previous: 10_000.0);

        $this->assertSame(33.33, $result['change']);
        $this->assertSame(0.33, $result['change_percent']);
    }

    public function test_it_returns_zero_when_there_is_no_previous_price(): void
    {
        $result = PriceChangeCalculator::calculate(current: 10_000.0, previous: null);

        $this->assertSame(0.0, $result['change']);
        $this->assertSame(0.0, $result['change_percent']);
    }

    public function test_it_returns_zero_when_previous_price_is_zero(): void
    {
        $result = PriceChangeCalculator::calculate(current: 100.0, previous: 0.0);

        $this->assertSame(0.0, $result['change']);
        $this->assertSame(0.0, $result['change_percent']);
    }
}
