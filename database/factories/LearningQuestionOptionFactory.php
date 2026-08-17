<?php

namespace Database\Factories;

use App\Models\LearningQuestion;
use App\Models\LearningQuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningQuestionOption>
 */
class LearningQuestionOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question_id' => LearningQuestion::factory(),
            'order' => fake()->numberBetween(1, 4),
            'text' => fake()->words(3, true),
            'is_correct' => false,
        ];
    }
}
