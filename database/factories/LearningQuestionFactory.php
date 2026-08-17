<?php

namespace Database\Factories;

use App\Enums\Learning\QuestionType;
use App\Models\LearningQuestion;
use App\Models\LearningQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningQuestion>
 */
class LearningQuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quiz_id' => LearningQuiz::factory(),
            'order' => fake()->numberBetween(1, 10),
            'type' => QuestionType::MultipleChoice,
            'question' => fake()->sentence().'?',
            'explanation' => fake()->sentence(),
            'difficulty' => fake()->randomElement(['easy', 'medium', 'hard']),
        ];
    }
}
