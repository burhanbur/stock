<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('sector_id', 36)->nullable();
            $table->string('name')->comment('Registered/legal company name');
            $table->string('short_name')->nullable();
            $table->text('description')->nullable();
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sector_id')->references('id')->on('sectors')->nullOnDelete();
            $table->index('sector_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
