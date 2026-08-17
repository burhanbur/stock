<?php

namespace Tests\Unit\Support;

use App\Support\Stocks\SignalBacktester;
use Tests\TestCase;

class SignalBacktesterTest extends TestCase
{
    public function test_it_returns_zero_counts_when_no_signal_ever_fires(): void
    {
        // A flat price keeps the momentum score pinned at 50 ("Tahan"),
        // so neither Beli nor Jual ever fires.
        $closes = array_fill(0, 80, 100.0);

        $result = SignalBacktester::run($closes);

        $this->assertSame(0, $result['beli']['count']);
        $this->assertNull($result['beli']['win_rate']);
        $this->assertNull($result['beli']['avg_return_percent']);
        $this->assertSame(0, $result['jual']['count']);
        $this->assertNull($result['jual']['win_rate']);
        $this->assertSame(10, $result['horizon_days']);
    }

    public function test_it_shows_a_high_win_rate_for_beli_signals_during_a_sustained_uptrend(): void
    {
        $closes = array_map(fn (int $i) => 100.0 + $i * 2, range(0, 89));

        $result = SignalBacktester::run($closes);

        $this->assertGreaterThan(0, $result['beli']['count']);
        $this->assertGreaterThanOrEqual(80.0, $result['beli']['win_rate']);
        $this->assertGreaterThan(0, $result['beli']['avg_return_percent']);
    }

    public function test_it_shows_a_high_win_rate_for_jual_signals_during_a_sustained_downtrend(): void
    {
        $closes = array_map(fn (int $i) => 300.0 - $i * 2, range(0, 89));

        $result = SignalBacktester::run($closes);

        $this->assertGreaterThan(0, $result['jual']['count']);
        $this->assertGreaterThanOrEqual(80.0, $result['jual']['win_rate']);
        $this->assertLessThan(0, $result['jual']['avg_return_percent']);
    }
}
