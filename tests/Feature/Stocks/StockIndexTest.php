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

class StockIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('stocks.index'))->assertRedirect(route('login'));
    }

    public function test_it_lists_stocks_with_their_latest_price(): void
    {
        $sector = Sector::factory()->create(['name' => 'Keuangan']);
        $company = Company::factory()->for($sector)->create(['name' => 'Bank Contoh Tbk']);
        $stock = Stock::factory()->for($company)->create(['ticker' => 'BBCX']);

        StockPrice::factory()->for($stock)->create(['trading_date' => now()->subDay(), 'close' => 1000]);
        StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1050]);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.index'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('Stocks/Index')
                ->has('stocks.data', 1)
                ->where('stocks.data.0.ticker', 'BBCX')
                ->where('stocks.data.0.latest_close', 1050)
                ->has('sectors', 1)
            );
    }

    public function test_it_filters_stocks_by_search_term(): void
    {
        $sector = Sector::factory()->create();
        Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'AAAA']);
        Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'ZZZZ']);

        $this->actingAs(User::factory()->create())
            ->get(route('stocks.index', ['search' => 'AAAA']))
            ->assertInertia(fn (Assert $page) => $page
                ->has('stocks.data', 1)
                ->where('stocks.data.0.ticker', 'AAAA')
            );
    }

    public function test_it_rejects_an_invalid_sort_value(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('stocks.index', ['sort' => 'not-a-real-column']))
            ->assertSessionHasErrors('sort');
    }
}
