<?php

namespace Database\Factories;

use App\Models\LearningLesson;
use App\Models\LearningQuiz;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningQuiz>
 */
class LearningQuizFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lesson_id' => LearningLesson::factory(),
            'title' => 'Quiz: '.fake()->sentence(3),
        ];
    }
}
