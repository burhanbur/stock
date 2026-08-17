<?php

namespace Database\Factories;

use App\Models\LearningGlossaryTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningGlossaryTerm>
 */
class LearningGlossaryTermFactory extends Factory
{
    public function definition(): array
    {
        $term = fake()->unique()->word();

        return [
            'slug' => $term,
            'term' => strtoupper($term),
            'full_name' => fake()->words(2, true),
            'simple_definition' => fake()->sentence(),
            'formal_definition' => fake()->sentence(),
            'example' => fake()->sentence(),
            'application_usage' => fake()->sentence(),
            'related_term_slugs' => [],
        ];
    }
}
