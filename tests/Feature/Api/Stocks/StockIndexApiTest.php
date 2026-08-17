<?php

namespace Tests\Feature\Api\Stocks;

use App\Models\ApiKey;
use App\Models\Company;
use App\Models\Sector;
use App\Models\Stock;
use App\Models\StockPrice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockIndexApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_requires_an_api_key(): void
    {
        $this->getJson('/api/v1/stocks')
            ->assertStatus(401)
            ->assertJson(['success' => false]);
    }

    public function test_it_rejects_an_invalid_api_key(): void
    {
        $this->withHeaders(['X-API-KEY' => 'not-a-real-key'])
            ->getJson('/api/v1/stocks')
            ->assertStatus(401);
    }

    public function test_it_rejects_an_inactive_api_key(): void
    {
        $apiKey = ApiKey::factory()->inactive()->create();

        $this->withHeaders(['X-API-KEY' => $apiKey->key])
            ->getJson('/api/v1/stocks')
            ->assertStatus(401);
    }

    public function test_it_rejects_a_key_without_the_required_permission(): void
    {
        $apiKey = ApiKey::factory()->withPermissions(['other.permission'])->create();

        $this->withHeaders(['X-API-KEY' => $apiKey->key])
            ->getJson('/api/v1/stocks')
            ->assertStatus(403);
    }

    public function test_it_lists_stocks_for_a_valid_key(): void
    {
        $apiKey = ApiKey::factory()->create();

        $sector = Sector::factory()->create(['name' => 'Keuangan']);
        $company = Company::factory()->for($sector)->create(['name' => 'Bank Contoh Tbk']);
        $stock = Stock::factory()->for($company)->create(['ticker' => 'BBCX']);
        StockPrice::factory()->for($stock)->create(['trading_date' => now(), 'close' => 1050]);

        $this->withHeaders(['X-API-KEY' => $apiKey->key])
            ->getJson('/api/v1/stocks')
            ->assertOk()
            ->assertJson([
                'success' => true,
                'totalData' => 1,
            ])
            ->assertJsonPath('data.0.ticker', 'BBCX')
            ->assertJsonPath('data.0.companyName', 'Bank Contoh Tbk')
            ->assertJsonPath('data.0.sector.name', 'Keuangan')
            ->assertJsonPath('data.0.latestClose', 1050)
            ->assertJsonStructure(['pagination' => ['total', 'perPage', 'currentPage', 'lastPage']]);
    }

    public function test_a_key_scoped_with_a_wildcard_permission_is_allowed(): void
    {
        $apiKey = ApiKey::factory()->withPermissions(['*'])->create();

        $this->withHeaders(['X-API-KEY' => $apiKey->key])
            ->getJson('/api/v1/stocks')
            ->assertOk();
    }

    public function test_it_filters_by_search_term(): void
    {
        $apiKey = ApiKey::factory()->create();
        $sector = Sector::factory()->create();
        Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'AAAA']);
        Stock::factory()->for(Company::factory()->for($sector))->create(['ticker' => 'ZZZZ']);

        $this->withHeaders(['X-API-KEY' => $apiKey->key])
            ->getJson('/api/v1/stocks?search=AAAA')
            ->assertOk()
            ->assertJson(['totalData' => 1])
            ->assertJsonPath('data.0.ticker', 'AAAA');
    }
}
