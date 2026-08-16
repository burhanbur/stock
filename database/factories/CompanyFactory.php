<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Sector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sector_id' => Sector::factory(),
            'name' => fake()->company().' Tbk',
            'short_name' => fake()->companySuffix(),
        ];
    }
}
