<?php

namespace App\Support\Learning;

use App\Models\LearningModule;
use Illuminate\Support\Collection;

class ModuleLock
{
    /**
     * A module is locked until every lesson in every module before it (by
     * `order`) is completed. Modules/lessons are otherwise presented
     * linearly — see the note on migration 2026_04_02_100002 for why this
     * app doesn't model a full prerequisite graph yet.
     *
     * @param  Collection<int, LearningModule>  $modulesInOrder  Ordered by `order` asc, each with `lessons` eager-loaded.
     * @param  array<string, bool>  $completedLessonIds  lesson_id => true
     * @return array<string, bool> module_id => is_locked
     */
    public static function compute(Collection $modulesInOrder, array $completedLessonIds): array
    {
        $locked = [];
        $previousModuleComplete = true;

        foreach ($modulesInOrder as $module) {
            $locked[$module->id] = ! $previousModuleComplete;

            $total = $module->lessons->count();
            $completed = $module->lessons->filter(fn ($lesson) => isset($completedLessonIds[$lesson->id]))->count();
            $previousModuleComplete = $total > 0 && $completed === $total;
        }

        return $locked;
    }
}
