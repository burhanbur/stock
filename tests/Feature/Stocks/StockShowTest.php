<?php

namespace Tests\Feature\Stocks;

use App\Models\Company;
use App\Models\Sector;
use App\Models\Stock;
use App\Models\StockPrice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StockShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_stock_detail_with_change_from_previous_close(): void
    {
        $sector = Sector::factory()->create(['name' => 'Keuangan']);
        $company = Company::factory()->for($sector)->create(['name' => 'Bank Contoh Tbk']);
        $stock = Stock::factory()->for($company)->create(['ticker' => 'BBCX']);

        StockPrice::factory()->for($stock)->create(['trading_date' => now()->subDay(), 'close' => 1000]);
        StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1100]);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.show', 'BBCX'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Stocks/Show')
                ->where('stock.ticker', 'BBCX')
                ->where('stock.company.name', 'Bank Contoh Tbk')
                ->where('stock.sector.name', 'Keuangan')
                ->where('stock.latest_close', 1100)
                ->where('stock.change', 100)
                ->where('stock.change_percent', 10)
                ->has('stock.prices', 2)
            );
    }

    public function test_it_includes_a_recommendation_once_there_is_enough_price_history(): void
    {
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);

        // 60 days of a steady uptrend gives the momentum calculator enough
        // history (min 50 points) to produce a real score instead of "Data
        // belum cukup".
        foreach (range(0, 59) as $i) {
            StockPrice::factory()->for($stock)->create([
                'trading_date' => now()->subDays(59 - $i),
                'close' => 1000 + $i * 5,
            ]);
        }

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.show', 'BBCX'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Stocks/Show')
                ->where('stock.recommendation.label', 'Beli')
                ->where('stock.recommendation.momentum.label', 'Beli')
                ->has('stock.recommendation.risk.score')
            );
    }

    public function test_it_reports_insufficient_data_for_recommendation_with_too_little_history(): void
    {
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);
        StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1000]);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.show', 'BBCX'))
            ->assertInertia(fn (Assert $page) => $page
                ->where('stock.recommendation.label', 'Data belum cukup')
                ->where('stock.recommendation.score', null)
            );
    }

    public function test_price_history_is_paginated_and_sorted_by_date_descending_by_default(): void
    {
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);

        foreach (range(0, 19) as $i) {
            StockPrice::factory()->for($stock)->create(['trading_date' => now()->subDays(19 - $i), 'close' => 1000 + $i]);
        }

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.show', 'BBCX'))
            ->assertInertia(fn (Assert $page) => $page
                ->has('priceHistory.data', 15)
                ->where('priceHistory.total', 20)
                ->where('priceHistory.data.0.trading_date', now()->toDateString())
            );
    }

    public function test_price_history_can_be_sorted_by_close_ascending(): void
    {
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);
        StockPrice::factory()->for($stock)->create(['trading_date' => now()->subDay(), 'close' => 2000]);
        StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1000]);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.show', ['ticker' => 'BBCX', 'price_sort' => 'close']))
            ->assertInertia(fn (Assert $page) => $page
                ->where('priceHistory.data.0.close', 1000)
                ->where('priceHistory.data.1.close', 2000)
            );
    }

    public function test_it_returns_404_for_an_unknown_ticker(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('stocks.show', 'NOPE'))
            ->assertNotFound();
    }

    public function test_ticker_lookup_is_case_insensitive(): void
    {
        Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.show', 'bbcx'))
            ->assertInertia(fn (Assert $page) => $page->where('stock.ticker', 'BBCX'));
    }
}
