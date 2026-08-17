<?php

namespace App\Support\Stocks;

class SupportResistanceCalculator
{
    /** Trading days on each side needed to confirm a swing high/low. */
    private const SWING_WINDOW = 5;

    /** Nearby swing points within this % of each other are merged into one level. */
    private const CLUSTER_TOLERANCE_PERCENT = 1.5;

    private const MAX_LEVELS = 3;

    private const MIN_DATA_POINTS = self::SWING_WINDOW * 2 + 1;

    /**
     * Finds historically significant price zones from swing highs/lows —
     * points that were the local extreme within a +/-5 trading day window —
     * then clusters nearby swings into a handful of levels, ranked by how
     * many times price touched that zone. This describes where price has
     * repeatedly reacted in the past; it does not predict where it will go.
     *
     * @param  array<int, array{high: float, low: float}>  $prices  Ascending by trading date.
     * @return array{support: array<int, array{level: float, touches: int}>, resistance: array<int, array{level: float, touches: int}>}
     */
    public static function calculate(array $prices, float $currentPrice): array
    {
        if (count($prices) < self::MIN_DATA_POINTS) {
            return ['support' => [], 'resistance' => []];
        }

        $highs = array_column($prices, 'high');
        $lows = array_column($prices, 'low');

        $swingHighs = self::findSwingPoints($highs, high: true);
        $swingLows = self::findSwingPoints($lows, high: false);

        $resistanceLevels = array_values(array_filter(
            self::clusterLevels($swingHighs),
            fn (array $l) => $l['level'] > $currentPrice
        ));
        $supportLevels = array_values(array_filter(
            self::clusterLevels($swingLows),
            fn (array $l) => $l['level'] < $currentPrice
        ));

        usort($resistanceLevels, fn ($a, $b) => $b['touches'] <=> $a['touches']);
        usort($supportLevels, fn ($a, $b) => $b['touches'] <=> $a['touches']);

        return [
            'support' => array_slice($supportLevels, 0, self::MAX_LEVELS),
            'resistance' => array_slice($resistanceLevels, 0, self::MAX_LEVELS),
        ];
    }

    /**
     * @param  array<int, float>  $values
     * @return array<int, float>
     */
    private static function findSwingPoints(array $values, bool $high): array
    {
        $points = [];
        $count = count($values);

        for ($i = self::SWING_WINDOW; $i < $count - self::SWING_WINDOW; $i++) {
            $window = array_slice($values, $i - self::SWING_WINDOW, self::SWING_WINDOW * 2 + 1);
            $extreme = $high ? max($window) : min($window);

            if ($values[$i] === $extreme) {
                $points[] = $values[$i];
            }
        }

        return $points;
    }

    /**
     * @param  array<int, float>  $values
     * @return array<int, array{level: float, touches: int}>
     */
    private static function clusterLevels(array $values): array
    {
        sort($values);

        $clusters = [];

        foreach ($values as $value) {
            $merged = false;

            foreach ($clusters as &$cluster) {
                $average = array_sum($cluster) / count($cluster);

                if ($average != 0.0 && abs($value - $average) / $average * 100 <= self::CLUSTER_TOLERANCE_PERCENT) {
                    $cluster[] = $value;
                    $merged = true;
                    break;
                }
            }
            unset($cluster);

            if (! $merged) {
                $clusters[] = [$value];
            }
        }

        return array_map(fn (array $c) => [
            'level' => round(array_sum($c) / count($c), 2),
            'touches' => count($c),
        ], $clusters);
    }
}
