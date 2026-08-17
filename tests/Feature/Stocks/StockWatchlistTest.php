<?php

namespace Tests\Feature\Stocks;

use App\Models\Company;
use App\Models\Stock;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StockWatchlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_a_stock_to_the_watchlist(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);

        $this->actingAs($user)
            ->post(route('stocks.watchlist.toggle', 'BBCX'))
            ->assertRedirect();

        $this->assertDatabaseHas('watchlists', ['user_id' => $user->id, 'stock_id' => $stock->id]);
    }

    public function test_it_removes_a_stock_already_on_the_watchlist(): void
    {
        $user = User::factory()->create();
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);
        Watchlist::create(['user_id' => $user->id, 'stock_id' => $stock->id]);

        $this->actingAs($user)
            ->post(route('stocks.watchlist.toggle', 'BBCX'))
            ->assertRedirect();

        $this->assertDatabaseMissing('watchlists', ['user_id' => $user->id, 'stock_id' => $stock->id]);
    }

    public function test_watchlist_status_is_per_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $stock = Stock::factory()->for(Company::factory())->create(['ticker' => 'BBCX']);
        Watchlist::create(['user_id' => $owner->id, 'stock_id' => $stock->id]);

        $this->actingAs($owner)
            ->get(route('stocks.show', 'BBCX'))
            ->assertInertia(fn (Assert $page) => $page->where('stock.is_watchlisted', true));

        $this->actingAs($otherUser)
            ->get(route('stocks.show', 'BBCX'))
            ->assertInertia(fn (Assert $page) => $page->where('stock.is_watchlisted', false));
    }

    public function test_it_filters_the_index_to_watchlisted_stocks_only(): void
    {
        $user = User::factory()->create();
        $watched = Stock::factory()->for(Company::factory())->create(['ticker' => 'WATCH']);
        Stock::factory()->for(Company::factory())->create(['ticker' => 'OTHER']);
        Watchlist::create(['user_id' => $user->id, 'stock_id' => $watched->id]);

        $this->actingAs($user)
            ->get(route('stocks.index', ['watchlist_only' => 1]))
            ->assertInertia(fn (Assert $page) => $page
                ->has('stocks.data', 1)
                ->where('stocks.data.0.ticker', 'WATCH')
            );
    }
}
