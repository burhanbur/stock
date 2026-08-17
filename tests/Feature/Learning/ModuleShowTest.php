<?php

namespace Tests\Feature\Learning;

use App\Models\LearningLesson;
use App\Models\LearningModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ModuleShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_lesson_is_unlocked_and_second_is_locked(): void
    {
        $module = LearningModule::factory()->create(['order' => 1, 'slug' => 'module-one']);
        LearningLesson::factory()->for($module, 'module')->create(['order' => 1, 'slug' => 'lesson-one']);
        LearningLesson::factory()->for($module, 'module')->create(['order' => 2, 'slug' => 'lesson-two']);

        $this->actingAs(User::factory()->create())
            ->get(route('learning.modules.show', 'module-one'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Learning/Module')
                ->where('is_locked', false)
                ->where('lessons.0.slug', 'lesson-one')
                ->where('lessons.0.is_locked', false)
                ->where('lessons.1.slug', 'lesson-two')
                ->where('lessons.1.is_locked', true)
            );
    }

    public function test_it_returns_404_for_an_unknown_module(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('learning.modules.show', 'nope'))
            ->assertNotFound();
    }
}
