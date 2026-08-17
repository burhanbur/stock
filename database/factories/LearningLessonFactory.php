<?php

namespace Database\Factories;

use App\Models\LearningLesson;
use App\Models\LearningModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningLesson>
 */
class LearningLessonFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'module_id' => LearningModule::factory(),
            'order' => fake()->unique()->numberBetween(1, 1000),
            'slug' => str($title)->slug(),
            'title' => $title,
            'estimated_minutes' => fake()->numberBetween(5, 15),
            'learning_objectives' => [fake()->sentence(), fake()->sentence()],
            'key_terms' => [],
            'content' => "## {$title}\n\n".fake()->paragraphs(3, true),
            'summary' => fake()->sentence(),
        ];
    }
}
