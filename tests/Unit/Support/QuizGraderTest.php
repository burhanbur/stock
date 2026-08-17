<?php

namespace Tests\Unit\Support;

use App\Support\Learning\QuizGrader;
use Tests\TestCase;

class QuizGraderTest extends TestCase
{
    public function test_it_scores_a_perfect_attempt(): void
    {
        $questions = [
            ['id' => 'q1', 'correct_option_id' => 'q1-a'],
            ['id' => 'q2', 'correct_option_id' => 'q2-b'],
        ];

        $result = QuizGrader::grade($questions, ['q1' => 'q1-a', 'q2' => 'q2-b']);

        $this->assertSame(100.0, $result['score']);
        $this->assertSame(2, $result['correct_answers']);
        $this->assertSame(2, $result['total_questions']);
        $this->assertTrue($result['results'][0]['is_correct']);
        $this->assertTrue($result['results'][1]['is_correct']);
    }

    public function test_it_scores_a_partially_correct_attempt(): void
    {
        $questions = [
            ['id' => 'q1', 'correct_option_id' => 'q1-a'],
            ['id' => 'q2', 'correct_option_id' => 'q2-b'],
            ['id' => 'q3', 'correct_option_id' => 'q3-c'],
        ];

        $result = QuizGrader::grade($questions, ['q1' => 'q1-a', 'q2' => 'q2-wrong', 'q3' => 'q3-c']);

        $this->assertSame(66.67, $result['score']);
        $this->assertSame(2, $result['correct_answers']);
        $this->assertFalse($result['results'][1]['is_correct']);
        $this->assertSame('q2-b', $result['results'][1]['correct_option_id']);
    }

    public function test_unanswered_questions_count_as_incorrect(): void
    {
        $questions = [
            ['id' => 'q1', 'correct_option_id' => 'q1-a'],
        ];

        $result = QuizGrader::grade($questions, []);

        $this->assertSame(0.0, $result['score']);
        $this->assertNull($result['results'][0]['selected_option_id']);
        $this->assertFalse($result['results'][0]['is_correct']);
    }

    public function test_it_returns_zero_score_for_no_questions(): void
    {
        $result = QuizGrader::grade([], []);

        $this->assertSame(0.0, $result['score']);
        $this->assertSame(0, $result['total_questions']);
    }
}
