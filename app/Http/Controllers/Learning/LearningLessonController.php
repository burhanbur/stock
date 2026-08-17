<?php

namespace App\Http\Controllers\Learning;

use App\Actions\Learning\GetLessonDetailAction;
use App\Actions\Learning\MarkLessonCompleteAction;
use App\Actions\Learning\RecordLessonViewAction;
use App\Http\Controllers\Controller;
use App\Models\LearningLesson;
use App\Models\LearningQuizAttempt;
use App\Support\Learning\QuizGrader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearningLessonController extends Controller
{
    public function show(
        string $module,
        string $lesson,
        Request $request,
        GetLessonDetailAction $action,
        RecordLessonViewAction $recordView,
    ): Response {
        $detail = $action->execute($module, $lesson, $request->user());

        abort_if($detail['is_locked'], 403, 'Selesaikan pelajaran sebelumnya terlebih dahulu.');

        $recordView->execute($request->user(), $detail['lesson']);

        return Inertia::render('Learning/Lesson', [
            'module' => [
                'slug' => $module,
                'title' => $detail['lesson']->module->title,
                'level' => $detail['lesson']->module->level->value,
            ],
            'lesson' => [
                'slug' => $detail['lesson']->slug,
                'order' => $detail['lesson']->order,
                'title' => $detail['lesson']->title,
                'estimated_minutes' => $detail['lesson']->estimated_minutes,
                'learning_objectives' => $detail['lesson']->learning_objectives,
                'key_terms' => $detail['lesson']->key_terms ?? [],
                'content' => $detail['lesson']->content,
                'summary' => $detail['lesson']->summary,
            ],
            'is_completed' => $detail['is_completed'],
            'previous_lesson' => $detail['previous_lesson']?->only(['slug', 'title']),
            'next_lesson' => $detail['next_lesson']?->only(['slug', 'title']),
            'quiz' => $this->quizForDisplay($detail['lesson']),
            'latest_attempt' => $this->attemptForDisplay($detail['lesson'], $detail['latest_attempt']),
        ]);
    }

    public function complete(
        string $module,
        string $lesson,
        Request $request,
        GetLessonDetailAction $action,
        MarkLessonCompleteAction $markComplete,
    ): RedirectResponse {
        $detail = $action->execute($module, $lesson, $request->user());

        abort_if($detail['is_locked'], 403);

        $markComplete->execute($request->user(), $detail['lesson']);

        return back();
    }

    private function quizForDisplay(LearningLesson $lesson): ?array
    {
        if (! $lesson->quiz) {
            return null;
        }

        return [
            'id' => $lesson->quiz->id,
            'title' => $lesson->quiz->title,
            'questions' => $lesson->quiz->questions->map(fn ($question) => [
                'id' => $question->id,
                'type' => $question->type->value,
                'question' => $question->question,
                'options' => $question->options->map(fn ($option) => [
                    'id' => $option->id,
                    'text' => $option->text,
                ])->values(),
            ])->values(),
        ];
    }

    private function attemptForDisplay(LearningLesson $lesson, ?LearningQuizAttempt $attempt): ?array
    {
        if (! $attempt || ! $lesson->quiz) {
            return null;
        }

        $questionsForGrading = $lesson->quiz->questions
            ->map(fn ($question) => [
                'id' => $question->id,
                'correct_option_id' => $question->options->firstWhere('is_correct', true)?->id,
            ])
            ->all();

        $graded = QuizGrader::grade($questionsForGrading, $attempt->answers);
        $questionsById = $lesson->quiz->questions->keyBy('id');

        return [
            'score' => (float) $attempt->score,
            'correct_answers' => $attempt->correct_answers,
            'total_questions' => $attempt->total_questions,
            'attempted_at' => $attempt->attempted_at->toDateTimeString(),
            'results' => collect($graded['results'])->map(function (array $result) use ($questionsById) {
                $question = $questionsById->get($result['question_id']);

                return [
                    'question_id' => $result['question_id'],
                    'selected_option_id' => $result['selected_option_id'],
                    'correct_option_id' => $result['correct_option_id'],
                    'is_correct' => $result['is_correct'],
                    'explanation' => $question?->explanation,
                ];
            })->values(),
        ];
    }
}
