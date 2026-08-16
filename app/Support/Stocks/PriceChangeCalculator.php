<?php

namespace App\Support\Stocks;

class PriceChangeCalculator
{
    /**
     * Compute the absolute and percentage change between two closing
     * prices. Pure, deterministic calculation kept separate from the
     * data layer so it can be unit tested with known input/output pairs.
     *
     * @return array{change: float, change_percent: float}
     */
    public static function calculate(float $current, ?float $previous): array
    {
        if ($previous === null || $previous == 0.0) {
            return ['change' => 0.0, 'change_percent' => 0.0];
        }

        $change = round($current - $previous, 2);
        $changePercent = round(($change / $previous) * 100, 2);

        return ['change' => $change, 'change_percent' => $changePercent];
    }
}
