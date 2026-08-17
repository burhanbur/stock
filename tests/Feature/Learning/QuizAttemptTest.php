<?php

namespace Tests\Feature\Learning;

use App\Models\LearningLesson;
use App\Models\LearningModule;
use App\Models\LearningQuestion;
use App\Models\LearningQuestionOption;
use App\Models\LearningQuiz;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAttemptTest extends TestCase
{
    use RefreshDatabase;

    private function makeQuizWithOneQuestion(): array
    {
        $module = LearningModule::factory()->create(['order' => 1, 'slug' => 'module-one']);
        $lesson = LearningLesson::factory()->for($module, 'module')->create(['order' => 1, 'slug' => 'lesson-one']);
        $quiz = LearningQuiz::factory()->for($lesson, 'lesson')->create();
        $question = LearningQuestion::factory()->for($quiz, 'quiz')->create(['order' => 1]);
        $correct = LearningQuestionOption::factory()->for($question, 'question')->create(['order' => 1, 'is_correct' => true]);
        $wrong = LearningQuestionOption::factory()->for($question, 'question')->create(['order' => 2, 'is_correct' => false]);

        return compact('lesson', 'quiz', 'question', 'correct', 'wrong');
    }

    public function test_a_correct_answer_scores_100_and_completes_the_lesson(): void
    {
        ['lesson' => $lesson, 'quiz' => $quiz, 'question' => $question, 'correct' => $correct] = $this->makeQuizWithOneQuestion();
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('learning.quizzes.attempts.store', $quiz), [
                'answers' => [$question->id => $correct->id],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('learning_quiz_attempts', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 100,
            'correct_answers' => 1,
        ]);

        $this->assertDatabaseHas('learning_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_a_wrong_answer_scores_zero_but_still_completes_the_lesson(): void
    {
        ['lesson' => $lesson, 'quiz' => $quiz, 'question' => $question, 'wrong' => $wrong] = $this->makeQuizWithOneQuestion();
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('learning.quizzes.attempts.store', $quiz), [
            'answers' => [$question->id => $wrong->id],
        ]);

        $this->assertDatabaseHas('learning_quiz_attempts', [
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'score' => 0,
            'correct_answers' => 0,
        ]);

        $this->assertDatabaseHas('learning_progress', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => 'completed',
        ]);
    }

    public function test_it_requires_at_least_one_answer(): void
    {
        ['quiz' => $quiz] = $this->makeQuizWithOneQuestion();

        $this->actingAs(User::factory()->create())
            ->post(route('learning.quizzes.attempts.store', $quiz), ['answers' => []])
            ->assertSessionHasErrors('answers');
    }
}
