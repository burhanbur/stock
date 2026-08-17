<?php

namespace App\Support\Stocks;

class RiskScoreCalculator
{
    /** Need at least this many closes to derive a meaningful sample of daily returns. */
    private const MIN_DATA_POINTS = 31;

    private const TRADING_DAYS_PER_YEAR = 252;

    /** Annualized volatility (%) at or below this maps to a 100 (safest) score. */
    private const LOW_VOLATILITY_PERCENT = 15.0;

    /** Annualized volatility (%) at or above this maps to a 0 (riskiest) score. */
    private const HIGH_VOLATILITY_PERCENT = 60.0;

    /**
     * Converts historical price volatility (standard deviation of daily
     * returns, annualized) into a 0-100 score where a higher score means
     * lower risk. Pure, deterministic, unit tested with known input/output
     * pairs — same pattern as PriceChangeCalculator.
     *
     * @param  array<int, float>  $closes  Closing prices, ascending by trading date.
     * @return array{score: int|null, label: string, annualized_volatility_percent: float|null}
     */
    public static function calculate(array $closes): array
    {
        if (count($closes) < self::MIN_DATA_POINTS) {
            return ['score' => null, 'label' => 'Data belum cukup', 'annualized_volatility_percent' => null];
        }

        $returns = self::dailyReturns($closes);
        $mean = array_sum($returns) / count($returns);
        $variance = array_sum(array_map(fn (float $r) => ($r - $mean) ** 2, $returns)) / count($returns);
        $annualizedVolatilityPercent = sqrt($variance) * sqrt(self::TRADING_DAYS_PER_YEAR) * 100;

        $normalized = ($annualizedVolatilityPercent - self::LOW_VOLATILITY_PERCENT)
            / (self::HIGH_VOLATILITY_PERCENT - self::LOW_VOLATILITY_PERCENT);
        $score = (int) round((1 - self::clamp($normalized, 0.0, 1.0)) * 100);

        return [
            'score' => $score,
            'label' => self::label($score),
            'annualized_volatility_percent' => round($annualizedVolatilityPercent, 2),
        ];
    }

    public static function label(int $score): string
    {
        return match (true) {
            $score >= 65 => 'Risiko Rendah',
            $score <= 35 => 'Risiko Tinggi',
            default => 'Risiko Sedang',
        };
    }

    /**
     * @param  array<int, float>  $closes
     * @return array<int, float>
     */
    private static function dailyReturns(array $closes): array
    {
        $returns = [];

        for ($i = 1, $count = count($closes); $i < $count; $i++) {
            if ($closes[$i - 1] == 0.0) {
                continue;
            }

            $returns[] = ($closes[$i] - $closes[$i - 1]) / $closes[$i - 1];
        }

        return $returns;
    }

    private static function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
