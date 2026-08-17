<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_question_options', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('question_id', 36);
            $table->unsignedSmallInteger('order');
            $table->string('text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->foreign('question_id')->references('id')->on('learning_questions')->cascadeOnDelete();
            $table->index('question_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_question_options');
    }
};
