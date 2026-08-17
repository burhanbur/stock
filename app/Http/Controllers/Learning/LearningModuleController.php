<?php

namespace App\Http\Controllers\Learning;

use App\Actions\Learning\GetModuleDetailAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearningModuleController extends Controller
{
    public function show(string $module, Request $request, GetModuleDetailAction $action): Response
    {
        $detail = $action->execute($module, $request->user());

        return Inertia::render('Learning/Module', [
            'module' => [
                'slug' => $detail['module']->slug,
                'order' => $detail['module']->order,
                'level' => $detail['module']->level->value,
                'level_label' => $detail['module']->level->label(),
                'title' => $detail['module']->title,
                'description' => $detail['module']->description,
            ],
            'is_locked' => $detail['is_locked'],
            'lessons' => collect($detail['lessons'])->map(fn ($entry) => [
                'slug' => $entry['lesson']->slug,
                'order' => $entry['lesson']->order,
                'title' => $entry['lesson']->title,
                'estimated_minutes' => $entry['lesson']->estimated_minutes,
                'is_completed' => $entry['is_completed'],
                'is_locked' => $entry['is_locked'],
            ])->values(),
        ]);
    }
}
