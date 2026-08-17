<?php

namespace App\Actions\Learning;

use App\Models\LearningLesson;
use App\Models\LearningQuizAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetLessonDetailAction
{
    public function __construct(private readonly GetModuleDetailAction $getModuleDetail) {}

    /**
     * @return array{
     *     lesson: LearningLesson,
     *     is_completed: bool,
     *     is_locked: bool,
     *     previous_lesson: ?LearningLesson,
     *     next_lesson: ?LearningLesson,
     *     latest_attempt: ?LearningQuizAttempt,
     * }
     */
    public function execute(string $moduleSlug, string $lessonSlug, User $user): array
    {
        $moduleDetail = $this->getModuleDetail->execute($moduleSlug, $user);

        $index = collect($moduleDetail['lessons'])->search(fn ($entry) => $entry['lesson']->slug === $lessonSlug);

        if ($index === false) {
            throw new ModelNotFoundException("Lesson [{$lessonSlug}] not found in module [{$moduleSlug}].");
        }

        $entries = $moduleDetail['lessons'];
        $current = $entries[$index];

        /** @var LearningLesson $lesson */
        $lesson = $current['lesson'];
        $lesson->load(['module', 'quiz.questions.options']);

        $latestAttempt = null;

        if ($lesson->quiz) {
            $latestAttempt = LearningQuizAttempt::query()
                ->where('user_id', $user->id)
                ->where('quiz_id', $lesson->quiz->id)
                ->latest('attempted_at')
                ->first();
        }

        return [
            'lesson' => $lesson,
            'is_completed' => $current['is_completed'],
            'is_locked' => $current['is_locked'],
            'previous_lesson' => $index > 0 ? $entries[$index - 1]['lesson'] : null,
            'next_lesson' => isset($entries[$index + 1]) ? $entries[$index + 1]['lesson'] : null,
            'latest_attempt' => $latestAttempt,
        ];
    }
}
