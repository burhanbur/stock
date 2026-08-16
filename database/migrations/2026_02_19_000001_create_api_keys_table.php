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
        Schema::create('api_keys', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('name')->comment('Friendly name for the API key');
            $table->string('key', 64)->unique()->comment('The actual API key');
            $table->text('description')->nullable();
            $table->string('application')->nullable()->comment('Application or system using this key');
            $table->json('ip_whitelist')->nullable()->comment('Allowed IP addresses');
            $table->json('permissions')->nullable()->comment('Allowed endpoints/actions');
            $table->boolean('is_active')->default(true);
            $table->integer('rate_limit')->default(60)->comment('Requests per minute');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('key');
            $table->index('is_active');
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_keys');
    }
};
