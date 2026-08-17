<?php

namespace App\Http\Resources\Learning;

use App\Models\LearningGlossaryTerm;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin LearningGlossaryTerm */
class LearningGlossaryTermResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'term' => $this->term,
            'full_name' => $this->full_name,
            'simple_definition' => $this->simple_definition,
            'formal_definition' => $this->formal_definition,
            'example' => $this->example,
            'application_usage' => $this->application_usage,
            'related_term_slugs' => $this->related_term_slugs ?? [],
        ];
    }
}
