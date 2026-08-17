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

class StockCompareTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_compares_two_stocks(): void
    {
        $sector = Sector::factory()->create();
        $first = Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'AAAA']);
        $second = Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'BBBB']);
        StockPrice::factory()->for($first)->create(['trading_date' => now(), 'close' => 1000]);
        StockPrice::factory()->for($second)->create(['trading_date' => now(), 'close' => 2000]);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.compare', ['tickers' => 'AAAA,BBBB']))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Stocks/Compare')
                ->has('stocks', 2)
                ->where('stocks.0.ticker', 'AAAA')
                ->where('stocks.1.ticker', 'BBBB')
            );
    }

    public function test_it_caps_comparison_at_three_stocks(): void
    {
        $sector = Sector::factory()->create();
        $tickers = ['AAAA', 'BBBB', 'CCCC', 'DDDD'];
        foreach ($tickers as $ticker) {
            $stock = Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => $ticker]);
            StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1000]);
        }

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.compare', ['tickers' => implode(',', $tickers)]))
            ->assertInertia(fn (Assert $page) => $page->has('stocks', 3));
    }

    public function test_it_redirects_with_fewer_than_two_valid_tickers(): void
    {
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'AAAA']);
        StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1000]);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.compare', ['tickers' => 'AAAA']))
            ->assertRedirect(route('stocks.index'));
    }

    public function test_it_flags_unknown_tickers_but_still_compares_the_valid_ones(): void
    {
        $sector = Sector::factory()->create();
        $first = Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'AAAA']);
        $second = Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'BBBB']);
        StockPrice::factory()->for($first)->create(['trading_date' => now(), 'close' => 1000]);
        StockPrice::factory()->for($second)->create(['trading_date' => now(), 'close' => 2000]);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.compare', ['tickers' => 'AAAA,BBBB,NOPE']))
            ->assertInertia(fn (Assert $page) => $page->has('stocks', 2));
    }
}
