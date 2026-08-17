<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_questions', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('quiz_id', 36);
            $table->unsignedSmallInteger('order');
            $table->string('type', 20)->default('multiple_choice')->comment('QuestionType enum');
            $table->text('question');
            $table->text('explanation')->comment('Shown after answering, correct or not');
            $table->string('difficulty', 10)->nullable()->comment('easy, medium, hard');
            $table->timestamps();

            $table->foreign('quiz_id')->references('id')->on('learning_quizzes')->cascadeOnDelete();
            $table->index('quiz_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_questions');
    }
};
