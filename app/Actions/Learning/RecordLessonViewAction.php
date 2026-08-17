<?php

namespace App\Actions\Learning;

use App\Enums\Learning\ProgressStatus;
use App\Models\LearningLesson;
use App\Models\LearningProgress;
use App\Models\User;

class RecordLessonViewAction
{
    /**
     * Upserts a `learning_progress` row the first time a user opens a
     * lesson. Never downgrades an already-completed lesson back to
     * in_progress.
     */
    public function execute(User $user, LearningLesson $lesson): LearningProgress
    {
        $progress = LearningProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        if ($progress) {
            return $progress;
        }

        return LearningProgress::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => ProgressStatus::InProgress,
            'started_at' => now(),
        ]);
    }
}
