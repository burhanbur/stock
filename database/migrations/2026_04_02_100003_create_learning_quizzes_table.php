<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_quizzes', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('lesson_id', 36);
            $table->string('title');
            $table->timestamps();

            $table->foreign('lesson_id')->references('id')->on('learning_lessons')->cascadeOnDelete();
            $table->index('lesson_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_quizzes');
    }
};
