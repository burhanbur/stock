<?php

namespace App\Http\Controllers\Learning;

use App\Actions\Learning\GetLearningDashboardAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearningDashboardController extends Controller
{
    public function index(Request $request, GetLearningDashboardAction $action): Response
    {
        $dashboard = $action->execute($request->user());

        return Inertia::render('Learning/Index', [
            'modules' => collect($dashboard['modules'])->map(fn ($summary) => [
                'id' => $summary['module']->id,
                'slug' => $summary['module']->slug,
                'order' => $summary['module']->order,
                'level' => $summary['module']->level->value,
                'level_label' => $summary['module']->level->label(),
                'title' => $summary['module']->title,
                'description' => $summary['module']->description,
                'total_lessons' => $summary['total_lessons'],
                'completed_lessons' => $summary['completed_lessons'],
                'percent' => $summary['percent'],
                'is_locked' => $summary['is_locked'],
            ])->values(),
            'overall_percent' => $dashboard['overall_percent'],
            'total_lessons' => $dashboard['total_lessons'],
            'completed_lessons' => $dashboard['completed_lessons'],
            'quiz_average' => $dashboard['quiz_average'],
            'continue_lesson' => $dashboard['continue_lesson'],
        ]);
    }
}
