<?php

namespace Database\Factories;

use App\Enums\Learning\ModuleLevel;
use App\Models\LearningModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearningModule>
 */
class LearningModuleFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'order' => fake()->unique()->numberBetween(1, 100),
            'slug' => str($title)->slug(),
            'level' => fake()->randomElement(ModuleLevel::cases()),
            'title' => $title,
            'description' => fake()->sentence(),
        ];
    }
}
