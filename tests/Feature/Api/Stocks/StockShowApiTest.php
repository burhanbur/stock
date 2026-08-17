<?php

namespace Tests\Feature\Api\Stocks;

use App\Models\ApiKey;
use App\Models\Company;
use App\Models\Sector;
use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockShowApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_stock_detail_for_a_valid_key(): void
    {
        $apiKey = ApiKey::factory()->create();

        $sector = Sector::factory()->create(['name' => 'Keuangan']);
        $company = Company::factory()->for($sector)->create(['name' => 'Bank Contoh Tbk']);
        $stock = Stock::factory()->for($company)->create(['ticker' => 'BBCX']);
        StockPrice::factory()->for($stock)->create(['trading_date' => now()->subDay(), 'close' => 1000]);
        StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1100]);

        $this->withHeaders(['X-API-KEY' => $apiKey->key])
            ->getJson('/api/v1/stocks/BBCX')
            ->assertOk()
            ->assertJsonPath('data.ticker', 'BBCX')
            ->assertJsonPath('data.company.name', 'Bank Contoh Tbk')
            ->assertJsonPath('data.latestClose', 1100)
            ->assertJsonPath('data.change', 100)
            ->assertJsonPath('data.changePercent', 10)
            ->assertJsonCount(2, 'data.prices');
    }

    public function test_it_returns_a_404_envelope_for_an_unknown_ticker(): void
    {
        $apiKey = ApiKey::factory()->create();

        $this->withHeaders(['X-API-KEY' => $apiKey->key])
            ->getJson('/api/v1/stocks/NOPE')
            ->assertStatus(404)
            ->assertJson(['success' => false]);
    }

    public function test_it_requires_an_api_key(): void
    {
        $this->getJson('/api/v1/stocks/BBCA')->assertStatus(401);
    }
}
