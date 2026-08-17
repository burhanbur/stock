<?php

namespace Database\Seeders;

use App\Models\LearningGlossaryTerm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningGlossarySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('learning_glossary_terms')->delete();

        foreach ($this->terms() as $slug => $term) {
            LearningGlossaryTerm::create([
                'slug' => $slug,
                'term' => $term['term'],
                'full_name' => $term['full_name'] ?? null,
                'simple_definition' => $term['simple_definition'],
                'formal_definition' => $term['formal_definition'] ?? null,
                'example' => $term['example'] ?? null,
                'application_usage' => $term['application_usage'] ?? null,
                'related_term_slugs' => $term['related'] ?? [],
            ]);
        }
    }

    /**
     * @return array<string, array{term: string, full_name?: string, simple_definition: string, formal_definition?: string, example?: string, application_usage?: string, related?: array<int, string>}>
     */
    private function terms(): array
    {
        return [
            'perusahaan' => [
                'term' => 'Perusahaan',
                'full_name' => 'Company',
                'simple_definition' => 'Sebuah entitas yang punya aset, menjalankan kegiatan usaha, dan bertujuan menghasilkan keuntungan dari kegiatan itu.',
                'formal_definition' => 'Organisasi bisnis yang didirikan untuk memproduksi atau menjual barang/jasa demi memperoleh laba, dan dapat berbentuk badan hukum tersendiri.',
                'example' => 'Warung kopi kecil hingga perusahaan besar seperti Bank Central Asia (BBCA) sama-sama menjalankan fungsi "perusahaan".',
                'related' => ['aset', 'modal', 'perusahaan-publik'],
            ],
            'aset' => [
                'term' => 'Aset',
                'full_name' => 'Asset',
                'simple_definition' => 'Segala sesuatu yang dimiliki dan bernilai bagi sebuah bisnis.',
                'formal_definition' => 'Sumber daya yang dikendalikan oleh perusahaan akibat peristiwa masa lalu dan diharapkan memberi manfaat ekonomi di masa depan.',
                'example' => 'Mesin kopi, meja-kursi, stok bahan baku, dan kas warung kopimu semuanya termasuk aset.',
                'related' => ['modal', 'ekuitas'],
            ],
            'modal' => [
                'term' => 'Modal',
                'full_name' => 'Capital',
                'simple_definition' => 'Uang atau nilai yang ditanam oleh pemilik untuk memulai dan menjalankan bisnis.',
                'example' => 'Rp10 juta yang kamu tanam pertama kali untuk membeli mesin kopi dan meja-kursi adalah modal awal warungmu.',
                'related' => ['aset', 'ekuitas'],
            ],
            'kepemilikan' => [
                'term' => 'Kepemilikan',
                'full_name' => 'Ownership',
                'simple_definition' => 'Berapa persen dari sebuah bisnis menjadi hakmu — menentukan porsi untung/rugi dan suara dalam keputusan besar.',
                'example' => 'Kamu kontribusi Rp10 juta dan Sari Rp10 juta, sehingga kepemilikan kalian masing-masing 50%.',
                'related' => ['ekuitas', 'saham'],
            ],
            'ekuitas' => [
                'term' => 'Ekuitas',
                'full_name' => 'Equity',
                'simple_definition' => 'Nilai kepemilikan pemilik dalam sebuah bisnis — nilai aset dikurangi utang.',
                'formal_definition' => 'Hak residual atas aset perusahaan setelah dikurangi seluruh kewajiban (liabilitas).',
                'example' => 'Bisnis dengan aset Rp20.000.000 dan utang Rp5.000.000 punya ekuitas Rp15.000.000.',
                'application_usage' => 'Ekuitas adalah dasar dari rasio ROE (Return on Equity) yang akan kamu pelajari di Modul 5 — salah satu komponen skor fundamental di mesin rekomendasi kita.',
                'related' => ['aset', 'kepemilikan', 'roe'],
            ],
            'saham' => [
                'term' => 'Saham',
                'full_name' => 'Stock / Share',
                'simple_definition' => 'Pecahan kecil kepemilikan sebuah perusahaan yang bisa diperjualbelikan.',
                'formal_definition' => 'Surat berharga yang menyatakan bukti kepemilikan atas suatu perusahaan, memberikan hak proporsional atas laba dan aset perusahaan.',
                'example' => 'Kalau sebuah perusahaan punya 1.000.000 lembar saham dan kamu memegang 250.000 lembar, kamu memiliki 25% perusahaan itu.',
                'application_usage' => 'Setiap ticker (BBCA, TLKM, dst.) di halaman Saham aplikasi ini mewakili satu jenis saham yang diperdagangkan di IDX.',
                'related' => ['saham-beredar', 'kapitalisasi-pasar', 'kepemilikan'],
            ],
            'saham-beredar' => [
                'term' => 'Saham Beredar',
                'full_name' => 'Shares Outstanding',
                'simple_definition' => 'Total jumlah lembar saham yang diterbitkan oleh sebuah perusahaan.',
                'example' => 'Kalau total saham beredar 1.000.000 lembar dan kamu punya 250.000 lembar, kepemilikanmu 25%.',
                'related' => ['saham', 'kapitalisasi-pasar'],
            ],
            'kapitalisasi-pasar' => [
                'term' => 'Kapitalisasi Pasar',
                'full_name' => 'Market Capitalization',
                'simple_definition' => 'Estimasi nilai total sebuah perusahaan menurut harga sahamnya saat ini di pasar.',
                'formal_definition' => 'Harga per saham dikalikan jumlah saham beredar.',
                'example' => 'Harga saham Rp2.500 dengan 400.000.000 saham beredar → kapitalisasi pasar = Rp1 triliun.',
                'related' => ['saham', 'saham-beredar'],
            ],
            'ipo' => [
                'term' => 'IPO',
                'full_name' => 'Initial Public Offering (Penawaran Umum Perdana)',
                'simple_definition' => 'Proses pertama kali sebuah perusahaan menjual sahamnya ke masyarakat umum lewat bursa.',
                'example' => 'Setelah IPO, sebuah perusahaan berubah status dari perusahaan privat menjadi perusahaan publik (Tbk.).',
                'related' => ['perusahaan-publik', 'pasar-primer'],
            ],
            'perusahaan-publik' => [
                'term' => 'Perusahaan Publik',
                'full_name' => 'Public Company (Tbk.)',
                'simple_definition' => 'Perusahaan yang sahamnya sudah tercatat di bursa dan bisa dibeli siapa saja.',
                'example' => 'PT Bank Central Asia Tbk — akhiran "Tbk." menandakan statusnya sebagai perusahaan publik.',
                'related' => ['ipo', 'saham'],
            ],
            'pasar-primer' => [
                'term' => 'Pasar Primer',
                'full_name' => 'Primary Market',
                'simple_definition' => 'Tempat saham dijual pertama kali langsung dari perusahaan ke investor, biasanya saat IPO.',
                'example' => 'Uang hasil penjualan saham di pasar primer masuk langsung ke kas perusahaan untuk ekspansi.',
                'related' => ['pasar-sekunder', 'ipo'],
            ],
            'pasar-sekunder' => [
                'term' => 'Pasar Sekunder',
                'full_name' => 'Secondary Market',
                'simple_definition' => 'Tempat jual-beli saham sehari-hari antar-investor, setelah IPO selesai.',
                'example' => 'Ketika kamu membeli saham BBCA hari ini lewat aplikasi sekuritas, kamu membelinya dari investor lain, bukan dari Bank BCA langsung.',
                'application_usage' => 'Bursa Efek Indonesia (IDX) pada dasarnya adalah tempat pasar sekunder ini beroperasi setiap hari kerja.',
                'related' => ['pasar-primer'],
            ],
            'dividen' => [
                'term' => 'Dividen',
                'full_name' => 'Dividend',
                'simple_definition' => 'Bagian laba perusahaan yang dibagikan secara tunai ke pemegang saham.',
                'example' => 'Dividen Rp50 per saham untuk pemegang 100.000 lembar saham = Rp5.000.000.',
                'application_usage' => 'Dividend Yield (dividen dibagi harga saham) menjadi salah satu komponen Skor Valuasi di Modul 6.',
                'related' => ['capital-gain', 'return'],
            ],
            'capital-gain' => [
                'term' => 'Capital Gain',
                'simple_definition' => 'Selisih untung dari perbedaan harga jual dan harga beli saham.',
                'formal_definition' => 'Keuntungan yang direalisasikan ketika aset dijual pada harga lebih tinggi dari harga belinya. Kebalikannya disebut Capital Loss.',
                'example' => 'Beli di Rp5.000, jual di Rp8.000 (10.000 lembar) = capital gain Rp30.000.000.',
                'related' => ['dividen', 'return'],
            ],
            'return' => [
                'term' => 'Return',
                'full_name' => 'Imbal Hasil',
                'simple_definition' => 'Total keuntungan investasi, gabungan dari dividen dan capital gain.',
                'example' => 'Return dividen 1,19% + return capital gain 9,52% = total return sekitar 10,71%.',
                'related' => ['dividen', 'capital-gain'],
            ],
            'risiko' => [
                'term' => 'Risiko',
                'full_name' => 'Risk',
                'simple_definition' => 'Ketidakpastian tentang seberapa jauh hasil investasimu bisa menyimpang dari yang diharapkan, baik untung maupun rugi.',
                'application_usage' => 'Skor Risiko adalah salah satu dari lima komponen skor pada mesin rekomendasi kita (lihat Modul 13).',
                'related' => ['volatilitas'],
            ],
            'volatilitas' => [
                'term' => 'Volatilitas',
                'full_name' => 'Volatility',
                'simple_definition' => 'Seberapa liar harga suatu saham bergerak naik-turun dari waktu ke waktu.',
                'example' => 'Saham yang bergerak ±8% per hari punya volatilitas jauh lebih tinggi dari saham yang bergerak ±1% per hari.',
                'application_usage' => 'Volatilitas historis dari data harga saham menjadi salah satu input untuk Skor Risiko di mesin rekomendasi kita.',
                'related' => ['risiko'],
            ],
            'obligasi' => [
                'term' => 'Obligasi',
                'full_name' => 'Bond',
                'simple_definition' => 'Surat utang: kamu meminjamkan uang ke penerbitnya dan menerima bunga tetap plus pengembalian pokok.',
                'formal_definition' => 'Instrumen utang di mana pemegangnya adalah kreditur (pemberi pinjaman), bukan pemilik entitas penerbit.',
                'example' => 'Berbeda dari saham, pemegang obligasi bukan pemilik perusahaan — mereka adalah pemberi pinjaman.',
                'related' => ['saham', 'likuiditas'],
            ],
            'likuiditas' => [
                'term' => 'Likuiditas',
                'full_name' => 'Liquidity',
                'simple_definition' => 'Seberapa cepat dan mudah sebuah aset bisa diubah menjadi uang tunai tanpa kehilangan banyak nilai.',
                'example' => 'Saham aktif punya likuiditas tinggi (bisa dijual dalam hitungan detik); properti punya likuiditas rendah (bisa butuh berbulan-bulan terjual).',
                'related' => ['obligasi'],
            ],
        ];
    }
}
