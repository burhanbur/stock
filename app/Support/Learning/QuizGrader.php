<?php

namespace App\Support\Learning;

class QuizGrader
{
    /**
     * Grade a set of answers against the correct option for each question.
     * Pure, deterministic, and framework-agnostic on purpose — the caller
     * maps Eloquent models to these plain shapes first — so it can be unit
     * tested without touching the database.
     *
     * @param  array<int, array{id: string, correct_option_id: string}>  $questions
     * @param  array<string, string>  $answers  question_id => selected_option_id
     * @return array{
     *     score: float,
     *     correct_answers: int,
     *     total_questions: int,
     *     results: array<int, array{question_id: string, selected_option_id: ?string, correct_option_id: string, is_correct: bool}>
     * }
     */
    public static function grade(array $questions, array $answers): array
    {
        $correctCount = 0;
        $results = [];

        foreach ($questions as $question) {
            $selectedOptionId = $answers[$question['id']] ?? null;
            $isCorrect = $selectedOptionId !== null && $selectedOptionId === $question['correct_option_id'];

            if ($isCorrect) {
                $correctCount++;
            }

            $results[] = [
                'question_id' => $question['id'],
                'selected_option_id' => $selectedOptionId,
                'correct_option_id' => $question['correct_option_id'],
                'is_correct' => $isCorrect,
            ];
        }

        $total = count($questions);

        return [
            'score' => $total > 0 ? round(($correctCount / $total) * 100, 2) : 0.0,
            'correct_answers' => $correctCount,
            'total_questions' => $total,
            'results' => $results,
        ];
    }
}
