<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Sector;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Development seed data for real, publicly listed IDX companies.
     * Company identity here is real; the daily price history generated
     * by StockPriceSeeder is synthetic and clearly marked as such.
     */
    public function run(): void
    {
        DB::table('stocks')->delete();
        DB::table('companies')->delete();

        $companies = [
            ['sector' => 'FINC', 'ticker' => 'BBCA', 'name' => 'Bank Central Asia Tbk', 'short_name' => 'BCA', 'listed_at' => '2000-05-31'],
            ['sector' => 'FINC', 'ticker' => 'BBRI', 'name' => 'Bank Rakyat Indonesia (Persero) Tbk', 'short_name' => 'BRI', 'listed_at' => '2003-11-10'],
            ['sector' => 'FINC', 'ticker' => 'BMRI', 'name' => 'Bank Mandiri (Persero) Tbk', 'short_name' => 'Mandiri', 'listed_at' => '2003-07-14'],
            ['sector' => 'FINC', 'ticker' => 'BBNI', 'name' => 'Bank Negara Indonesia (Persero) Tbk', 'short_name' => 'BNI', 'listed_at' => '1996-11-25'],
            ['sector' => 'CNSA', 'ticker' => 'UNVR', 'name' => 'Unilever Indonesia Tbk', 'short_name' => 'Unilever', 'listed_at' => '1982-01-11'],
            ['sector' => 'CNSA', 'ticker' => 'ICBP', 'name' => 'Indofood CBP Sukses Makmur Tbk', 'short_name' => 'ICBP', 'listed_at' => '2010-10-07'],
            ['sector' => 'CNSA', 'ticker' => 'INDF', 'name' => 'Indofood Sukses Makmur Tbk', 'short_name' => 'Indofood', 'listed_at' => '1994-07-14'],
            ['sector' => 'INFR', 'ticker' => 'TLKM', 'name' => 'Telkom Indonesia (Persero) Tbk', 'short_name' => 'Telkom', 'listed_at' => '1995-11-14'],
            ['sector' => 'INDS', 'ticker' => 'ASII', 'name' => 'Astra International Tbk', 'short_name' => 'Astra', 'listed_at' => '1990-04-04'],
            ['sector' => 'BASM', 'ticker' => 'ANTM', 'name' => 'Aneka Tambang Tbk', 'short_name' => 'Antam', 'listed_at' => '1997-11-27'],
            ['sector' => 'BASM', 'ticker' => 'ADRO', 'name' => 'Adaro Energy Indonesia Tbk', 'short_name' => 'Adaro', 'listed_at' => '2008-07-16'],
            ['sector' => 'BASM', 'ticker' => 'INCO', 'name' => 'Vale Indonesia Tbk', 'short_name' => 'Vale', 'listed_at' => '1990-05-16'],
        ];

        foreach ($companies as $row) {
            $sector = Sector::where('code', $row['sector'])->first();

            $company = Company::create([
                'sector_id' => $sector?->id,
                'name' => $row['name'],
                'short_name' => $row['short_name'],
                'created_by' => null,
                'updated_by' => null,
            ]);

            Stock::create([
                'company_id' => $company->id,
                'ticker' => $row['ticker'],
                'exchange' => 'IDX',
                'currency' => 'IDR',
                'listed_at' => $row['listed_at'],
                'is_active' => true,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
