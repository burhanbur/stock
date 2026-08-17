<?php

namespace App\Actions\Learning;

use App\Enums\Learning\ProgressStatus;
use App\Models\LearningLesson;
use App\Models\LearningProgress;
use App\Models\User;

class MarkLessonCompleteAction
{
    public function execute(User $user, LearningLesson $lesson): LearningProgress
    {
        $progress = LearningProgress::query()->firstOrNew(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['started_at' => now()],
        );

        $progress->status = ProgressStatus::Completed;
        $progress->completed_at ??= now();
        $progress->save();

        return $progress;
    }
}
