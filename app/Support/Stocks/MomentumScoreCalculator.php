<?php

namespace App\Support\Stocks;

class MomentumScoreCalculator
{
    /**
     * SMA50 is the longest moving average used, so it sets the minimum
     * history required for a meaningful signal.
     */
    private const MIN_DATA_POINTS = 50;

    private const MOMENTUM_LOOKBACK_DAYS = 20;

    /** Deviation (%) from SMA50 that maxes out the trend signal. */
    private const TREND_CAP_PERCENT = 12.0;

    /** 20-day return (%) that maxes out the momentum signal. */
    private const MOMENTUM_CAP_PERCENT = 15.0;

    /**
     * Blends a trend signal (price vs. its 50-day average) with a raw
     * momentum signal (20-day return) into a single 0-100 score. Pure,
     * deterministic, and kept separate from the data layer so it can be
     * unit tested with known input/output pairs — same pattern as
     * PriceChangeCalculator.
     *
     * @param  array<int, float>  $closes  Closing prices, ascending by trading date.
     * @return array{score: int|null, label: string, sma20: float|null, sma50: float|null, momentum_percent: float|null}
     */
    public static function calculate(array $closes): array
    {
        $count = count($closes);

        if ($count < self::MIN_DATA_POINTS) {
            return ['score' => null, 'label' => 'Data belum cukup', 'sma20' => null, 'sma50' => null, 'momentum_percent' => null];
        }

        $latest = $closes[$count - 1];
        $sma20 = self::average(array_slice($closes, -20));
        $sma50 = self::average(array_slice($closes, -50));

        $momentumBase = $closes[$count - 1 - self::MOMENTUM_LOOKBACK_DAYS];
        $momentumPercent = $momentumBase != 0.0 ? (($latest - $momentumBase) / $momentumBase) * 100 : 0.0;

        $trendDeviationPercent = $sma50 != 0.0 ? (($latest - $sma50) / $sma50) * 100 : 0.0;
        $trendSignal = self::clamp($trendDeviationPercent / self::TREND_CAP_PERCENT, -1.0, 1.0);
        $momentumSignal = self::clamp($momentumPercent / self::MOMENTUM_CAP_PERCENT, -1.0, 1.0);

        $combined = ($trendSignal + $momentumSignal) / 2;
        $score = (int) round((($combined + 1) / 2) * 100);

        return [
            'score' => $score,
            'label' => self::label($score),
            'sma20' => round($sma20, 2),
            'sma50' => round($sma50, 2),
            'momentum_percent' => round($momentumPercent, 2),
        ];
    }

    public static function label(int $score): string
    {
        return match (true) {
            $score >= 65 => 'Beli',
            $score <= 35 => 'Jual',
            default => 'Tahan',
        };
    }

    /** @param array<int, float> $values */
    private static function average(array $values): float
    {
        return array_sum($values) / count($values);
    }

    private static function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
