<?php

namespace Tests\Unit\Support;

use App\Support\Stocks\RecommendationScoreCalculator;
use Tests\TestCase;

class RecommendationScoreCalculatorTest extends TestCase
{
    public function test_it_returns_insufficient_data_when_momentum_is_missing(): void
    {
        $result = RecommendationScoreCalculator::combine(null, 80);

        $this->assertNull($result['score']);
        $this->assertSame('Data belum cukup', $result['label']);
    }

    public function test_it_returns_insufficient_data_when_risk_is_missing(): void
    {
        $result = RecommendationScoreCalculator::combine(80, null);

        $this->assertNull($result['score']);
        $this->assertSame('Data belum cukup', $result['label']);
    }

    public function test_it_weighs_momentum_more_than_risk(): void
    {
        // 90 * 0.7 + 10 * 0.3 = 63 + 3 = 66.
        $result = RecommendationScoreCalculator::combine(90, 10);

        $this->assertSame(66, $result['score']);
        $this->assertSame('Beli', $result['label']);
    }

    public function test_it_labels_a_low_combined_score_as_jual(): void
    {
        // 10 * 0.7 + 10 * 0.3 = 7 + 3 = 10.
        $result = RecommendationScoreCalculator::combine(10, 10);

        $this->assertSame(10, $result['score']);
        $this->assertSame('Jual', $result['label']);
    }

    public function test_it_labels_a_mid_combined_score_as_tahan(): void
    {
        // 50 * 0.7 + 50 * 0.3 = 35 + 15 = 50.
        $result = RecommendationScoreCalculator::combine(50, 50);

        $this->assertSame(50, $result['score']);
        $this->assertSame('Tahan', $result['label']);
    }

    public function test_low_risk_alone_cannot_turn_neutral_momentum_into_beli(): void
    {
        // 50 * 0.7 + 100 * 0.3 = 35 + 30 = 65 -> would cross the Beli
        // threshold on the blended score alone, but momentum itself is
        // neutral (Tahan), so the overall label must stay Tahan.
        $result = RecommendationScoreCalculator::combine(50, 100);

        $this->assertSame(65, $result['score']);
        $this->assertSame('Tahan', $result['label']);
    }

    public function test_high_risk_can_temper_a_beli_momentum_down_to_tahan(): void
    {
        // 70 * 0.7 + 0 * 0.3 = 49 + 0 = 49 -> momentum alone says Beli, but
        // the blended score lands in the neutral band once risk is heavy.
        $result = RecommendationScoreCalculator::combine(70, 0);

        $this->assertSame(49, $result['score']);
        $this->assertSame('Tahan', $result['label']);
    }
}
