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
