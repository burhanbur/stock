<?php

namespace App\Actions\Learning;

use App\Enums\Learning\ProgressStatus;
use App\Models\LearningLesson;
use App\Models\LearningModule;
use App\Models\User;
use App\Support\Learning\ModuleLock;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class GetModuleDetailAction
{
    /**
     * @return array{
     *     module: LearningModule,
     *     is_locked: bool,
     *     lessons: array<int, array{lesson: LearningLesson, is_completed: bool, is_locked: bool}>,
     * }
     */
    public function execute(string $moduleSlug, User $user): array
    {
        $modules = LearningModule::query()->with('lessons')->orderBy('order')->get();
        $module = $modules->firstWhere('slug', $moduleSlug);

        if (! $module) {
            throw new ModelNotFoundException("Learning module [{$moduleSlug}] not found.");
        }

        $completedLessonIds = array_flip(
            $user->learningProgress()->where('status', ProgressStatus::Completed)->pluck('lesson_id')->all()
        );
        $lockedByModuleId = ModuleLock::compute($modules, $completedLessonIds);
        $isModuleLocked = $lockedByModuleId[$module->id];

        $lessons = [];
        $previousLessonComplete = true;

        foreach ($module->lessons as $lesson) {
            $isCompleted = isset($completedLessonIds[$lesson->id]);

            $lessons[] = [
                'lesson' => $lesson,
                'is_completed' => $isCompleted,
                'is_locked' => $isModuleLocked || ! $previousLessonComplete,
            ];

            $previousLessonComplete = $isCompleted;
        }

        return [
            'module' => $module,
            'is_locked' => $isModuleLocked,
            'lessons' => $lessons,
        ];
    }
}
