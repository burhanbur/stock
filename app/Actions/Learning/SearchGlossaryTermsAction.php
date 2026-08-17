<?php

namespace App\Actions\Learning;

use App\Models\LearningGlossaryTerm;
use Illuminate\Database\Eloquent\Collection;

class SearchGlossaryTermsAction
{
    public function execute(?string $search): Collection
    {
        return LearningGlossaryTerm::query()
            ->search($search)
            ->orderBy('term')
            ->get();
    }
}
