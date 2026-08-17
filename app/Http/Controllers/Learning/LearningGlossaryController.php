<?php

namespace App\Http\Controllers\Learning;

use App\Actions\Learning\SearchGlossaryTermsAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Learning\LearningGlossaryTermResource;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearningGlossaryController extends Controller
{
    public function index(Request $request, SearchGlossaryTermsAction $action): Response
    {
        $search = $request->string('search')->trim()->value() ?: null;

        return Inertia::render('Learning/Glossary', [
            'terms' => LearningGlossaryTermResource::collection($action->execute($search))->resolve(),
            'search' => $search,
        ]);
    }
}
