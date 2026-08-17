<?php

namespace Database\Seeders;

use App\Models\LearningModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningModuleSeeder extends Seeder
{
    /**
     * Modules 01–03 are seeded. See ai/learning-module.md for why the rest
     * of the 13-module curriculum isn't implemented yet, and note that
     * Modules 4, 5, 6, 8, 9, 11, and 13 are already promised by number in
     * shipped lesson/glossary content — don't renumber around them when
     * adding more modules later.
     */
    public function run(): void
    {
        DB::table('learning_modules')->delete();

        LearningModule::create([
            'order' => 1,
            'slug' => 'dasar-dasar-saham',
            'level' => 'beginner',
            'title' => 'Dasar-Dasar Saham',
            'description' => 'Mulai dari nol: apa itu perusahaan, kepemilikan, saham, dan bagaimana investor bisa untung maupun rugi.',
        ]);

        LearningModule::create([
            'order' => 2,
            'slug' => 'mekanisme-pasar',
            'level' => 'beginner',
            'title' => 'Mekanisme Pasar & Transaksi Saham',
            'description' => 'Bagaimana jual-beli saham sesungguhnya terjadi di Bursa Efek Indonesia — broker, lot, bid-ask, order, dan aturan main lainnya.',
        ]);

        LearningModule::create([
            'order' => 3,
            'slug' => 'membaca-data-harga-saham',
            'level' => 'beginner',
            'title' => 'Membaca Data Harga Saham',
            'description' => 'Cara membaca OHLCV, grafik harga, volume, tren, dan level support/resistance — bekal sebelum masuk ke laporan keuangan.',
        ]);
    }
}
