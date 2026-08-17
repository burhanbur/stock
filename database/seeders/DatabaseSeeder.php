<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->call([
                UserSeeder::class,

                SectorSeeder::class,
                CompanySeeder::class,
                StockPriceSeeder::class,

                LearningModuleSeeder::class,
                LearningLessonSeeder::class,
                LearningQuizSeeder::class,
                LearningGlossarySeeder::class,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
