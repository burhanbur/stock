<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A "stock" is the listed instrument (ticker) on an exchange, kept
     * separate from "company" because tickers can change or be
     * re-used over time while the underlying company does not.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('company_id', 36);
            $table->string('ticker', 10)->comment('Current active ticker, e.g. BBCA');
            $table->string('exchange', 10)->default('IDX');
            $table->string('board', 20)->nullable()->comment('Main, Development, Acceleration, etc.');
            $table->char('currency', 3)->default('IDR');
            $table->date('listed_at')->nullable();
            $table->boolean('is_active')->default(true)->comment('Still listed / trading');
            $table->char('created_by', 36)->nullable();
            $table->char('updated_by', 36)->nullable();
            $table->char('deleted_by', 36)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->restrictOnDelete();
            $table->unique(['exchange', 'ticker']);
            $table->index('company_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
