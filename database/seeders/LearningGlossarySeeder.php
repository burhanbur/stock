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
            'rata-rata-bergerak' => [
                'term' => 'Rata-Rata Bergerak',
                'full_name' => 'Moving Average (MA)',
                'simple_definition' => 'Harga rata-rata suatu saham selama sejumlah hari terakhir, dihitung ulang setiap hari — dipakai untuk melihat arah tren tanpa terganggu naik-turun harian.',
                'formal_definition' => 'Rata-rata aritmatika dari harga penutupan dalam N periode terakhir, bergeser maju setiap periode baru.',
                'example' => 'MA20 dari saham yang ditutup di 100, 102, 98, ... selama 20 hari terakhir adalah jumlah 20 harga itu dibagi 20.',
                'application_usage' => 'Posisi harga saat ini relatif terhadap MA20 dan MA50-nya menjadi salah satu input Skor Momentum di halaman Detail Saham.',
                'related' => ['momentum', 'volatilitas'],
            ],
            'momentum' => [
                'term' => 'Momentum',
                'simple_definition' => 'Seberapa kuat dan searah pergerakan harga sebuah saham belakangan ini — naik terus, turun terus, atau datar.',
                'formal_definition' => 'Tingkat perubahan harga suatu aset dalam periode tertentu, digunakan sebagai sinyal apakah tren yang sedang berjalan cenderung berlanjut.',
                'example' => 'Saham yang naik 10% dalam 20 hari terakhir punya momentum positif; yang turun 10% punya momentum negatif.',
                'application_usage' => 'Skor Momentum di halaman Detail Saham menggabungkan tren rata-rata bergerak dan return 20 hari terakhir menjadi label Beli/Tahan/Jual.',
                'related' => ['rata-rata-bergerak', 'risiko'],
            ],

            // Modul 2: Mekanisme Pasar & Transaksi Saham
            'bursa-efek' => [
                'term' => 'Bursa Efek',
                'full_name' => 'Stock Exchange',
                'simple_definition' => 'Pasar terpusat dan diawasi tempat semua order beli-jual saham dari seluruh investor dipertemukan.',
                'example' => 'Semua saham di aplikasi ini (BBCA, TLKM, ASII, dst.) diperdagangkan di Bursa Efek Indonesia (IDX).',
                'application_usage' => 'Kolom "Exchange" pada setiap kartu saham di halaman Saham selalu bertuliskan IDX.',
                'related' => ['broker'],
            ],
            'broker' => [
                'term' => 'Broker',
                'full_name' => 'Perusahaan Sekuritas',
                'simple_definition' => 'Perantara resmi berizin yang menjembatani order beli-jual investor perorangan ke sistem bursa.',
                'example' => 'Budi tidak bisa mengakses IDX langsung — ia harus memasukkan order lewat aplikasi broker tempat ia membuka rekening.',
                'related' => ['bursa-efek', 'rekening-dana-nasabah'],
            ],
            'rekening-dana-nasabah' => [
                'term' => 'Rekening Dana Nasabah',
                'full_name' => 'RDN',
                'simple_definition' => 'Rekening bank khusus atas nama investor sendiri untuk menyimpan dana sebelum dipakai membeli saham — terpisah dari rekening milik broker.',
                'formal_definition' => 'Rekening yang dibuka atas nama nasabah di bank yang ditunjuk, untuk melindungi dana nasabah dari risiko bisnis perusahaan sekuritas.',
                'related' => ['broker'],
            ],
            'lot' => [
                'term' => 'Lot',
                'simple_definition' => 'Satuan minimum transaksi saham di IDX: 1 Lot = 100 lembar saham.',
                'example' => 'Membeli 1 lot saham seharga Rp6.350/lembar berarti membayar Rp6.350 × 100 = Rp635.000 (belum termasuk biaya broker).',
                'application_usage' => 'Harga yang ditampilkan di halaman Saham dan Detail Saham selalu per lembar — kalikan 100 untuk memperkirakan nilai transaksi per lot.',
            ],
            'bid' => [
                'term' => 'Bid',
                'simple_definition' => 'Harga tertinggi yang bersedia dibayar oleh calon pembeli saham saat itu.',
                'related' => ['ask', 'spread'],
            ],
            'ask' => [
                'term' => 'Ask',
                'full_name' => 'Offer',
                'simple_definition' => 'Harga terendah yang diminta oleh calon penjual saham saat itu.',
                'related' => ['bid', 'spread'],
            ],
            'spread' => [
                'term' => 'Spread',
                'simple_definition' => 'Selisih antara harga ask dan harga bid suatu saham pada suatu saat.',
                'formal_definition' => 'Spread = Ask − Bid. Spread yang sempit menandakan likuiditas tinggi; spread lebar menandakan likuiditas rendah.',
                'example' => 'Bid Rp6.325 dan ask Rp6.350 berarti spread-nya Rp25.',
                'related' => ['bid', 'ask', 'likuiditas'],
            ],
            'market-order' => [
                'term' => 'Market Order',
                'simple_definition' => 'Perintah membeli/menjual segera di harga pasar terbaik yang tersedia, tanpa menentukan harga spesifik.',
                'related' => ['limit-order'],
            ],
            'limit-order' => [
                'term' => 'Limit Order',
                'simple_definition' => 'Perintah membeli/menjual hanya pada harga tertentu (atau lebih baik) yang ditentukan sendiri oleh investor.',
                'related' => ['market-order'],
            ],
            'auto-reject' => [
                'term' => 'Auto Reject',
                'simple_definition' => 'Batas kenaikan (ARA) atau penurunan (ARB) harga maksimum yang diizinkan bursa dalam satu hari perdagangan.',
                'application_usage' => 'Karena ada batas ini, harga saham secara wajar tidak akan pernah melompat ekstrem dalam sehari di data historis kita — lonjakan tidak wajar biasanya pertanda data yang belum bersih.',
            ],
            'hari-bursa' => [
                'term' => 'Hari Bursa',
                'simple_definition' => 'Hari kerja saat Bursa Efek Indonesia buka untuk perdagangan — Senin sampai Jumat, kecuali libur nasional/bursa.',
                'application_usage' => 'Perhitungan seperti "return 20 hari" pada Skor Momentum kita menghitung 20 hari bursa, bukan 20 hari kalender.',
            ],
            'ticker' => [
                'term' => 'Ticker',
                'full_name' => 'Kode Saham',
                'simple_definition' => 'Kode unik (biasanya 4 huruf di IDX) yang mewakili sebuah perusahaan tercatat di bursa.',
                'example' => 'BBCA adalah ticker untuk PT Bank Central Asia Tbk.',
            ],
            'papan-pencatatan' => [
                'term' => 'Papan Pencatatan',
                'simple_definition' => 'Pengelompokan perusahaan tercatat di IDX (misalnya Papan Utama, Papan Pengembangan) berdasarkan kriteria seperti ukuran dan kinerja.',
            ],

            // Modul 3: Membaca Data Harga Saham
            'ohlc' => [
                'term' => 'OHLC',
                'full_name' => 'Open, High, Low, Close',
                'simple_definition' => 'Empat angka harga yang dicatat untuk setiap saham setiap hari perdagangan: harga pembukaan, tertinggi, terendah, dan penutupan.',
                'application_usage' => 'Tabel Riwayat Harga di halaman Detail Saham menampilkan OHLC (plus Volume) untuk setiap hari bursa.',
                'related' => ['volume-perdagangan'],
            ],
            'candlestick' => [
                'term' => 'Candlestick',
                'full_name' => 'Grafik Lilin',
                'simple_definition' => 'Jenis grafik harga yang menampilkan keempat angka OHLC sekaligus per hari dalam bentuk "lilin" — standar umum di aplikasi trading profesional.',
                'application_usage' => 'Aplikasi ini memakai line chart (bukan candlestick) berbasis harga Close agar lebih sederhana bagi pemula.',
            ],
            'volume-perdagangan' => [
                'term' => 'Volume Perdagangan',
                'full_name' => 'Trading Volume',
                'simple_definition' => 'Jumlah total lembar saham yang berpindah tangan dalam satu hari perdagangan.',
                'application_usage' => 'Pergerakan harga yang disertai volume tinggi umumnya dianggap lebih meyakinkan daripada pergerakan dengan volume rendah.',
                'related' => ['ohlc'],
            ],
            'tren' => [
                'term' => 'Tren',
                'full_name' => 'Trend',
                'simple_definition' => 'Arah pergerakan umum harga saham dalam suatu periode: naik (uptrend), turun (downtrend), atau relatif mendatar (sideways).',
                'related' => ['momentum', 'rata-rata-bergerak'],
            ],
            'support' => [
                'term' => 'Support',
                'simple_definition' => 'Level harga di mana saham secara historis cenderung berhenti turun dan berbalik naik — seperti "lantai" harga.',
                'application_usage' => 'Level Support terdeteksi otomatis di halaman Detail Saham dari titik-titik balik harga historis — pola masa lalu, bukan jaminan masa depan.',
                'related' => ['resistance'],
            ],
            'resistance' => [
                'term' => 'Resistance',
                'simple_definition' => 'Level harga di mana saham secara historis cenderung berhenti naik dan berbalik turun — seperti "langit-langit" harga.',
                'application_usage' => 'Level Resistance terdeteksi otomatis di halaman Detail Saham dari titik-titik balik harga historis — pola masa lalu, bukan jaminan masa depan.',
                'related' => ['support'],
            ],
            'perubahan-harga' => [
                'term' => 'Perubahan Harga',
                'full_name' => 'Change / Change Percent',
                'simple_definition' => 'Selisih harga Close hari ini dengan Close hari bursa sebelumnya, dalam Rupiah (change) maupun persentase (change percent).',
                'formal_definition' => 'Change = Close hari ini − Close hari bursa sebelumnya. Change Percent = (Change ÷ Close hari bursa sebelumnya) × 100%.',
                'application_usage' => 'Badge hijau/merah di halaman Saham dan Detail Saham menampilkan angka ini persis.',
            ],
            'data-historis' => [
                'term' => 'Data Historis',
                'simple_definition' => 'Kumpulan data harga dari hari-hari sebelumnya yang sudah selesai dan tercatat — dipakai untuk analisis pola, berbeda dari data real-time/live yang bergerak tiap detik.',
                'application_usage' => 'Aplikasi ini memakai data historis yang disinkronkan berkala, bukan data real-time — bukan alat untuk transaksi mendadak.',
            ],
        ];
    }
}
