<?php

namespace Database\Seeders;

use App\Models\LearningModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningModuleSeeder extends Seeder
{
    /**
     * Only Module 01 is seeded for now — see ai/learning-module.md for why
     * the other 12 modules from the curriculum aren't implemented yet.
     */
    public function run(): void
    {
        DB::table('learning_modules')->delete();

        LearningModule::create([
            'order' => 1,
            'slug' => 'dasar-dasar-saham',
            'level' => 'beginner',
            'title' => 'Dasar-Dasar Saham',
            'description' => 'Mulai dari nol: apa itu perusahaan, kepemilikan, saham, dan bagaimana investor bisa untung maupun rugi.',
        ]);
    }
}
