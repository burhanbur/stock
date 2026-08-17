<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Lesson prerequisites are intentionally implicit (a lesson requires the
     * previous `order` within the same module to be completed; a module
     * requires the previous module to be completed) rather than an explicit
     * prerequisite graph — the initial curriculum is linear. See
     * ai/learning-module.md if a real DAG becomes necessary later.
     */
    public function up(): void
    {
        Schema::create('learning_lessons', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('module_id', 36);
            $table->unsignedSmallInteger('order');
            $table->string('slug')->unique();
            $table->string('title');
            $table->unsignedSmallInteger('estimated_minutes')->default(10);
            $table->json('learning_objectives')->comment('Array of strings');
            $table->json('key_terms')->nullable()->comment('Array of learning_glossary_terms.slug referenced by this lesson');
            $table->longText('content')->comment('Lesson body, Markdown');
            $table->text('summary')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('module_id')->references('id')->on('learning_modules')->cascadeOnDelete();
            $table->unique(['module_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_lessons');
    }
};
