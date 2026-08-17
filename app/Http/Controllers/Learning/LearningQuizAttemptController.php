<?php

namespace App\Http\Controllers\Learning;

use App\Actions\Learning\SubmitQuizAttemptAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Learning\SubmitQuizAttemptRequest;
use App\Models\LearningQuiz;
use Illuminate\Http\RedirectResponse;

class LearningQuizAttemptController extends Controller
{
    public function store(
        LearningQuiz $quiz,
        SubmitQuizAttemptRequest $request,
        SubmitQuizAttemptAction $action,
    ): RedirectResponse {
        $action->execute($request->user(), $quiz, $request->answers());

        return back();
    }
}
