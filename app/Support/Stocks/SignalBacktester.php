<?php

namespace App\Support\Stocks;

class SignalBacktester
{
    /** How many trading days forward each signal's outcome is measured over. */
    public const HORIZON_DAYS = 10;

    /**
     * Walks the price history day by day, recomputing the recommendation
     * score using only data available up to that day — no lookahead — then
     * checks what the price actually did over the following HORIZON_DAYS
     * trading days. This validates the existing Momentum/Risk scoring
     * engine against its own history; it is not a forecast of future
     * prices. Pure and deterministic, unit tested the same way as
     * PriceChangeCalculator.
     *
     * @param  array<int, float>  $closes  Ascending by trading date.
     * @return array{beli: array{count: int, win_rate: float|null, avg_return_percent: float|null}, jual: array{count: int, win_rate: float|null, avg_return_percent: float|null}, horizon_days: int}
     */
    public static function run(array $closes): array
    {
        $count = count($closes);
        $returns = ['Beli' => [], 'Jual' => []];

        for ($i = 0; $i < $count - self::HORIZON_DAYS; $i++) {
            $windowCloses = array_slice($closes, 0, $i + 1);

            $momentum = MomentumScoreCalculator::calculate($windowCloses);
            $risk = RiskScoreCalculator::calculate($windowCloses);
            $overall = RecommendationScoreCalculator::combine($momentum['score'], $risk['score']);

            if (! isset($returns[$overall['label']])) {
                continue;
            }

            $entry = $closes[$i];
            $exit = $closes[$i + self::HORIZON_DAYS];
            $returnPercent = $entry != 0.0 ? (($exit - $entry) / $entry) * 100 : 0.0;

            $returns[$overall['label']][] = $returnPercent;
        }

        return [
            'beli' => self::summarize($returns['Beli'], isWin: fn (float $r) => $r > 0),
            'jual' => self::summarize($returns['Jual'], isWin: fn (float $r) => $r < 0),
            'horizon_days' => self::HORIZON_DAYS,
        ];
    }

    /**
     * @param  array<int, float>  $returnsPercent
     * @return array{count: int, win_rate: float|null, avg_return_percent: float|null}
     */
    private static function summarize(array $returnsPercent, callable $isWin): array
    {
        $count = count($returnsPercent);

        if ($count === 0) {
            return ['count' => 0, 'win_rate' => null, 'avg_return_percent' => null];
        }

        $wins = count(array_filter($returnsPercent, $isWin));

        return [
            'count' => $count,
            'win_rate' => round($wins / $count * 100, 1),
            'avg_return_percent' => round(array_sum($returnsPercent) / $count, 2),
        ];
    }
}
