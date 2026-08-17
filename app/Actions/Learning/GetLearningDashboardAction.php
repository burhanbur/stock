<?php

namespace App\Actions\Learning;

use App\Enums\Learning\ProgressStatus;
use App\Models\LearningModule;
use App\Models\LearningQuizAttempt;
use App\Models\User;
use App\Support\Learning\ModuleLock;

class GetLearningDashboardAction
{
    /**
     * @return array{
     *     modules: array<int, array{module: LearningModule, total_lessons: int, completed_lessons: int, percent: float, is_locked: bool}>,
     *     overall_percent: float,
     *     total_lessons: int,
     *     completed_lessons: int,
     *     quiz_average: ?float,
     *     continue_lesson: ?array{module_slug: string, lesson_slug: string, lesson_title: string},
     * }
     */
    public function execute(User $user): array
    {
        $modules = LearningModule::query()->with('lessons')->orderBy('order')->get();
        $completedLessonIds = array_flip(
            $user->learningProgress()->where('status', ProgressStatus::Completed)->pluck('lesson_id')->all()
        );
        $lockedByModuleId = ModuleLock::compute($modules, $completedLessonIds);

        $totalLessons = 0;
        $completedLessons = 0;
        $moduleSummaries = [];
        $continueLesson = null;

        foreach ($modules as $module) {
            $lessonCount = $module->lessons->count();
            $moduleCompletedCount = $module->lessons->filter(fn ($lesson) => isset($completedLessonIds[$lesson->id]))->count();
            $isLocked = $lockedByModuleId[$module->id];

            $totalLessons += $lessonCount;
            $completedLessons += $moduleCompletedCount;

            if ($continueLesson === null && ! $isLocked) {
                $nextLesson = $module->lessons->first(fn ($lesson) => ! isset($completedLessonIds[$lesson->id]));

                if ($nextLesson) {
                    $continueLesson = [
                        'module_slug' => $module->slug,
                        'lesson_slug' => $nextLesson->slug,
                        'lesson_title' => $nextLesson->title,
                    ];
                }
            }

            $moduleSummaries[] = [
                'module' => $module,
                'total_lessons' => $lessonCount,
                'completed_lessons' => $moduleCompletedCount,
                'percent' => $lessonCount > 0 ? round(($moduleCompletedCount / $lessonCount) * 100, 1) : 0.0,
                'is_locked' => $isLocked,
            ];
        }

        $quizAverage = LearningQuizAttempt::query()->where('user_id', $user->id)->avg('score');

        return [
            'modules' => $moduleSummaries,
            'overall_percent' => $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 1) : 0.0,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'quiz_average' => $quizAverage !== null ? round((float) $quizAverage, 1) : null,
            'continue_lesson' => $continueLesson,
        ];
    }
}
