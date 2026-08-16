<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Daily OHLCV history. This is a high-volume, append-only fact
     * table, so it intentionally uses a bigint identity primary key
     * instead of the UUID convention used for dimension tables
     * (sectors/companies/stocks) to keep storage and index size down.
     */
    public function up(): void
    {
        Schema::create('stock_prices', function (Blueprint $table) {
            $table->id();
            $table->char('stock_id', 36);
            $table->date('trading_date');
            $table->decimal('open', 18, 2);
            $table->decimal('high', 18, 2);
            $table->decimal('low', 18, 2);
            $table->decimal('close', 18, 2);
            $table->unsignedBigInteger('volume')->default(0);
            $table->string('source', 50)->nullable()->comment('Data lineage, e.g. seed:dev, provider:idx');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('stock_id')->references('id')->on('stocks')->cascadeOnDelete();
            $table->unique(['stock_id', 'trading_date']);
            $table->index('trading_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_prices');
    }
};
