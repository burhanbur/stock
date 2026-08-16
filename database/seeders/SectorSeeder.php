<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sectors')->delete();

        $sectors = [
            ['code' => 'FINC', 'name' => 'Keuangan', 'description' => 'Perbankan dan jasa keuangan'],
            ['code' => 'CNSA', 'name' => 'Barang Konsumen Primer', 'description' => 'Consumer non-cyclicals'],
            ['code' => 'INFR', 'name' => 'Infrastruktur', 'description' => 'Telekomunikasi dan infrastruktur'],
            ['code' => 'INDS', 'name' => 'Industri', 'description' => 'Manufaktur dan otomotif'],
            ['code' => 'BASM', 'name' => 'Bahan Baku', 'description' => 'Pertambangan dan bahan baku'],
        ];

        foreach ($sectors as $sector) {
            Sector::create([
                ...$sector,
                'created_by' => null,
                'updated_by' => null,
            ]);
        }
    }
}
