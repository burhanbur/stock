<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A user's personal shortlist of stocks to keep an eye on. Per-user
     * preference join table, same shape as learning_progress (UUID PK,
     * user_id + entity_id FK, unique pair) rather than the fact-table
     * (bigint PK) or dimension-table (audit columns + soft deletes)
     * conventions — toggling watchlist membership is a hard add/remove,
     * not something that needs history or soft-deletion.
     */
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('user_id', 36);
            $table->char('stock_id', 36);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('stock_id')->references('id')->on('stocks')->cascadeOnDelete();
            $table->unique(['user_id', 'stock_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};
