<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_glossary_terms', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('slug')->unique();
            $table->string('term');
            $table->string('full_name')->nullable()->comment('e.g. "Return on Equity" for the term "ROE"');
            $table->text('simple_definition');
            $table->text('formal_definition')->nullable();
            $table->text('example')->nullable();
            $table->text('application_usage')->nullable()->comment('"Why this matters to our system" section');
            $table->json('related_term_slugs')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_glossary_terms');
    }
};
