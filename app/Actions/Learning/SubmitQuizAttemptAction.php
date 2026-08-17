<?php

namespace App\Actions\Learning;

use App\Models\LearningQuiz;
use App\Models\LearningQuizAttempt;
use App\Models\User;
use App\Support\Learning\QuizGrader;

class SubmitQuizAttemptAction
{
    public function __construct(private readonly MarkLessonCompleteAction $markLessonComplete) {}

    /**
     * @param  array<string, string>  $answers  question_id => selected_option_id
     * @return array{attempt: LearningQuizAttempt, results: array}
     */
    public function execute(User $user, LearningQuiz $quiz, array $answers): array
    {
        $quiz->loadMissing('questions.options', 'lesson');

        $questionsForGrading = $quiz->questions
            ->map(fn ($question) => [
                'id' => $question->id,
                'correct_option_id' => $question->options->firstWhere('is_correct', true)?->id,
            ])
            ->all();

        $graded = QuizGrader::grade($questionsForGrading, $answers);

        $attempt = LearningQuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'total_questions' => $graded['total_questions'],
            'correct_answers' => $graded['correct_answers'],
            'score' => $graded['score'],
            'answers' => $answers,
            'attempted_at' => now(),
        ]);

        if ($quiz->lesson) {
            $this->markLessonComplete->execute($user, $quiz->lesson);
        }

        return [
            'attempt' => $attempt,
            'results' => $graded['results'],
        ];
    }
}
