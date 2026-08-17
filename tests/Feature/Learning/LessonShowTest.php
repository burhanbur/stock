<?php

namespace Tests\Feature\Learning;

use App\Models\LearningLesson;
use App\Models\LearningModule;
use App\Models\LearningQuestion;
use App\Models\LearningQuestionOption;
use App\Models\LearningQuiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LessonShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_lesson_content_and_quiz_without_leaking_correct_answers(): void
    {
        $module = LearningModule::factory()->create(['order' => 1, 'slug' => 'module-one']);
        $lesson = LearningLesson::factory()->for($module, 'module')->create([
            'order' => 1,
            'slug' => 'lesson-one',
            'title' => 'Apa Itu Saham?',
        ]);
        $quiz = LearningQuiz::factory()->for($lesson, 'lesson')->create();
        $question = LearningQuestion::factory()->for($quiz, 'quiz')->create(['order' => 1]);
        LearningQuestionOption::factory()->for($question, 'question')->create(['order' => 1, 'is_correct' => true]);
        LearningQuestionOption::factory()->for($question, 'question')->create(['order' => 2, 'is_correct' => false]);

        $response = $this->actingAs(User::factory()->create())
            ->get(route('learning.lessons.show', ['module-one', 'lesson-one']))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Learning/Lesson')
                ->where('lesson.title', 'Apa Itu Saham?')
                ->where('is_completed', false)
                ->has('quiz.questions', 1)
                ->has('quiz.questions.0.options', 2)
            );

        $page = $response->viewData('page')['props'];
        foreach ($page['quiz']['questions'][0]['options'] as $option) {
            $this->assertArrayNotHasKey('is_correct', $option);
        }
    }

    public function test_viewing_a_lesson_records_progress(): void
    {
        $module = LearningModule::factory()->create(['order' => 1, 'slug' => 'module-one']);
        $lesson = LearningLesson::factory()->for($module, 'module')->create(['order' => 1, 'slug' => 'lesson-one']);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('learning.lessons.show', ['module-one', 'lesson-one']));

        $this->assertDatabaseHas('learning_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_a_locked_lesson_returns_403(): void
    {
        $module = LearningModule::factory()->create(['order' => 1, 'slug' => 'module-one']);
        LearningLesson::factory()->for($module, 'module')->create(['order' => 1, 'slug' => 'lesson-one']);
        LearningLesson::factory()->for($module, 'module')->create(['order' => 2, 'slug' => 'lesson-two']);

        $this->actingAs(User::factory()->create())
            ->get(route('learning.lessons.show', ['module-one', 'lesson-two']))
            ->assertForbidden();
    }
}
