<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_modules', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->unsignedSmallInteger('order')->comment('Curriculum sequence, e.g. Module 01 = 1');
            $table->string('slug')->unique();
            $table->string('level', 20)->comment('ModuleLevel enum: beginner, intermediate, advanced, quant');
            $table->string('title');
            $table->text('description')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_modules');
    }
};
