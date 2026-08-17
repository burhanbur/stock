<?php

namespace Tests\Feature\Learning;

use App\Models\LearningLesson;
use App\Models\LearningModule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('learning.index'))->assertRedirect(route('login'));
    }

    public function test_it_shows_modules_with_zero_progress_for_a_new_user(): void
    {
        $moduleOne = LearningModule::factory()->create(['order' => 1, 'slug' => 'module-one']);
        LearningLesson::factory()->count(2)->for($moduleOne, 'module')->sequence(['order' => 1], ['order' => 2])->create();

        $moduleTwo = LearningModule::factory()->create(['order' => 2, 'slug' => 'module-two']);
        LearningLesson::factory()->for($moduleTwo, 'module')->create(['order' => 1]);

        $this->actingAs(User::factory()->create())
            ->get(route('learning.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Learning/Index')
                ->where('overall_percent', 0)
                ->where('total_lessons', 3)
                ->where('completed_lessons', 0)
                ->where('modules.0.is_locked', false)
                ->where('modules.1.is_locked', true)
                ->where('continue_lesson.module_slug', 'module-one')
            );
    }

    public function test_module_unlocks_once_previous_module_is_fully_completed(): void
    {
        $user = User::factory()->create();

        $moduleOne = LearningModule::factory()->create(['order' => 1, 'slug' => 'module-one']);
        $lesson = LearningLesson::factory()->for($moduleOne, 'module')->create(['order' => 1]);

        $moduleTwo = LearningModule::factory()->create(['order' => 2, 'slug' => 'module-two']);
        LearningLesson::factory()->for($moduleTwo, 'module')->create(['order' => 1]);

        $user->learningProgress()->create([
            'lesson_id' => $lesson->id,
            'status' => 'completed',
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('learning.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('modules.0.percent', 100)
                ->where('modules.1.is_locked', false)
            );
    }
}
