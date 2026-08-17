<?php

namespace Database\Seeders;

use App\Models\LearningLesson;
use App\Models\LearningModule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningLessonSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('learning_lessons')->delete();

        foreach ($this->moduleLessons() as $moduleSlug => $lessons) {
            $module = LearningModule::where('slug', $moduleSlug)->firstOrFail();

            foreach ($lessons as $order => $lesson) {
                LearningLesson::create([
                    'module_id' => $module->id,
                    'order' => $order + 1,
                    'slug' => $lesson['slug'],
                    'title' => $lesson['title'],
                    'estimated_minutes' => $lesson['estimated_minutes'],
                    'learning_objectives' => $lesson['learning_objectives'],
                    'key_terms' => $lesson['key_terms'],
                    'content' => $lesson['content'],
                    'summary' => $lesson['summary'],
                ]);
            }
        }
    }

    /**
     * @return array<string, array<int, array{slug: string, title: string, estimated_minutes: int, learning_objectives: array<int, string>, key_terms: array<int, string>, content: string, summary: string}>>
     */
    private function moduleLessons(): array
    {
        return [
            'dasar-dasar-saham' => [
                $this->lessonApaItuPerusahaan(),
                $this->lessonApaItuKepemilikan(),
                $this->lessonApaItuSaham(),
                $this->lessonMengapaPerusahaanMenjualSaham(),
                $this->lessonMengapaInvestorMembeliSaham(),
                $this->lessonBagaimanaInvestorUntung(),
                $this->lessonBagaimanaInvestorRugi(),
                $this->lessonSahamVsAsetLain(),
            ],
            'mekanisme-pasar' => [
                $this->lessonApaItuBursaEfek(),
                $this->lessonBrokerDanRekeningEfek(),
                $this->lessonApaItuLot(),
                $this->lessonBidAskDanSpread(),
                $this->lessonJenisOrder(),
                $this->lessonAutoRejectDanBatasHarga(),
                $this->lessonJamDanHariPerdagangan(),
                $this->lessonKodeSahamDanPapanPencatatan(),
            ],
            'membaca-data-harga-saham' => [
                $this->lessonApaItuDataOhlc(),
                $this->lessonMembacaGrafikHarga(),
                $this->lessonApaItuVolumePerdagangan(),
                $this->lessonMengenaliTrenHarga(),
                $this->lessonSupportDanResistance(),
                $this->lessonMemahamiPerubahanHargaHarian(),
                $this->lessonDataHistorisVsRealTime(),
                $this->lessonLatihanMembacaHalamanDetailSaham(),
            ],
        ];
    }

    private function lessonApaItuPerusahaan(): array
    {
        return [
            'slug' => 'apa-itu-perusahaan',
            'title' => 'Apa Itu Perusahaan?',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan apa itu perusahaan dengan bahasa sendiri, bukan definisi buku teks',
                'Membedakan aset dan modal dalam konteks bisnis kecil',
                'Memahami bagaimana sebuah bisnis menghasilkan uang',
            ],
            'key_terms' => ['perusahaan', 'aset', 'modal'],
            'summary' => 'Perusahaan adalah kendaraan untuk menjalankan bisnis: kamu masukkan modal, beli aset, jual produk/jasa, dan (semoga) untung. Semua konsep saham nanti dibangun di atas ide sederhana ini.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Sebelum bicara soal saham, IHSG, atau P/E Ratio, kita perlu mundur dulu ke pertanyaan paling dasar: **apa sebenarnya yang kamu miliki ketika kamu memiliki saham sebuah perusahaan?**

Jawabannya dimulai dari memahami apa itu perusahaan itu sendiri. Kalau ini sudah jelas, semua bab berikutnya jauh lebih mudah dipahami.

## Bayangkan Kamu Buka Warung Kopi

Anggap kamu punya Rp10 juta tabungan, dan kamu memutuskan buka warung kopi kecil.

Dengan uang itu, kamu beli:

- Mesin kopi — Rp4 juta
- Meja dan kursi — Rp2 juta
- Stok biji kopi dan gula — Rp1 juta
- Sisa Rp3 juta kamu simpan sebagai kas untuk operasional

Semua barang dan uang tunai itu — mesin, meja-kursi, stok, kas — disebut **Aset**. Aset adalah segala sesuatu yang dimiliki dan bernilai bagi bisnis kamu.

Rp10 juta yang kamu masukkan di awal disebut **Modal**. Modal adalah uang (atau nilai) yang ditanam oleh pemilik untuk memulai dan menjalankan bisnis.

Jadi persamaannya sederhana:

```
Modal yang kamu tanam  →  dibelikan Aset  →  dipakai untuk jualan kopi  →  hasilkan uang
```

## Lalu, Apa Itu "Perusahaan"?

Warung kopimu, secara sederhana, sudah menjalankan fungsi sebuah **Perusahaan**: sebuah entitas yang punya aset, menjalankan kegiatan usaha (jualan kopi), dan bertujuan menghasilkan keuntungan dari kegiatan itu.

Bedanya, warung kopimu masih sangat kecil dan hanya kamu yang memilikinya. Perusahaan besar seperti Bank Central Asia (BBCA) atau Telkom Indonesia (TLKM) menjalankan ide yang persis sama — punya aset (gedung, mesin, uang tunai, piutang), menjalankan usaha (perbankan, telekomunikasi), dan berusaha menghasilkan keuntungan — hanya saja skalanya jutaan kali lebih besar, dan pemiliknya bukan cuma satu orang.

## Bisnis Untung, Bisnis Rugi

Setiap bulan, warung kopimu punya:

- **Pemasukan**: uang dari jualan kopi ke pelanggan
- **Pengeluaran**: beli bahan baku baru, bayar listrik, gaji karyawan (kalau ada)

Kalau pemasukan lebih besar dari pengeluaran, warungmu **untung**. Kalau lebih kecil, warungmu **rugi**. Ini adalah inti dari bagaimana SEMUA bisnis — dari warung kopi sampai bank raksasa — beroperasi. Nanti di Modul 4 kita akan belajar membaca laporan keuangan yang mencatat semua ini secara formal.

## Kenapa Ini Penting untuk Sistem Kita?

Setiap saham di aplikasi ini (BBCA, TLKM, ASII, dan lainnya) mewakili sebuah **Perusahaan** sungguhan yang punya aset, menjalankan usaha, dan mencetak untung/rugi setiap periode. Data harga saham yang kamu lihat di halaman [Saham](/stocks) pada akhirnya bergerak karena performa bisnis di baliknya — bukan angka acak.

## Latihan

Warung kopimu di akhir bulan mencatat:

- Pemasukan dari jualan: Rp8.000.000
- Pengeluaran (bahan baku, listrik, dll): Rp5.500.000

Apakah warungmu untung atau rugi bulan ini, dan berapa besarnya?
MD,
        ];
    }

    private function lessonApaItuKepemilikan(): array
    {
        return [
            'slug' => 'apa-itu-kepemilikan',
            'title' => 'Apa Itu Kepemilikan?',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Menjelaskan apa artinya "memiliki" bagian dari sebuah bisnis',
                'Memahami bagaimana kepemilikan bisa dibagi ke beberapa orang',
                'Mengenal istilah ekuitas sebagai nilai kepemilikan',
            ],
            'key_terms' => ['kepemilikan', 'ekuitas'],
            'summary' => 'Kepemilikan bisa dibagi berdasarkan porsi (%), dan porsi itu menentukan berapa besar bagian untung/rugi serta suara dalam keputusan yang kamu dapat. Nilai kepemilikanmu disebut ekuitas.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Di pelajaran sebelumnya, warung kopi itu 100% milikmu sendiri. Tapi kebanyakan bisnis — apalagi yang besar — punya lebih dari satu pemilik. Memahami bagaimana kepemilikan dibagi adalah kunci untuk memahami apa itu saham nanti.

## Warung Kopi, Sekarang Berdua

Bayangkan temanmu, Sari, tertarik ikut bisnis warung kopimu. Kalian sepakat:

- Kamu sudah punya warung senilai Rp10 juta (dari modal awal)
- Sari masukkan tambahan modal Rp10 juta untuk ekspansi (buka cabang kedua)

Total modal warung sekarang Rp20 juta. Karena kalian masing-masing menyumbang Rp10 juta, kalian sepakat membagi **Kepemilikan** 50:50.

Kepemilikan berarti: berapa persen dari bisnis itu menjadi hakmu. Kalau kamu punya 50% kepemilikan, itu berarti:

- Kamu berhak atas 50% dari keuntungan (atau menanggung 50% dari kerugian)
- Kamu punya 50% suara dalam keputusan besar (misalnya: apakah buka cabang ketiga?)
- Kalau warung dijual, kamu berhak atas 50% dari hasil penjualannya

## Kepemilikan Tidak Harus 50:50

Pembagian kepemilikan tidak selalu rata. Kalau Sari cuma masukkan Rp5 juta sementara kamu tetap Rp10 juta (dari total modal Rp15 juta), maka wajarnya:

- Kepemilikanmu = 10/15 = **66,7%**
- Kepemilikan Sari = 5/15 = **33,3%**

Semakin besar porsi modal yang kamu tanam (atau nilai yang kamu kontribusikan), semakin besar persentase kepemilikanmu.

## Mengenalkan Istilah "Ekuitas"

Nilai kepemilikanmu dalam sebuah bisnis punya nama formal: **Ekuitas (Equity)**. Ekuitas adalah nilai yang tersisa untuk pemilik setelah semua utang bisnis dilunasi.

Rumus sederhananya:

```
Ekuitas Pemilik = Nilai Aset Bisnis − Utang Bisnis
```

Kalau warung kalian punya aset senilai Rp20 juta dan tidak ada utang sama sekali, maka total ekuitas warung = Rp20 juta. Ekuitasmu (66,7%) = sekitar Rp13,3 juta, dan ekuitas Sari (33,3%) = sekitar Rp6,7 juta.

Kalau warung punya utang (misalnya pinjam Rp5 juta ke bank untuk beli mesin baru), maka:

```
Ekuitas = Rp20.000.000 (aset) − Rp5.000.000 (utang) = Rp15.000.000
```

Ekuitas inilah yang sebenarnya "milikmu" — bukan seluruh aset, karena sebagian aset itu dibiayai oleh utang yang harus dibayar kembali.

## Kenapa Ini Penting untuk Sistem Kita?

Ekuitas adalah salah satu angka paling penting dalam laporan keuangan perusahaan (kita akan bahas mendalam di Modul 4). Nanti di Modul 5, kamu akan belajar rasio **ROE (Return on Equity)** — yang secara harfiah mengukur seberapa efektif sebuah perusahaan menghasilkan laba dari ekuitas pemiliknya. ROE ini menjadi salah satu komponen dalam skor fundamental yang dipakai mesin rekomendasi kita.

## Latihan

Sebuah bisnis warung punya aset senilai Rp50.000.000 dan utang ke bank sebesar Rp20.000.000. Ada dua pemilik: kamu (kontribusi modal 70%) dan Sari (kontribusi modal 30%).

1. Berapa total ekuitas bisnis ini?
2. Berapa nilai ekuitas milikmu?
MD,
        ];
    }

    private function lessonApaItuSaham(): array
    {
        return [
            'slug' => 'apa-itu-saham',
            'title' => 'Apa Itu Saham?',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan saham sebagai pecahan kecil dari kepemilikan perusahaan',
                'Memahami hubungan antara jumlah saham beredar dan kapitalisasi pasar',
                'Menghitung kapitalisasi pasar sederhana dari harga saham dan jumlah saham beredar',
            ],
            'key_terms' => ['saham', 'saham-beredar', 'kapitalisasi-pasar'],
            'summary' => 'Saham adalah kepemilikan perusahaan yang dipecah menjadi jutaan/miliaran unit kecil agar mudah dijual-belikan. Harga satu saham dikali jumlah saham beredar = kapitalisasi pasar, estimasi nilai total perusahaan menurut pasar.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Ini bab intinya: setelah paham perusahaan dan kepemilikan, sekarang kita sampai ke pertanyaan judul modul ini — **apa itu saham, sebenarnya?**

## Dari Warung ke Perusahaan Raksasa

Di dua pelajaran sebelumnya, kepemilikan warung kopi dibagi antara kamu dan Sari dengan persentase sederhana (66,7% dan 33,3%). Itu gampang diatur karena cuma ada dua pemilik.

Sekarang bayangkan warung kopimu sukses besar, tumbuh menjadi perusahaan nasional dengan ribuan gerai. Untuk berkembang lebih jauh, kamu butuh modal sangat besar — lebih besar dari yang bisa ditanggung kamu dan Sari berdua. Solusinya: mengajak **banyak orang** untuk ikut memiliki sebagian kecil perusahaan ini.

Tapi mustahil mengatur kepemilikan dengan persentase rumit untuk ribuan pemilik ("kamu punya 0,00034%, dia punya 0,00012%..."). Solusinya: kepemilikan perusahaan dipecah menjadi **unit-unit kecil yang seragam** — inilah yang disebut **Saham (Stock/Share)**.

## Satu Saham = Satu Kepingan Kecil Kepemilikan

Katakanlah perusahaan warung kopimu memutuskan membagi total kepemilikannya menjadi **1.000.000 lembar saham**. Setiap lembar saham mewakili:

```
1 saham = 1 / 1.000.000 dari kepemilikan perusahaan
```

Kalau kamu memegang 250.000 lembar dari total 1.000.000 lembar itu, berarti kamu memiliki 25% perusahaan — sama persis konsepnya dengan kepemilikan 66,7% di Pelajaran 2, hanya dinyatakan dalam bentuk yang jauh lebih mudah dijual, dibeli, dan dibagi ke banyak orang.

Total jumlah lembar saham yang diterbitkan perusahaan disebut **Saham Beredar (Shares Outstanding)**.

## Berapa Nilai Perusahaan Menurut Pasar?

Kalau saham perusahaanmu diperjualbelikan di harga Rp5.000 per lembar, dan total saham beredar 1.000.000 lembar, maka nilai total perusahaan menurut pasar adalah:

```
Kapitalisasi Pasar = Harga per Saham × Jumlah Saham Beredar
                    = Rp5.000 × 1.000.000
                    = Rp5.000.000.000 (Rp5 miliar)
```

Angka ini disebut **Kapitalisasi Pasar (Market Capitalization)**, sering disingkat "market cap". Ini adalah estimasi nilai total sebuah perusahaan menurut harga yang disepakati pasar saat ini — bukan nilai pasti, karena harga saham bisa naik-turun setiap hari.

Sebagai perbandingan nyata: BBCA (Bank Central Asia) punya kapitalisasi pasar yang termasuk terbesar di Bursa Efek Indonesia, mencerminkan besarnya bisnis dan kepercayaan pasar terhadap perusahaan tersebut.

## Kenapa Ini Penting untuk Sistem Kita?

Buka halaman [Saham](/stocks) di aplikasi ini — kolom "Harga Terakhir" pada setiap baris adalah harga per satu lembar saham. Kalau kamu kalikan dengan jumlah saham beredar perusahaan tersebut (data yang belum kita tampilkan di versi awal ini), kamu akan dapat estimasi kapitalisasi pasarnya. Ukuran market cap ini nantinya relevan untuk membandingkan perusahaan besar (blue chip) vs kecil (small cap), yang punya karakteristik risiko berbeda — dibahas di Modul 8.

## Latihan

Sebuah perusahaan fiktif, "PT Kopi Nusantara Tbk", punya 400.000.000 lembar saham beredar. Harga sahamnya saat ini Rp2.500 per lembar.

Berapa estimasi kapitalisasi pasar perusahaan ini?
MD,
        ];
    }

    private function lessonMengapaPerusahaanMenjualSaham(): array
    {
        return [
            'slug' => 'mengapa-perusahaan-menjual-saham',
            'title' => 'Mengapa Perusahaan Menjual Saham?',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Menjelaskan alasan utama perusahaan melakukan IPO',
                'Membedakan perusahaan publik dan perusahaan privat',
                'Memahami apa itu pasar primer',
            ],
            'key_terms' => ['ipo', 'perusahaan-publik', 'pasar-primer'],
            'summary' => 'Perusahaan menjual saham ke publik (IPO) terutama untuk mendapatkan modal besar demi berkembang, tanpa harus berutang. Setelah itu perusahaan disebut perusahaan publik dan sahamnya bisa dibeli siapa saja di bursa.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Kalau saham adalah pecahan kepemilikan perusahaan, pertanyaan wajar berikutnya: kenapa pemilik perusahaan mau membagi-bagikan kepemilikannya ke orang asing di pasar? Bukannya itu berarti mereka jadi "punya" bisnismu lebih sedikit?

## Balik ke Contoh Warung Kopi

Ingat, perusahaan warung kopimu ingin ekspansi besar-besaran — buka ribuan gerai baru. Untuk itu, kamu butuh dana sangat besar, katakanlah Rp500 miliar. Ada beberapa cara mendapatkan dana sebesar itu:

1. **Berutang ke bank** — tapi utang sebesar itu berarti bunga besar yang harus dibayar rutin, dan risiko gagal bayar kalau bisnis melambat.
2. **Menjual sebagian kepemilikan ke publik** — kamu "menukar" sebagian persentase kepemilikanmu dengan uang tunai dari investor, TANPA kewajiban membayar bunga atau mengembalikan uang itu nanti.

Opsi kedua inilah yang disebut **Penawaran Umum Perdana / Initial Public Offering (IPO)** — proses pertama kali sebuah perusahaan menjual sahamnya kepada masyarakat umum melalui bursa saham.

## Sebelum dan Sesudah IPO

Sebelum IPO, perusahaanmu adalah **Perusahaan Privat**: sahamnya hanya dipegang oleh segelintir orang (kamu, Sari, mungkin beberapa investor awal) dan tidak bisa dibeli sembarang orang.

Setelah IPO, perusahaanmu menjadi **Perusahaan Publik** (biasanya ditandai dengan akhiran "Tbk." di Indonesia, singkatan dari "Terbuka") — sahamnya kini tercatat di bursa dan bisa dibeli siapa saja yang punya rekening efek, dari investor individu sampai institusi besar.

Saat IPO, saham dijual pertama kali langsung dari perusahaan ke investor. Transaksi jual-beli saham baru ini terjadi di apa yang disebut **Pasar Primer (Primary Market)** — uang hasil penjualan saham ini benar-benar masuk ke kas perusahaan untuk dipakai ekspansi.

Ini berbeda dengan transaksi jual-beli saham sehari-hari di bursa (misalnya kamu beli saham BBCA hari ini) — itu terjadi di **Pasar Sekunder**, yang akan kita bahas lebih detail di Pelajaran 6, di mana uangnya berpindah antar-investor, bukan masuk ke kas perusahaan lagi.

## Kenapa Perusahaan Mau "Berbagi" Kepemilikan?

Karena bagi banyak perusahaan, mendapatkan modal besar tanpa beban utang jauh lebih berharga dibanding mempertahankan 100% kepemilikan pada bisnis yang pertumbuhannya jadi terbatas. Lebih baik memiliki 60% dari perusahaan senilai Rp10 triliun, daripada memiliki 100% dari perusahaan yang cuma bernilai Rp1 triliun karena tidak pernah dapat modal untuk berkembang.

## Kenapa Ini Penting untuk Sistem Kita?

Semua ticker yang kamu lihat di halaman [Saham](/stocks) — BBCA, TLKM, ASII, dan lainnya — adalah perusahaan yang sudah melewati proses IPO dan kini berstatus perusahaan publik (Tbk.) di Bursa Efek Indonesia (IDX). Data `listed_at` (tanggal pencatatan) pada setiap saham di sistem kita menandai kapan perusahaan tersebut resmi menjadi perusahaan publik.

## Latihan (Benar/Salah)

"Ketika kamu membeli saham BBCA hari ini di aplikasi sekuritas, uang yang kamu bayarkan langsung masuk ke kas PT Bank Central Asia Tbk."

Benar atau salah? Jelaskan alasannya.
MD,
        ];
    }

    private function lessonMengapaInvestorMembeliSaham(): array
    {
        return [
            'slug' => 'mengapa-investor-membeli-saham',
            'title' => 'Mengapa Investor Membeli Saham?',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Menyebutkan dua sumber utama keuntungan dari memiliki saham',
                'Menjelaskan apa itu dividen dengan bahasa sederhana',
                'Menjelaskan apa itu capital gain dengan bahasa sederhana',
            ],
            'key_terms' => ['dividen', 'capital-gain'],
            'summary' => 'Investor membeli saham untuk mengejar dua hal: dividen (bagian laba yang dibagikan tunai) dan capital gain (selisih untung dari kenaikan harga saham). Keduanya bisa didapat sekaligus atau salah satu saja, tergantung strategi dan perusahaan.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Kita sudah tahu perusahaan menjual saham untuk dapat modal. Sekarang dari sisi sebaliknya: **kenapa orang mau membeli saham itu?** Apa untungnya bagi investor?

## Kembali ke Warung Kopi yang Sudah Jadi Perusahaan Publik

Anggap "PT Kopi Nusantara Tbk" (kelanjutan cerita warung kopimu) sudah IPO. Sari, yang dulu ikut membangun bisnis ini, memutuskan tetap memegang sahamnya sebagai investor. Ada dua cara Sari bisa untung dari sini.

## Cara Pertama: Dividen

Setiap tahun, kalau perusahaan untung, manajemen bisa memutuskan membagikan sebagian labanya secara tunai kepada seluruh pemegang saham, sesuai proporsi kepemilikan masing-masing. Pembagian laba tunai ini disebut **Dividen (Dividend)**.

Misalnya, PT Kopi Nusantara Tbk mencatat laba tahun ini, dan manajemen memutuskan membagikan dividen Rp50 per saham. Kalau Sari memegang 100.000 lembar saham, dia menerima:

```
Dividen Sari = Rp50 × 100.000 lembar = Rp5.000.000
```

Uang ini masuk langsung ke rekening Sari, tanpa dia perlu menjual satu pun sahamnya. Ia tetap jadi pemilik 100.000 lembar saham setelahnya.

Catatan penting: **tidak semua perusahaan membagikan dividen setiap tahun**. Perusahaan yang sedang fokus tumbuh besar-besaran sering memilih menahan seluruh labanya untuk diinvestasikan kembali ke bisnis, bukan dibagikan.

## Cara Kedua: Capital Gain

Cara kedua adalah dari **kenaikan harga saham itu sendiri**. Misalnya Sari beli sahamnya dulu di harga Rp5.000 per lembar. Beberapa tahun kemudian, karena bisnis tumbuh pesat, harga sahamnya naik jadi Rp8.000 per lembar.

Kalau Sari menjual 10.000 lembar sahamnya sekarang:

```
Modal awal   = Rp5.000 × 10.000 lembar = Rp50.000.000
Nilai jual   = Rp8.000 × 10.000 lembar = Rp80.000.000
Capital Gain = Rp80.000.000 − Rp50.000.000 = Rp30.000.000
```

Selisih untung dari perbedaan harga beli dan harga jual ini disebut **Capital Gain**. (Kalau harga jual lebih rendah dari harga beli, selisih ruginya disebut **Capital Loss** — dibahas lebih dalam di Pelajaran 7.)

## Kenapa Harga Saham Bisa Naik?

Secara sederhana: harga saham cenderung naik ketika semakin banyak orang percaya perusahaan itu akan makin bernilai di masa depan (lebih untung, tumbuh lebih besar) — sehingga mereka mau membayar lebih mahal untuk memilikinya. Ini akan kita bahas lebih formal lewat konsep **Valuasi** di Modul 6.

## Kenapa Ini Penting untuk Sistem Kita?

Halaman [Detail Saham](/stocks) di aplikasi ini menampilkan **perubahan harga (change)** dari hari ke hari — itu adalah cerminan langsung dari potensi capital gain/loss harian. Ke depan, saat Modul 5 dan 6 sudah tersedia, kamu akan belajar metrik seperti **Dividend Yield** yang mengukur seberapa besar dividen relatif terhadap harga saham — salah satu komponen skor valuasi di mesin rekomendasi kita.

## Latihan

Kamu membeli 500 lembar saham di harga Rp3.200 per lembar. Setahun kemudian, perusahaan membagikan dividen Rp80 per saham, dan harga sahamnya naik menjadi Rp3.600.

1. Berapa total dividen yang kamu terima?
2. Berapa capital gain kamu (di atas kertas, jika belum dijual) dari kenaikan harga?
MD,
        ];
    }

    private function lessonBagaimanaInvestorUntung(): array
    {
        return [
            'slug' => 'bagaimana-investor-menghasilkan-uang',
            'title' => 'Bagaimana Investor Menghasilkan Uang',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan mekanisme pasar sekunder tempat investor jual-beli saham',
                'Menghitung total return investasi (dividen + capital gain)',
                'Membedakan keuntungan "di atas kertas" dan keuntungan yang sudah terealisasi',
            ],
            'key_terms' => ['pasar-sekunder', 'return'],
            'summary' => 'Sebagian besar transaksi saham terjadi di pasar sekunder, antar-investor, lewat bursa. Total keuntungan investor (return) adalah gabungan dividen dan capital gain, tapi keuntungan baru "terealisasi" ketika saham benar-benar dijual.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Pelajaran sebelumnya menjelaskan DUA CARA investor untung (dividen dan capital gain). Sekarang kita satukan keduanya, dan pahami DI MANA sebenarnya transaksi jual-beli saham sehari-hari terjadi.

## Mengingat Kembali: Pasar Primer vs Pasar Sekunder

Di Pelajaran 4, kita sebut bahwa saat IPO, transaksi terjadi di **Pasar Primer** — uang dari investor masuk langsung ke kas perusahaan.

Tapi begitu IPO selesai, hampir semua transaksi jual-beli saham selanjutnya — termasuk yang kamu lakukan setiap hari lewat aplikasi sekuritas — terjadi di **Pasar Sekunder**. Di pasar sekunder, kamu tidak membeli saham dari perusahaan, tapi dari **investor lain** yang sedang menjual sahamnya. Begitu juga sebaliknya: kalau kamu jual saham, pembelinya adalah investor lain, bukan perusahaan.

```
Pasar Primer:    Perusahaan  →  jual saham pertama kali  →  Investor Awal
Pasar Sekunder:  Investor A  ↔  jual-beli saham          ↔  Investor B
```

Bursa Efek Indonesia (IDX/BEI) pada dasarnya adalah tempat pasar sekunder ini beroperasi — mempertemukan jutaan investor yang ingin membeli dan menjual saham, setiap hari kerja.

## Menggabungkan Dividen + Capital Gain = Return

Total keuntungan investor dari sebuah investasi saham, menggabungkan dividen yang diterima DAN capital gain dari kenaikan harga, punya istilah formal: **Return** (imbal hasil).

Contoh lengkap: kamu beli saham di harga Rp8.400 per lembar (1.000 lembar, total modal Rp8.400.000). Setahun kemudian:

- Kamu menerima dividen total Rp100 per saham → Rp100.000
- Harga saham naik menjadi Rp9.200 per lembar

Kalau dihitung dalam persentase return:

```
Return dari Dividen      = Rp100.000 / Rp8.400.000       ≈ 1,19%
Return dari Capital Gain = (Rp9.200 − Rp8.400) / Rp8.400 ≈ 9,52%
Total Return             ≈ 1,19% + 9,52% = 10,71%
```

## "Di Atas Kertas" vs Sudah Terealisasi

Ini konsep yang sering bikin bingung pemula: selama kamu **belum menjual** sahammu, kenaikan harga itu baru disebut keuntungan **"di atas kertas" (unrealized/paper gain)** — belum benar-benar jadi uang di tanganmu, dan masih bisa berubah (naik lagi, atau malah turun) esok hari.

Keuntungan baru **terealisasi (realized)** — benar-benar menjadi uang tunai — begitu kamu benar-benar menjual saham tersebut di harga yang lebih tinggi dari harga belimu.

Ini penting karena banyak investor pemula panik menjual di saat harga turun sedikit (padahal rugi itu juga masih "di atas kertas"), atau terlalu percaya diri karena portofolionya "untung besar" padahal belum pernah dijual sama sekali.

## Kenapa Ini Penting untuk Sistem Kita?

Angka "change" dan "change_percent" yang kamu lihat di halaman [Detail Saham](/stocks) pada dasarnya adalah potensi return harian yang MASIH di atas kertas — bukan uang yang sudah pasti kamu kantongi. Nanti di Modul 11 (Backtesting), kita akan belajar bagaimana mengukur return sebuah strategi secara ilmiah dari data historis.

## Latihan

Kamu beli saham seharga Rp4.000 per lembar. Sekarang harganya Rp4.500, tapi kamu belum menjualnya sama sekali.

Apakah keuntunganmu sudah "terealisasi" atau masih "di atas kertas"? Jelaskan kenapa perbedaan ini penting.
MD,
        ];
    }

    private function lessonBagaimanaInvestorRugi(): array
    {
        return [
            'slug' => 'bagaimana-investor-bisa-rugi',
            'title' => 'Bagaimana Investor Bisa Rugi',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan bagaimana penurunan harga saham menyebabkan kerugian',
                'Memahami risiko ekstrem: perusahaan bangkrut atau delisting',
                'Mengenal istilah risiko dan volatilitas secara sederhana (akan diperdalam di Modul 8)',
            ],
            'key_terms' => ['risiko', 'volatilitas'],
            'summary' => 'Investor bisa rugi lewat penurunan harga (capital loss) hingga skenario terburuk perusahaan bangkrut/delisting. Volatilitas — seberapa liar harga naik-turun — adalah salah satu ukuran risiko yang akan kita pelajari lebih dalam nanti.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Sejauh ini kita banyak bicara soal untung. Tapi investasi saham juga punya risiko nyata kehilangan uang — dan memahami risiko ini SEBELUM mulai berinvestasi jauh lebih penting daripada membayangkan keuntungannya saja.

## Skenario 1: Capital Loss

Ini kebalikan dari capital gain di Pelajaran 5. Kalau kamu beli saham di harga Rp5.000 per lembar, lalu harganya turun ke Rp3.500 dan kamu menjualnya di harga itu:

```
Modal awal  = Rp5.000 × 1.000 lembar = Rp5.000.000
Nilai jual  = Rp3.500 × 1.000 lembar = Rp3.500.000
Capital Loss = Rp5.000.000 − Rp3.500.000 = Rp1.500.000
```

Sama seperti capital gain, kerugian ini baru **terealisasi** kalau kamu benar-benar menjual di harga rendah itu. Kalau kamu tidak menjual dan menunggu harga pulih, kerugian itu masih "di atas kertas" — bisa saja kembali untung nanti, atau malah tambah rugi.

## Kenapa Harga Saham Bisa Turun?

Harga saham bisa turun karena berbagai alasan, misalnya:

- Kinerja bisnis perusahaan memburuk (laba turun, rugi)
- Kondisi ekonomi makro memburuk (suku bunga naik, resesi)
- Sentimen pasar negatif terhadap sektor atau saham tertentu
- Berita buruk spesifik tentang perusahaan (skandal, gugatan hukum, dsb.)

## Skenario Terburuk: Perusahaan Bangkrut atau Delisting

Ini risiko paling ekstrem. Kalau sebuah perusahaan publik gagal total menjalankan bisnisnya — bangkrut — sahamnya bisa kehilangan hampir seluruh nilainya, bahkan sampai dikeluarkan dari bursa (disebut **delisting**). Dalam skenario ini, pemegang saham bisa kehilangan hampir seluruh modal yang diinvestasikan.

Ini alasan kenapa **diversifikasi** (tidak menaruh seluruh uangmu di satu saham saja) menjadi prinsip penting — akan kita bahas mendalam di Modul 8 dan 9.

## Mengenal Sekilas: Volatilitas

Beberapa saham harganya relatif stabil dari hari ke hari. Yang lain bisa naik-turun tajam dalam hitungan jam. Seberapa liar pergerakan harga suatu saham disebut **Volatilitas (Volatility)**.

Saham dengan volatilitas tinggi punya potensi capital gain yang lebih besar dalam waktu singkat — tapi juga potensi capital loss yang sama besarnya. Volatilitas adalah salah satu cara formal untuk mengukur **Risiko (Risk)**: ketidakpastian tentang seberapa jauh hasil investasimu bisa menyimpang dari yang kamu harapkan, baik ke atas maupun ke bawah.

Kita baru menyentuh permukaan konsep risiko di sini — pembahasan lengkap tentang volatilitas, standar deviasi, dan *maximum drawdown* ada di Modul 8.

## Kenapa Ini Penting untuk Sistem Kita?

Mesin rekomendasi kita nantinya akan punya **Skor Risiko (Risk Score)** sebagai salah satu dari lima komponen penilaian (lihat Modul 13). Skor ini, antara lain, dibangun dari data historis volatilitas harga — persis seperti yang kamu lihat di grafik harga pada halaman [Detail Saham](/stocks).

## Latihan (Interpretasi)

Saham A biasanya bergerak ±1% per hari. Saham B biasanya bergerak ±8% per hari.

Manakah yang memiliki volatilitas lebih tinggi, dan apa artinya itu bagi risiko yang kamu tanggung sebagai investor?
MD,
        ];
    }

    private function lessonSahamVsAsetLain(): array
    {
        return [
            'slug' => 'saham-vs-aset-lain',
            'title' => 'Saham vs Aset Lain',
            'estimated_minutes' => 9,
            'learning_objectives' => [
                'Membandingkan karakteristik saham dengan tabungan/deposito, emas, properti, dan obligasi',
                'Menjelaskan trade-off antara return, risiko, dan likuiditas',
                'Menyimpulkan mengapa tidak ada aset yang "paling baik" secara mutlak',
            ],
            'key_terms' => ['obligasi', 'likuiditas'],
            'summary' => 'Setiap jenis aset (saham, deposito, emas, properti, obligasi) punya kombinasi berbeda antara potensi return, risiko, dan likuiditas. Tidak ada yang "terbaik" secara mutlak — semua tergantung tujuan dan toleransi risiko masing-masing investor.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Ini pelajaran penutup Modul 1. Setelah memahami apa itu saham dan bagaimana untung-ruginya, wajar untuk bertanya: **kenapa harus saham? Bagaimana dibandingkan pilihan investasi lain?**

## Empat Jenis Aset untuk Dibandingkan

Mari bandingkan saham dengan tiga jenis aset populer lainnya di Indonesia:

### 1. Tabungan / Deposito

Uangmu disimpan di bank, dapat bunga tetap (untuk deposito) yang sudah diketahui besarnya sejak awal. Sangat aman (dijamin LPS hingga batas tertentu), tapi bunganya kecil — biasanya tidak jauh berbeda dari laju inflasi, jadi nilai riil uangmu nyaris tidak bertumbuh.

### 2. Emas

Harga emas cenderung naik dalam jangka panjang dan dianggap "aman" saat kondisi ekonomi tidak menentu. Tapi emas tidak menghasilkan apa pun selama kamu memegangnya (tidak ada "dividen emas") — keuntungannya murni dari kenaikan harga.

### 3. Properti (Tanah/Rumah)

Bisa menghasilkan pendapatan sewa (mirip dividen) DAN naik nilainya (mirip capital gain). Tapi butuh modal besar di awal, dan sulit dijual cepat kalau butuh uang mendadak — kamu tidak bisa menjual "setengah kamar tidur" saja.

### 4. Obligasi

**Obligasi (Bond)** pada dasarnya adalah surat utang: kamu meminjamkan uang ke penerbitnya (pemerintah atau perusahaan), dan sebagai gantinya kamu menerima bunga tetap secara berkala, plus pengembalian pokok utang di akhir periode. Berbeda dari saham, dengan obligasi kamu bukan pemilik perusahaan — kamu adalah pemberi pinjaman. Umumnya risikonya lebih rendah dari saham, tapi potensi keuntungannya juga lebih terbatas.

## Konsep Baru: Likuiditas

Salah satu perbedaan besar antar aset di atas adalah **Likuiditas (Liquidity)** — seberapa cepat dan mudah sebuah aset bisa diubah menjadi uang tunai tanpa kehilangan banyak nilai.

- Saham (terutama yang aktif diperdagangkan): likuiditas tinggi — bisa dijual dalam hitungan detik di jam bursa buka
- Deposito: likuiditas sedang — bisa dicairkan, tapi biasanya kena penalti kalau sebelum jatuh tempo
- Properti: likuiditas rendah — bisa butuh berbulan-bulan mencari pembeli dengan harga wajar

## Membandingkan Semuanya

| Aset | Potensi Return | Risiko | Likuiditas |
|---|---|---|---|
| Tabungan/Deposito | Rendah | Sangat Rendah | Sedang–Tinggi |
| Obligasi | Rendah–Sedang | Rendah–Sedang | Sedang |
| Saham | Sedang–Tinggi | Sedang–Tinggi | Tinggi |
| Emas | Sedang | Sedang | Tinggi |
| Properti | Sedang–Tinggi | Sedang | Rendah |

Pola yang selalu berulang di dunia investasi: **potensi return yang lebih tinggi hampir selalu datang bersama risiko yang lebih tinggi juga.** Tidak ada aset ajaib dengan return tinggi tapi risiko nol — kalau ada yang menawarkan itu, patut dicurigai.

## Kenapa Ini Penting untuk Sistem Kita?

Modul 8 dan 9 nanti akan membahas bagaimana menyeimbangkan berbagai aset (termasuk kombinasi saham-saham berbeda) dalam satu portofolio untuk mengelola trade-off return-vs-risiko ini secara lebih terukur, bukan cuma berdasarkan feeling.

## Penutup Modul 1

Sampai di sini, kamu sudah paham fondasi paling dasar: apa itu perusahaan, kepemilikan, saham, kenapa perusahaan menjualnya, kenapa investor membelinya, dan bagaimana untung-ruginya bekerja. Modul 2 (akan datang) akan membawa kita ke bagaimana mekanisme jual-beli saham sesungguhnya bekerja di Bursa Efek Indonesia — order, bid-ask, lot, dan seterusnya.

## Latihan (Skenario)

Kamu punya Rp20 juta dan sedang mempertimbangkan dua pilihan: (A) deposito bank dengan bunga tetap 5% per tahun, atau (B) saham yang secara historis rata-rata naik 12% per tahun tapi harganya bisa turun tajam sewaktu-waktu.

Faktor apa saja yang sebaiknya kamu pertimbangkan sebelum memilih antara keduanya?
MD,
        ];
    }

    private function lessonApaItuBursaEfek(): array
    {
        return [
            'slug' => 'apa-itu-bursa-efek',
            'title' => 'Apa Itu Bursa Efek?',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Menjelaskan fungsi bursa efek sebagai tempat bertemunya pembeli dan penjual saham',
                'Menyebutkan nama bursa saham resmi di Indonesia',
                'Memahami peran otoritas pengawas dalam menjaga transaksi tetap adil',
            ],
            'key_terms' => ['bursa-efek'],
            'summary' => 'Bursa Efek adalah pasar terpusat dan diawasi tempat jutaan investor bertemu untuk jual-beli saham. Di Indonesia, bursa resminya adalah Bursa Efek Indonesia (IDX/BEI).',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Modul 1 sudah menjelaskan APA itu saham. Sekarang kita masuk ke Modul 2: DI MANA dan BAGAIMANA saham itu benar-benar diperjualbelikan setiap hari. Ini fondasi sebelum kamu bisa memahami harga, order, dan data yang kita tampilkan di aplikasi ini.

## PT Kopi Nusantara Tbk, Setelah IPO

Ingat PT Kopi Nusantara Tbk dari Modul 1 — sudah resmi IPO dan menjadi perusahaan publik. Sahamnya sekarang dipegang oleh ribuan investor berbeda. Pertanyaannya: kalau salah satu investor itu, katakanlah namanya Budi, ingin menjual sahamnya besok pagi, ke mana dia harus pergi? Dan kalau ada investor lain, Wati, ingin membeli saham yang sama di hari yang sama, bagaimana mereka berdua bisa saling menemukan?

Kalau tidak ada tempat terpusat, ini akan kacau — Budi harus mencari pembeli sendiri-sendiri, tidak ada jaminan harga yang wajar, dan rawan penipuan.

## Solusinya: Bursa Efek

**Bursa Efek (Stock Exchange)** adalah pasar terpusat dan resmi tempat semua order beli dan jual saham dari seluruh investor di sebuah negara dipertemukan dalam satu sistem yang sama, pada saat yang sama, dengan aturan yang sama untuk semua orang.

Bayangkan seperti pasar tradisional raksasa, tapi alih-alih sayur dan ikan, yang diperjualbelikan adalah saham — dan alih-alih tawar-menawar langsung di lapak, semuanya dicatat dan dicocokkan lewat sistem komputer yang diawasi ketat.

Di Indonesia, bursa resminya bernama **Bursa Efek Indonesia**, disingkat **IDX** (Indonesia Stock Exchange) atau kadang disebut **BEI**, berkedudukan di Jakarta. Ini satu-satunya bursa saham resmi di Indonesia — semua ticker yang kamu lihat di aplikasi ini (BBCA, TLKM, ASII, dan lainnya) diperdagangkan di sana.

## Kenapa Butuh Pengawasan?

Karena uang banyak orang dipertaruhkan di sini, bursa efek tidak bisa dibiarkan berjalan tanpa aturan. Di Indonesia, **Otoritas Jasa Keuangan (OJK)** mengawasi keseluruhan industri pasar modal, memastikan perusahaan yang IPO benar-benar transparan soal kondisi keuangannya, dan transaksi berjalan adil untuk semua pihak — investor kecil maupun besar.

## Kenapa Ini Penting untuk Sistem Kita?

Kolom **Exchange** yang muncul di setiap kartu saham pada halaman [Saham](/stocks) selalu bertuliskan "IDX" — karena seluruh data yang kita tampilkan berasal dari saham-saham yang tercatat resmi di Bursa Efek Indonesia.

## Latihan

Kenapa investor tidak bisa (dan tidak boleh) jual-beli saham langsung satu sama lain tanpa lewat bursa resmi seperti IDX? Sebutkan minimal satu alasan.
MD,
        ];
    }

    private function lessonBrokerDanRekeningEfek(): array
    {
        return [
            'slug' => 'broker-dan-rekening-efek',
            'title' => 'Broker dan Rekening Efek',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan mengapa investor perorangan butuh perusahaan sekuritas untuk bertransaksi',
                'Menjelaskan fungsi Rekening Dana Nasabah (RDN)',
                'Memahami bahwa aplikasi ini bukan aplikasi untuk transaksi jual-beli sungguhan',
            ],
            'key_terms' => ['broker', 'rekening-dana-nasabah'],
            'summary' => 'Investor perorangan tidak bisa langsung mengakses sistem bursa — mereka butuh perantara resmi bernama Perusahaan Sekuritas (broker), dan dana mereka disimpan terpisah di Rekening Dana Nasabah (RDN).',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Sekarang kita tahu Bursa Efek Indonesia adalah tempat semua transaksi saham terjadi. Tapi Budi (investor dari pelajaran sebelumnya) tidak bisa datang langsung ke gedung IDX dan bilang "saya mau jual saham". Lalu bagaimana caranya dia benar-benar bertransaksi?

## Perlu "Makelar" Resmi

Sistem bursa hanya bisa diakses oleh anggota resmi yang punya izin khusus — bukan investor perorangan biasa. Untuk itu, Budi harus melewati perantara yang disebut **Perusahaan Sekuritas**, atau lebih umum dikenal sebagai **Broker**.

Broker adalah perusahaan berizin yang menjadi jembatan antara investor perorangan seperti Budi dan sistem bursa. Ketika Budi memasukkan order lewat aplikasi sekuritas di ponselnya, order itu diteruskan oleh broker ke sistem IDX untuk dicocokkan dengan order dari investor lain.

Ini mirip seperti kamu titip barang ke makelar di pasar besar karena kamu sendiri tidak punya akses/izin untuk berjualan langsung di dalam pasar itu — broker berperan sebagai makelar resmi ini.

## Uangmu Disimpan di Mana? Rekening Dana Nasabah (RDN)

Sebelum bisa membeli saham, Budi harus menyetor uang ke sebuah rekening khusus bernama **Rekening Dana Nasabah (RDN)** — rekening bank yang terpisah dari rekening operasional broker itu sendiri, dan atas nama Budi sendiri (bukan atas nama brokernya).

Kenapa harus terpisah? Ini untuk melindungi investor — kalaupun perusahaan sekuritasnya bermasalah secara bisnis, uang milik nasabah di RDN tetap aman karena bukan bagian dari aset perusahaan sekuritas tersebut.

Alur sederhananya:

```
Setor uang ke RDN → Beli saham lewat broker → Saham tercatat atas namamu
                                                di penitipan efek terpusat (KSEI)
```

## Penting: Aplikasi Ini Bukan Aplikasi Trading

Aplikasi Stock Recommendation yang sedang kamu pakai ini **murni untuk riset, belajar, dan melihat data** — bukan aplikasi sekuritas. Kamu tidak bisa benar-benar membeli atau menjual saham lewat sini. Untuk benar-benar bertransaksi, kamu perlu membuka rekening di perusahaan sekuritas resmi yang terdaftar di OJK.

## Kenapa Ini Penting untuk Sistem Kita?

Memahami alur ini penting supaya kamu tidak bingung: fitur "Watchlist" dan "Rekomendasi" di aplikasi kita adalah alat bantu analisis, bukan tombol beli/jual sungguhan. Keputusan transaksi tetap kamu lakukan lewat aplikasi sekuritas resmi pilihanmu.

## Latihan (Benar/Salah)

"Uang yang kamu setor untuk membeli saham disimpan langsung sebagai milik perusahaan sekuritas tempat kamu membuka akun."

Benar atau salah? Jelaskan.
MD,
        ];
    }

    private function lessonApaItuLot(): array
    {
        return [
            'slug' => 'apa-itu-lot',
            'title' => 'Apa Itu Lot?',
            'estimated_minutes' => 6,
            'learning_objectives' => [
                'Menjelaskan definisi 1 lot di Bursa Efek Indonesia',
                'Menghitung nilai transaksi minimum pembelian sebuah saham',
            ],
            'key_terms' => ['lot'],
            'summary' => 'Di IDX, saham tidak dibeli per lembar, melainkan per satuan Lot, di mana 1 Lot = 100 lembar saham. Ini menentukan nilai minimum transaksi pembelian sebuah saham.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Setiap kali kamu melihat "Harga Terakhir" di halaman [Saham](/stocks), misalnya BBCA di harga IDR 6.350 — apakah itu berarti kamu bisa membeli hanya 1 lembar saham seharga Rp6.350? Ternyata tidak, dan pelajaran ini menjelaskan kenapa.

## Beli Telur per Kerat, Bukan per Butir

Bayangkan kamu ke pasar untuk beli telur. Biasanya penjual tidak melayani pembelian 1 butir telur saja — mereka jual per kerat (isi 30 butir). Kalau kamu mau beli telur, kamu harus beli minimal 1 kerat penuh.

Bursa Efek Indonesia menerapkan konsep serupa untuk saham, yang disebut **Lot**. Di IDX, satuan transaksi minimum untuk membeli atau menjual saham adalah:

```
1 Lot = 100 lembar saham
```

Kamu tidak bisa membeli, misalnya, 37 lembar saham BBCA saja — pembelian harus dalam kelipatan lot (100, 200, 300 lembar, dan seterusnya).

## Menghitung Nilai Transaksi

Kalau harga saham BBCA adalah Rp6.350 per lembar, maka nilai minimum untuk membeli 1 lot saham BBCA adalah:

```
Nilai 1 Lot = Harga per Lembar × 100
            = Rp6.350 × 100
            = Rp635.000
```

Ini BELUM termasuk biaya transaksi (komisi broker), yang biasanya berkisar 0,1%–0,3% dari nilai transaksi untuk pembelian.

Kalau Budi ingin membeli 5 lot saham BBCA:

```
Total lembar  = 5 lot × 100 = 500 lembar
Nilai transaksi = 500 lembar × Rp6.350 = Rp3.175.000
```

## Kenapa Ini Penting untuk Sistem Kita?

Setiap angka "Harga Terakhir" yang kamu lihat di aplikasi ini — di halaman [Saham](/stocks) maupun [Detail Saham](/stocks) — selalu dalam satuan per lembar, bukan per lot. Ingat untuk mengalikannya dengan 100 (dan dengan jumlah lot yang kamu incar) kalau ingin membayangkan nilai transaksi sungguhan.

## Latihan

Harga saham TLKM saat ini Rp2.620 per lembar. Berapa nilai transaksi (belum termasuk biaya broker) untuk membeli 3 lot saham TLKM?
MD,
        ];
    }

    private function lessonBidAskDanSpread(): array
    {
        return [
            'slug' => 'bid-ask-dan-spread',
            'title' => 'Bid, Ask, dan Spread',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan apa itu harga bid dan harga ask/offer',
                'Menjelaskan bagaimana sebuah transaksi saham terjadi',
                'Menghitung spread antara bid dan ask',
            ],
            'key_terms' => ['bid', 'ask', 'spread'],
            'summary' => 'Harga saham terbentuk dari pertemuan harga bid (tawaran beli tertinggi) dan ask (tawaran jual terendah). Selisih keduanya disebut spread. Transaksi terjadi ketika ada order yang saling cocok.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Kita sering melihat "harga saham" seolah itu satu angka tunggal yang pasti. Padahal, di setiap saat selama jam bursa buka, sebenarnya ada DUA harga yang saling "tarik-menarik" — dan memahami ini adalah kunci memahami bagaimana harga penutupan (close) yang kita tampilkan di aplikasi ini benar-benar terbentuk.

## Tawar-Menawar di Pasar

Ingat cara tawar-menawar di pasar tradisional: penjual sayur pasang harga jual (misalnya "cabai Rp50.000/kg"), sementara pembeli menawar harga lebih rendah (misalnya "Rp45.000 boleh?"). Transaksi baru terjadi kalau keduanya sepakat di satu harga.

Bursa saham bekerja dengan logika serupa, tapi terformalisasi dalam dua istilah:

- **Bid**: harga tertinggi yang bersedia dibayar oleh calon PEMBELI saat itu.
- **Ask** (juga disebut **Offer**): harga terendah yang diminta oleh calon PENJUAL saat itu.

## Contoh Konkret

Misalkan pada suatu momen, untuk saham BBCA:

```
Bid (tawaran beli tertinggi) = Rp6.325
Ask (tawaran jual terendah)  = Rp6.350
```

Ini berarti: pembeli yang paling "berani" saat ini mau membayar maksimal Rp6.325, sementara penjual yang paling "murah hati" saat ini minta minimal Rp6.350. Karena keduanya belum ketemu, transaksi BELUM terjadi.

## Apa Itu Spread?

Selisih antara ask dan bid disebut **Spread**:

```
Spread = Ask − Bid
       = Rp6.350 − Rp6.325
       = Rp25
```

Saham yang aktif diperdagangkan (banyak peminat, seperti BBCA) biasanya punya spread kecil karena banyak pembeli-penjual berlomba mendekati harga wajar. Saham yang jarang diperdagangkan bisa punya spread jauh lebih lebar — tanda likuiditasnya rendah (ingat konsep Likuiditas dari Modul 1).

## Bagaimana Transaksi Terjadi?

Transaksi baru terjadi ketika ada pembeli yang mau membayar di harga ask (atau penjual yang mau melepas di harga bid) — dengan kata lain, salah satu pihak "mengalah" dan menyetujui harga pihak lain. Harga di mana transaksi TERAKHIR terjadi pada hari itu, saat bursa tutup, adalah yang kita catat sebagai harga **Close**.

## Kenapa Ini Penting untuk Sistem Kita?

Harga "Close" yang kamu lihat di kolom Harga Terakhir dan di tabel Riwayat Harga pada aplikasi ini BUKAN harga bid maupun ask — melainkan harga transaksi nyata terakhir yang benar-benar terjadi hari itu, hasil pertemuan bid dan ask.

## Latihan

Untuk saham ASII, bid saat ini Rp4.760 dan ask Rp4.780. Berapa spread-nya, dan apa yang bisa kamu simpulkan jika spread saham lain ternyata jauh lebih lebar, misalnya Rp200?
MD,
        ];
    }

    private function lessonJenisOrder(): array
    {
        return [
            'slug' => 'jenis-order-market-limit',
            'title' => 'Jenis-Jenis Order: Market vs Limit',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Membedakan Market Order dan Limit Order',
                'Memahami trade-off kecepatan vs kepastian harga di antara keduanya',
            ],
            'key_terms' => ['market-order', 'limit-order'],
            'summary' => 'Market Order dieksekusi segera di harga pasar terbaik yang tersedia (cepat, harga tidak pasti). Limit Order menunggu sampai harga incaranmu tercapai (harga pasti, waktu eksekusi tidak pasti).',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Setelah paham bid, ask, dan spread, sekarang: bagaimana caranya Budi benar-benar "memasukkan" keinginannya untuk beli atau jual ke dalam sistem bursa? Jawabannya lewat sebuah **Order**, dan ada dua jenis utama yang perlu kamu kenal.

## Order Tipe 1: Market Order

**Market Order** adalah perintah untuk membeli atau menjual SEGERA, di harga terbaik yang tersedia saat itu juga — tanpa menentukan harga spesifik.

Kalau Budi memasang market order beli untuk BBCA saat bid Rp6.325 dan ask Rp6.350, ordernya akan langsung tereksekusi di harga ask terdekat yang tersedia (sekitar Rp6.350), karena dia memilih "beli sekarang juga, berapa pun harganya yang wajar saat ini".

**Kelebihan**: hampir pasti langsung tereksekusi (kalau ada lawan transaksi).
**Kekurangan**: kamu tidak punya kendali pasti atas harga — bisa saja tereksekusi sedikit lebih mahal/murah dari yang kamu bayangkan, terutama di saham yang sedang bergerak cepat.

## Order Tipe 2: Limit Order

**Limit Order** adalah perintah untuk membeli atau menjual HANYA PADA harga tertentu (atau lebih baik) yang kamu tentukan sendiri.

Misalnya Budi memasang limit order beli BBCA di harga Rp6.300 — meskipun harga pasar saat ini Rp6.350. Order ini akan "menunggu" di sistem sampai ada penjual yang bersedia melepas di harga Rp6.300 atau lebih rendah. Kalau harga tidak pernah turun ke situ, order Budi tidak akan pernah tereksekusi.

**Kelebihan**: kamu punya kepastian penuh atas harga transaksi.
**Kekurangan**: tidak ada jaminan order akan tereksekusi sama sekali.

## Membandingkan Keduanya

| | Market Order | Limit Order |
|---|---|---|
| Kepastian eksekusi | Tinggi | Tidak pasti |
| Kepastian harga | Rendah | Tinggi |
| Cocok untuk | Butuh cepat masuk/keluar | Punya target harga spesifik |

## Kenapa Ini Penting untuk Sistem Kita?

Aplikasi ini tidak memproses order sungguhan (lihat Pelajaran 2), tapi memahami perbedaan ini penting supaya kamu tahu bahwa harga yang benar-benar kamu dapatkan saat bertransaksi lewat aplikasi sekuritas bisa berbeda dari harga "Close" hari sebelumnya yang kita tampilkan — tergantung jenis order dan kondisi pasar saat itu.

## Latihan (Skenario)

Budi sangat ingin memastikan dia berhasil membeli saham TLKM SEKARANG JUGA sebelum harga naik lebih jauh, dan tidak keberatan membayar sedikit lebih mahal. Order jenis apa yang paling cocok untuknya — market order atau limit order? Jelaskan alasannya.
MD,
        ];
    }

    private function lessonAutoRejectDanBatasHarga(): array
    {
        return [
            'slug' => 'auto-reject-dan-batas-harga',
            'title' => 'Auto Reject dan Batas Harga',
            'estimated_minutes' => 6,
            'learning_objectives' => [
                'Menjelaskan tujuan mekanisme Auto Reject di bursa',
                'Memahami bahwa harga saham tidak bisa bergerak tak terbatas dalam sehari',
            ],
            'key_terms' => ['auto-reject'],
            'summary' => 'Auto Reject adalah batas kenaikan/penurunan harga maksimum yang diizinkan bursa dalam satu hari perdagangan, untuk mencegah volatilitas ekstrem dan potensi manipulasi harga.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Pernahkah kamu bertanya-tanya kenapa harga saham di aplikasi ini tidak pernah melonjak, katakanlah, +500% dalam satu hari (kecuali kasus tertentu seperti aksi korporasi)? Ini bukan kebetulan — ada aturan bursa yang secara sengaja membatasinya.

## Kenapa Harga Perlu Dibatasi?

Bayangkan tidak ada batasan sama sekali: sekelompok orang bisa saja beramai-ramai memborong saham kecil yang jarang diperdagangkan, mendorong harganya melonjak liar dalam hitungan menit, lalu menjualnya ke investor yang panik ikut membeli karena takut ketinggalan ("FOMO"). Ini merugikan investor awam dan merusak kepercayaan terhadap pasar.

Untuk mencegah ini, bursa menerapkan mekanisme yang disebut **Auto Reject** — batas maksimum kenaikan (disingkat **ARA**, Auto Reject Atas) dan penurunan (**ARB**, Auto Reject Bawah) harga yang diizinkan dalam satu hari perdagangan. Order yang mencoba bertransaksi di luar batas ini akan otomatis ditolak sistem.

Besaran persentase batas ARA/ARB berbeda-beda tergantung rentang harga saham, dan aturannya bisa berubah dari waktu ke waktu — untuk detail resmi dan terkini, selalu cek situs resmi IDX, bukan aplikasi ini.

## Efek Auto Reject terhadap Data Historis

Karena ada batas ini, kenaikan atau penurunan harga saham dari satu hari ke hari berikutnya (yang kita catat sebagai "change") pada kondisi normal tidak akan pernah melompat ekstrem dalam sehari. Kalau kamu pernah melihat lonjakan harga yang sangat tidak wajar pada data historis, itu biasanya tanda ada masalah pada data itu sendiri (misalnya akibat aksi korporasi seperti stock split yang belum disesuaikan) — bukan cerminan pergerakan pasar yang wajar.

## Kenapa Ini Penting untuk Sistem Kita?

Perhitungan **Skor Risiko** di halaman [Detail Saham](/stocks) mengukur volatilitas dari data harga historis. Kalau data itu berisi lonjakan yang tidak wajar (bukan pergerakan pasar sungguhan, misalnya akibat data yang belum bersih), hasil perhitungan risikonya bisa jadi menyesatkan — pelajaran penting yang justru kami temukan sendiri saat membangun fitur ini.

## Latihan (Benar/Salah)

"Karena ada mekanisme Auto Reject, harga saham di IDX tidak akan pernah turun lebih dari batas persentase yang ditentukan bursa dalam satu hari perdagangan normal."

Benar atau salah?
MD,
        ];
    }

    private function lessonJamDanHariPerdagangan(): array
    {
        return [
            'slug' => 'jam-dan-hari-perdagangan',
            'title' => 'Jam dan Hari Perdagangan',
            'estimated_minutes' => 6,
            'learning_objectives' => [
                'Menjelaskan konsep hari bursa dan sesi perdagangan',
                'Memahami mengapa data harga saham tidak pernah ada di akhir pekan atau hari libur',
            ],
            'key_terms' => ['hari-bursa'],
            'summary' => 'Bursa Efek Indonesia hanya buka di Hari Bursa (Senin–Jumat, kecuali libur nasional), terbagi dalam beberapa sesi perdagangan per hari. Ini alasan data historis kita hanya berisi hari kerja.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Kalau kamu perhatikan tabel Riwayat Harga di halaman [Detail Saham](/stocks) kita, kamu tidak akan pernah menemukan baris untuk hari Sabtu, Minggu, atau tanggal merah nasional. Ini bukan data yang hilang — memang tidak ada transaksi sama sekali di hari-hari itu.

## Bursa Tidak Buka 24/7

Berbeda dari beberapa aset seperti cryptocurrency yang diperdagangkan 24 jam nonstop, bursa saham konvensional seperti IDX hanya beroperasi pada **Hari Bursa** — hari kerja Senin sampai Jumat, kecuali hari libur nasional atau hari libur khusus yang ditetapkan bursa.

Dalam satu hari bursa, perdagangan juga terbagi ke dalam beberapa sesi (misalnya sesi pagi dan sesi siang), dengan jeda istirahat di antaranya, dan sesi pra-pembukaan/pra-penutupan singkat untuk menentukan harga pembukaan dan penutupan yang adil. Jam dan pembagian sesi persisnya bisa berbeda tergantung hari dan bisa berubah sewaktu-waktu — cek jadwal resmi IDX untuk detail terkini.

## Kenapa Sistem Ini Ada?

Pembatasan jam dan hari ini memberi waktu istirahat bagi seluruh sistem — mulai dari investor, broker, sampai infrastruktur teknologi bursa itu sendiri — sekaligus memberi waktu untuk penyelesaian administrasi transaksi (settlement) dari hari sebelumnya.

## Kenapa Ini Penting untuk Sistem Kita?

Ini juga menjelaskan istilah "hari perdagangan" (trading days) yang sering kita pakai di seluruh aplikasi ini — misalnya perhitungan Skor Momentum yang memakai "return 20 hari" pada halaman [Detail Saham](/stocks) berarti 20 HARI BURSA, bukan 20 hari kalender biasa (yang kalau dihitung mundur akan mencakup beberapa akhir pekan yang sebenarnya tidak ada transaksi).

## Latihan

Kalau hari ini Jumat dan sebuah saham baru saja mencatat harga penutupan, kapan kira-kira data harga penutupan berikutnya akan tercatat (dengan asumsi tidak ada libur nasional di antaranya)?
MD,
        ];
    }

    private function lessonKodeSahamDanPapanPencatatan(): array
    {
        return [
            'slug' => 'kode-saham-dan-papan-pencatatan',
            'title' => 'Kode Saham dan Papan Pencatatan',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan format kode saham (ticker) di IDX',
                'Menjelaskan konsep papan pencatatan dan mengapa perusahaan bisa berada di papan berbeda',
                'Mempraktikkan membaca informasi ticker dan papan pada halaman detail saham',
            ],
            'key_terms' => ['ticker', 'papan-pencatatan'],
            'summary' => 'Setiap saham di IDX punya kode unik 4 huruf (ticker) dan dicatat pada salah satu papan pencatatan (misalnya Utama atau Pengembangan) berdasarkan kriteria tertentu seperti ukuran dan kinerja perusahaan.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Ini pelajaran penutup Modul 2, dan langsung menjelaskan dua kolom yang selalu kamu lihat di setiap kartu saham pada aplikasi ini: kode ticker (misalnya "BBCA") dan papan pencatatan.

## Kode Saham (Ticker)

Setiap perusahaan yang tercatat di IDX diberi **Kode Saham**, atau lebih umum disebut **Ticker** — biasanya terdiri dari 4 huruf unik yang mewakili perusahaan tersebut. Ini seperti "nama panggilan resmi" perusahaan di bursa, dipakai di semua sistem perdagangan dan laporan.

Contoh yang sudah sering kita sebut sepanjang modul ini:

- **BBCA** — PT Bank Central Asia Tbk
- **TLKM** — PT Telkom Indonesia (Persero) Tbk
- **ASII** — PT Astra International Tbk

Kalau perusahaan warung kopi kita, PT Kopi Nusantara Tbk, benar-benar IPO di IDX, kemungkinan besar ia akan diberi ticker seperti "KOPI" — singkat, mudah diingat, dan unik dari perusahaan lain.

## Papan Pencatatan

Tidak semua perusahaan yang IPO otomatis setara. IDX mengelompokkan perusahaan tercatat ke dalam beberapa **Papan Pencatatan**, berdasarkan kriteria seperti ukuran aset, lama beroperasi, dan profitabilitas. Contohnya:

- **Papan Utama**: umumnya untuk perusahaan besar dan sudah mapan, dengan syarat pencatatan paling ketat.
- **Papan Pengembangan**: untuk perusahaan yang belum memenuhi seluruh kriteria Papan Utama, misalnya perusahaan yang lebih baru atau masih dalam tahap pertumbuhan.

(IDX dari waktu ke waktu menyesuaikan struktur dan kriteria papan pencatatannya — anggap contoh di atas sebagai gambaran umum, bukan aturan yang pasti tetap selamanya.)

Papan pencatatan ini memberi sinyal kasar soal profil perusahaan — meskipun bukan jaminan mutlak soal kualitas investasinya.

## Kenapa Ini Penting untuk Sistem Kita?

Setiap saham pada aplikasi ini punya field `ticker` (kode 4 huruf) dan `board` (papan pencatatan) yang bisa kamu lihat di kartu info pada halaman [Detail Saham](/stocks). Karena ini masih data pengembangan, kamu mungkin akan melihat papan yang masih kosong ("-") untuk beberapa saham — data ini belum lengkap diisi, bukan berarti sahamnya tidak tercatat di papan manapun.

## Penutup Modul 2

Sampai di sini kamu sudah paham bagaimana transaksi saham benar-benar terjadi: lewat bursa resmi (IDX), diperantarai broker, dalam satuan lot, terbentuk dari bid-ask, dengan berbagai jenis order, dibatasi auto reject, hanya di hari bursa, dan tercatat dengan kode+papan tertentu. Modul 3 akan mengajarimu membaca data harga historis itu sendiri — OHLC, grafik, volume, dan tren.

## Latihan

Buka halaman [Saham](/stocks) di aplikasi ini, pilih satu saham mana pun, lalu buka halaman detailnya. Catat: apa kode tickernya, di bursa mana ia tercatat, dan apa isi kolom "Papan"nya?
MD,
        ];
    }

    private function lessonApaItuDataOhlc(): array
    {
        return [
            'slug' => 'apa-itu-data-ohlc',
            'title' => 'Apa Itu Data OHLC?',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan arti Open, High, Low, dan Close dalam data harga saham',
                'Menjelaskan mengapa satu hari perdagangan dicatat dengan 4 angka harga, bukan 1',
                'Membaca satu baris data OHLC dan menceritakan pergerakan harga hari itu',
            ],
            'key_terms' => ['ohlc'],
            'summary' => 'Data harga harian saham dicatat dalam 4 angka (Open, High, Low, Close) yang menceritakan bagaimana harga bergerak sepanjang hari — bukan cuma satu angka tunggal.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Modul 2 sudah menjelaskan BAGAIMANA transaksi saham terjadi. Sekarang, Modul 3, kita belajar BAGAIMANA MEMBACA hasil dari jutaan transaksi itu — dimulai dari bentuk data paling dasar yang kita simpan untuk setiap saham, setiap hari.

## Satu Hari, Banyak Harga

Ingat dari Modul 2: sepanjang hari bursa, harga saham terus bergerak naik-turun mengikuti bid dan ask yang saling bertemu. Kalau kita cuma mencatat SATU angka per hari, kita akan kehilangan banyak cerita penting tentang apa yang sebenarnya terjadi.

Karena itu, setiap hari perdagangan sebuah saham dicatat dengan **empat** angka harga, disingkat **OHLC**:

- **Open**: harga transaksi pertama saat sesi perdagangan dibuka hari itu.
- **High**: harga tertinggi yang tercapai sepanjang hari itu.
- **Low**: harga terendah yang tercapai sepanjang hari itu.
- **Close**: harga transaksi terakhir saat sesi perdagangan ditutup hari itu.

Ditambah satu angka lagi yang akan kita bahas di Pelajaran 3 (Volume), gabungannya sering disebut **OHLCV**.

## Membaca Satu Baris Data

Ambil contoh baris data untuk saham BBCA pada satu hari:

```
Open  : 6.300
High  : 6.350
Low   : 6.275
Close : 6.350
```

Cerita yang bisa kita baca dari angka-angka ini: hari itu perdagangan BBCA dibuka di Rp6.300. Sepanjang hari, harga sempat turun ke titik terendah Rp6.275, lalu berbalik naik dan menyentuh titik tertinggi Rp6.350 — dan ternyata harga penutupannya PERSIS di titik tertinggi hari itu (Rp6.350), menandakan sentimen pembeli menguat menjelang penutupan.

Bandingkan dengan hari lain yang closenya justru dekat dengan Low — itu menandakan tekanan jual justru menguat menjelang penutupan, meskipun sempat naik tinggi di tengah hari.

## Kenapa Ini Penting untuk Sistem Kita?

Tabel **Riwayat Harga** di halaman [Detail Saham](/stocks) menampilkan persis kelima kolom ini (Open, High, Low, Close, Volume) untuk setiap hari perdagangan — dan semua fitur analisis di aplikasi ini, mulai dari grafik, Skor Momentum, sampai level Support/Resistance, dibangun di atas data mentah OHLCV ini.

## Latihan

Sebuah saham mencatat data harian: Open 4.500, High 4.650, Low 4.480, Close 4.500. Apa yang bisa kamu simpulkan dari perbandingan Open dan Close di sini, dibandingkan dengan seberapa jauh High-nya dari keduanya?
MD,
        ];
    }

    private function lessonMembacaGrafikHarga(): array
    {
        return [
            'slug' => 'membaca-grafik-harga',
            'title' => 'Membaca Grafik Harga',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Membedakan line chart dan candlestick chart',
                'Menjelaskan mengapa grafik lebih mudah dibaca dibanding tabel angka mentah',
            ],
            'key_terms' => ['candlestick'],
            'summary' => 'Grafik harga mengubah ratusan angka OHLC menjadi pola visual yang lebih mudah dibaca. Line chart (dipakai di aplikasi ini) menghubungkan harga Close antar hari; candlestick chart menampilkan keempat angka OHLC sekaligus per hari.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Bayangkan membaca 90 baris data OHLC dalam bentuk tabel dan mencoba menyimpulkan "apakah harga sedang tren naik atau turun?" — melelahkan dan gampang salah. Di sinilah grafik (chart) menyelamatkan kita.

## Line Chart: Yang Kita Pakai

Grafik "Harga Penutupan" pada halaman [Detail Saham](/stocks) di aplikasi ini adalah **Line Chart** — grafik garis yang menghubungkan harga **Close** setiap hari secara berurutan. Sumbu mendatar (X) menunjukkan tanggal, sumbu tegak (Y) menunjukkan harga.

Kelebihan line chart: sederhana, mudah langsung menangkap arah tren secara sekilas — apakah garis secara umum naik, turun, atau mendatar dari kiri ke kanan.

## Candlestick Chart: Standar Industri

Di aplikasi trading profesional, grafik yang lebih umum dipakai adalah **Candlestick Chart** (grafik lilin). Setiap "lilin" mewakili satu hari, dan menampilkan KEEMPAT angka OHLC sekaligus dalam satu bentuk visual:

- Badan lilin (kotak): rentang antara harga Open dan Close
- Sumbu/ekor di atas dan bawah kotak: menunjukkan High dan Low hari itu
- Warna lilin (biasanya hijau/putih vs merah/hitam): menandakan apakah Close lebih tinggi atau lebih rendah dari Open hari itu

Candlestick memberi informasi lebih kaya (kamu bisa melihat volatilitas intra-hari), tapi juga lebih ramai dibaca oleh pemula dibanding line chart yang sederhana.

## Kenapa Ini Penting untuk Sistem Kita?

Aplikasi ini sengaja memakai line chart berbasis harga Close untuk menjaga tampilan tetap sederhana buat pemula. Perhatikan juga garis putus-putus hijau dan merah yang mungkin muncul di grafik itu — itu adalah level **Support** dan **Resistance** yang akan kita bahas di Pelajaran 5, dihitung otomatis dari titik balik harga historis.

## Latihan

Kalau kamu melihat garis pada line chart bergerak naik secara konsisten dari kiri ke kanan selama sebulan terakhir, kesimpulan awal apa yang bisa kamu ambil tentang arah harga saham itu?
MD,
        ];
    }

    private function lessonApaItuVolumePerdagangan(): array
    {
        return [
            'slug' => 'apa-itu-volume-perdagangan',
            'title' => 'Apa Itu Volume Perdagangan?',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Menjelaskan definisi volume perdagangan',
                'Menjelaskan mengapa volume penting untuk menilai "kekuatan" sebuah pergerakan harga',
            ],
            'key_terms' => ['volume-perdagangan'],
            'summary' => 'Volume adalah jumlah lembar saham yang berpindah tangan dalam satu hari. Pergerakan harga yang disertai volume tinggi umumnya dianggap lebih meyakinkan daripada pergerakan dengan volume rendah.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Angka harga saja tidak menceritakan seberapa "kuat" atau "meyakinkan" sebuah pergerakan harga. Untuk itu kita butuh satu angka tambahan yang sudah kita singgung di Pelajaran 1: Volume.

## Definisi Volume

**Volume Perdagangan** adalah jumlah total lembar saham yang berpindah tangan (diperjualbelikan) dalam satu hari perdagangan.

Kalau hari ini ada 100 transaksi terpisah untuk saham BBCA, dan totalnya menjumlahkan 56.953.500 lembar saham berpindah tangan, maka Volume hari itu adalah 56.953.500.

## Kenapa Volume Penting: Dua Skenario

Bayangkan dua skenario di mana harga BBCA sama-sama naik 2% dalam sehari:

**Skenario A**: Naik 2% dengan volume SANGAT TINGGI (jauh di atas rata-rata harian biasanya)
→ Menandakan BANYAK investor beramai-ramai membeli, sinyal kepercayaan pasar yang kuat terhadap kenaikan ini.

**Skenario B**: Naik 2% dengan volume SANGAT RENDAH (jauh di bawah rata-rata harian)
→ Kenaikan ini mungkin hanya digerakkan segelintir transaksi kecil — kurang meyakinkan, dan lebih rentan berbalik arah keesokan harinya.

Aturan praktis yang sering dipakai: **pergerakan harga yang didukung volume tinggi cenderung lebih dipercaya untuk berlanjut, dibanding pergerakan dengan volume rendah.**

## Kenapa Ini Penting untuk Sistem Kita?

Kolom **Volume** ada di setiap baris tabel Riwayat Harga pada halaman [Detail Saham](/stocks). Meskipun Skor Momentum kita (Modul 7 nanti akan membahas detail teknikalnya) saat ini fokus pada pergerakan harga, membiasakan diri membaca volume berdampingan dengan harga adalah kebiasaan penting bagi siapa pun yang belajar membaca data pasar.

## Latihan

Saham X naik 5% hari ini dengan volume 3x lipat dari rata-rata volumenya biasanya. Saham Y juga naik 5% hari ini, tapi dengan volume hanya setengah dari rata-rata biasanya. Manakah kenaikan yang lebih "meyakinkan", dan mengapa?
MD,
        ];
    }

    private function lessonMengenaliTrenHarga(): array
    {
        return [
            'slug' => 'mengenali-tren-harga',
            'title' => 'Mengenali Tren: Naik, Turun, Sideways',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Mengenali secara visual pola uptrend, downtrend, dan sideways pada grafik harga',
                'Memahami bahwa tren adalah pengamatan arah umum, bukan garis lurus sempurna',
            ],
            'key_terms' => ['tren'],
            'summary' => 'Tren adalah arah pergerakan umum harga saham dalam suatu periode: naik (uptrend), turun (downtrend), atau relatif mendatar (sideways). Harga jarang bergerak lurus sempurna — tren dilihat dari pola umumnya, bukan tiap gerakan harian.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Sekarang kamu sudah bisa membaca OHLC, grafik, dan volume satu per satu. Pelajaran ini menggabungkan kemampuan itu untuk mengenali pola yang lebih besar: ke arah mana sebenarnya harga sebuah saham "sedang berjalan"?

## Tiga Pola Dasar

**Tren (Trend)** adalah arah pergerakan umum harga saham selama suatu periode waktu. Ada tiga pola dasar:

**Uptrend (Tren Naik)**: harga secara umum bergerak naik dari waktu ke waktu, meskipun ada naik-turun kecil di sepanjang jalan. Kalau kamu bandingkan titik-titik terendah berturut-turut, masing-masing cenderung lebih tinggi dari titik terendah sebelumnya.

**Downtrend (Tren Turun)**: kebalikannya — harga secara umum bergerak turun, dengan titik-titik tertinggi berturut-turut yang cenderung lebih rendah dari sebelumnya.

**Sideways (Konsolidasi)**: harga bergerak dalam rentang yang relatif sempit, tanpa arah naik atau turun yang jelas dalam jangka waktu tertentu — seperti "beristirahat" sebelum memilih arah berikutnya.

## Tren Bukan Garis Lurus

Kesalahan umum pemula: mengira uptrend berarti harga naik TERUS TANPA PERNAH turun sedikit pun. Kenyataannya, hampir semua tren — naik maupun turun — punya banyak gerakan zig-zag kecil di sepanjang jalan. Yang menentukan arah tren adalah pola KESELURUHAN dalam periode yang kamu amati, bukan setiap gerakan harian.

Cara sederhana membacanya: perhatikan grafik dalam rentang waktu (misalnya 90 hari), lalu tanyakan — kalau ditarik garis lurus kasar dari titik paling kiri ke titik paling kanan, ke arah mana garis itu condong?

## Kenapa Ini Penting untuk Sistem Kita?

Konsep tren inilah yang mendasari **Skor Momentum** kita di halaman [Detail Saham](/stocks) — meskipun di balik layar dihitung dengan rumus matematis presisi (rata-rata bergerak dan return periode tertentu), intinya sama: mendeteksi apakah harga secara umum sedang uptrend, downtrend, atau sideways. Detail rumusnya akan kita bahas lengkap di Modul 7.

## Latihan

Perhatikan grafik harga sebuah saham selama 3 bulan terakhir: harganya naik-turun setiap minggu, tapi titik terendah bulan ini selalu lebih tinggi dari titik terendah bulan sebelumnya. Tren apa yang paling menggambarkan pola ini?
MD,
        ];
    }

    private function lessonSupportDanResistance(): array
    {
        return [
            'slug' => 'support-dan-resistance',
            'title' => 'Support dan Resistance',
            'estimated_minutes' => 8,
            'learning_objectives' => [
                'Menjelaskan konsep support dan resistance dengan bahasa sederhana',
                'Memahami bagaimana level ini terbentuk dari perilaku banyak investor',
                'Memahami bahwa support/resistance adalah pola historis, bukan jaminan masa depan',
            ],
            'key_terms' => ['support', 'resistance'],
            'summary' => 'Support adalah level harga di mana saham cenderung berhenti turun dan berbalik naik; resistance adalah level di mana saham cenderung berhenti naik dan berbalik turun. Keduanya terbentuk dari pola harga historis, bukan aturan pasti.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Kalau kamu perhatikan grafik harga saham dalam waktu cukup lama, kamu akan sering melihat pola menarik: harga seperti "memantul" berulang kali di level tertentu, seolah ada lantai tak terlihat yang menahannya, atau langit-langit yang menghalanginya naik lebih jauh.

## Lantai dan Langit-Langit Sementara

Bayangkan sebuah ruangan dengan lantai dan langit-langit. Bola yang dilempar ke dalam ruangan itu akan memantul dari lantai (tidak jatuh menembus ke bawah) dan memantul dari langit-langit (tidak menembus ke atas) — selama tidak dilempar dengan tenaga yang cukup untuk menembus keduanya.

Ini analogi untuk dua konsep penting:

**Support**: level harga di mana saham secara historis cenderung BERHENTI TURUN dan berbalik naik — seperti "lantai" harga. Ini biasanya terjadi karena di level harga tersebut, banyak investor menganggapnya "cukup murah" untuk mulai membeli lagi, sehingga permintaan beli meningkat dan menahan penurunan lebih lanjut.

**Resistance**: level harga di mana saham secara historis cenderung BERHENTI NAIK dan berbalik turun — seperti "langit-langit" harga. Ini biasanya terjadi karena di level itu, banyak investor menganggapnya "cukup mahal" dan mulai menjual, meningkatkan tekanan jual yang menahan kenaikan lebih lanjut.

## Bagaimana Level Ini Terbentuk?

Support dan resistance bukan angka ajaib — mereka terbentuk dari psikologi kolektif banyak investor yang mengingat harga-harga penting di masa lalu. Semakin sering harga "menyentuh" level tertentu dan memantul balik dari sana, semakin banyak investor yang memperhatikan level itu, dan semakin kuat level tersebut cenderung bertahan (meski tidak selalu).

## Level Ini Bisa "Ditembus"

Penting dipahami: support dan resistance BUKAN dinding permanen. Kalau tekanan beli atau jual cukup kuat (misalnya karena berita besar tentang perusahaan), harga BISA menembus level itu — dan menariknya, level yang tadinya resistance yang tertembus sering berubah peran menjadi support baru, begitu juga sebaliknya.

## Kenapa Ini Penting untuk Sistem Kita?

Ini persis fitur **"Analisis Lanjutan"** yang sudah kamu lihat di halaman [Detail Saham](/stocks) kita! Sistem secara otomatis mendeteksi titik-titik balik (swing high/low) dari data harga historis, mengelompokkan level yang berdekatan, dan menampilkan level Support/Resistance yang paling sering "disentuh" harga. Ingat baik-baik: ini murni pola historis — bukan jaminan harga akan memantul lagi persis di level yang sama di masa depan.

## Latihan

Sebuah saham sudah tiga kali dalam 6 bulan terakhir turun mendekati harga Rp5.000, lalu selalu berbalik naik dari situ. Level Rp5.000 ini paling tepat disebut sebagai apa — support atau resistance? Jelaskan.
MD,
        ];
    }

    private function lessonMemahamiPerubahanHargaHarian(): array
    {
        return [
            'slug' => 'memahami-perubahan-harga-harian',
            'title' => 'Memahami Perubahan Harga Harian',
            'estimated_minutes' => 6,
            'learning_objectives' => [
                'Menghitung perubahan harga (change) dan persentase perubahan (change percent)',
                'Menjelaskan mengapa perubahan dihitung dari harga penutupan hari sebelumnya',
            ],
            'key_terms' => ['perubahan-harga'],
            'summary' => 'Perubahan harga harian (change) dihitung dari selisih harga Close hari ini dengan harga Close hari perdagangan sebelumnya, dan dinyatakan juga dalam bentuk persentase (change percent) agar mudah dibandingkan antar saham.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Badge hijau atau merah dengan tanda panah yang kamu lihat di samping harga saham pada aplikasi ini — itulah yang kita bahas di pelajaran ini: bagaimana angka "perubahan harga" itu benar-benar dihitung.

## Rumusnya Sederhana

**Perubahan Harga (Change)** adalah selisih antara harga Close hari ini dengan harga Close hari perdagangan SEBELUMNYA (bukan kemarin secara kalender, tapi hari bursa sebelumnya — ingat Pelajaran 7 di Modul 2).

```
Change = Close Hari Ini − Close Hari Bursa Sebelumnya
```

Karena angka Rupiah saja sulit dibandingkan antar saham dengan harga yang jauh berbeda (naik Rp100 di saham Rp1.000 jauh lebih besar dampaknya dibanding naik Rp100 di saham Rp10.000), kita juga menghitung **Change Percent**:

```
Change Percent = (Change / Close Hari Bursa Sebelumnya) × 100%
```

## Contoh Perhitungan

Saham BBCA ditutup di Rp6.375 kemarin, dan hari ini ditutup di Rp6.350:

```
Change         = Rp6.350 − Rp6.375 = −Rp25
Change Percent = (−25 / 6.375) × 100% ≈ −0,39%
```

Artinya BBCA turun Rp25, atau sekitar 0,39%, dibanding penutupan hari sebelumnya — persis seperti yang biasa kamu lihat di badge merah pada aplikasi ini.

## Kenapa Ini Penting untuk Sistem Kita?

Badge perubahan harga di halaman [Saham](/stocks) maupun [Detail Saham](/stocks) — dengan ikon panah naik (hijau) atau turun (merah) — dihasilkan dari perhitungan persis seperti di atas. Kamu sekarang tahu persis dari mana angka itu berasal, bukan sekadar percaya begitu saja.

## Latihan

Sebuah saham ditutup di harga Rp8.400 kemarin dan Rp8.820 hari ini. Hitung change (dalam Rupiah) dan change percent-nya.
MD,
        ];
    }

    private function lessonDataHistorisVsRealTime(): array
    {
        return [
            'slug' => 'data-historis-vs-real-time',
            'title' => 'Data Historis vs Real-Time',
            'estimated_minutes' => 7,
            'learning_objectives' => [
                'Membedakan data historis dan data real-time/live',
                'Menjelaskan dari mana dan bagaimana aplikasi ini mendapatkan datanya',
                'Memahami keterbatasan sumber data yang dipakai aplikasi ini',
            ],
            'key_terms' => ['data-historis'],
            'summary' => 'Aplikasi ini menggunakan data historis harga (diperbarui secara berkala) dari penyedia data tidak resmi, bukan data real-time streaming seperti aplikasi trading sungguhan — penting untuk memahami batasan ini sebelum menggunakan data untuk analisis.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Sebelum kita lanjut ke pelajaran penutup Modul 3, penting untuk jujur soal DARI MANA data yang kamu lihat di aplikasi ini berasal, dan apa keterbatasannya — supaya kamu jadi pengguna data yang kritis, bukan cuma percaya begitu saja pada angka yang ditampilkan.

## Dua Jenis Data Harga

**Data Real-Time (Live)**: harga yang diperbarui setiap detik, mengikuti setiap transaksi yang terjadi langsung di bursa. Ini yang dipakai aplikasi trading sungguhan, biasanya berbayar dan bersumber resmi langsung dari penyedia data bursa.

**Data Historis**: kumpulan data harga (OHLCV) dari hari-hari SEBELUMNYA yang sudah selesai dan tercatat permanen — dipakai untuk analisis pola, backtesting, dan riset, bukan untuk memantau harga detik demi detik.

## Bagaimana Aplikasi Ini Mendapatkan Data?

Aplikasi ini mengambil data historis harga dari Yahoo Finance lewat endpoint publik yang **tidak resmi** (bukan API resmi berbayar), diperbarui lewat tombol "Sync Data" secara manual atau otomatis terjadwal sekali sehari. Ini artinya:

- Data BUKAN real-time — ada jeda antara harga sungguhan di pasar dengan yang tersimpan di aplikasi ini.
- Karena sumbernya tidak resmi, sewaktu-waktu endpoint ini bisa berubah, dibatasi, atau terputus tanpa pemberitahuan.
- Sesekali data bisa mengandung anomali (misalnya akibat aksi korporasi yang belum disesuaikan) sebelum dibersihkan — persis seperti yang kita singgung di Pelajaran 6, Modul 2.

## Kenapa Ini Penting untuk Sistem Kita?

Disclaimer di footer aplikasi ini — "Data pengembangan bersifat sintetis dan hanya untuk keperluan demo — bukan data pasar riil" — dan status data (misalnya kapan terakhir sinkron) ada untuk mengingatkanmu batasan ini. Aplikasi ini dirancang sebagai alat BELAJAR dan RISET berbasis data historis, bukan terminal trading real-time untuk mengambil keputusan transaksi mendadak.

## Latihan (Refleksi)

Kalau kamu ingin benar-benar bertransaksi saham berdasarkan harga yang bergerak detik ini juga, apakah aplikasi Stock Recommendation ini sumber yang tepat untuk itu? Kenapa?
MD,
        ];
    }

    private function lessonLatihanMembacaHalamanDetailSaham(): array
    {
        return [
            'slug' => 'latihan-membaca-halaman-detail-saham',
            'title' => 'Latihan: Membaca Halaman Detail Saham',
            'estimated_minutes' => 10,
            'learning_objectives' => [
                'Mengintegrasikan seluruh konsep Modul 1–3 untuk membaca satu halaman Detail Saham secara utuh',
                'Mempraktikkan langsung dengan data saham sungguhan di aplikasi ini',
            ],
            'key_terms' => [],
            'summary' => 'Pelajaran penutup Modul 3 ini menggabungkan semua konsep dari tiga modul sebelumnya menjadi satu latihan praktik: membaca halaman Detail Saham dari atas sampai bawah secara utuh dan bermakna.',
            'content' => <<<'MD'
## Kenapa Ini Penting?

Ini pelajaran penutup Modul 3, sekaligus penutup sementara sebelum kita masuk ke Modul 4 (laporan keuangan). Saatnya menggabungkan SEMUA yang sudah kamu pelajari sejauh ini menjadi satu kemampuan utuh: membaca halaman [Detail Saham](/stocks) dari atas sampai bawah, dan benar-benar memahami setiap bagiannya.

## Langkah Praktik

Buka halaman Detail Saham untuk saham pilihanmu, lalu ikuti urutan berikut sambil menghubungkan ke apa yang sudah kamu pelajari:

**1. Header (Ticker, Bursa, Nama Perusahaan)**
Kode ticker dan "IDX" yang kamu lihat — ingat Modul 2, Pelajaran 1 dan 8: ini adalah kode resmi perusahaan tercatat di Bursa Efek Indonesia.

**2. Harga Terakhir dan Badge Perubahan**
Harga ini adalah harga **Close** dari hari bursa terakhir (Modul 3, Pelajaran 1). Badge hijau/merah di sampingnya adalah **Change** dan **Change Percent** (Modul 3, Pelajaran 6), dihitung dari selisih dengan Close hari bursa sebelumnya.

**3. Kartu Rekomendasi (Skor Momentum & Skor Risiko)**
Skor Momentum mencerminkan **Tren** (Modul 3, Pelajaran 4) yang terdeteksi dari data harga. Skor Risiko mencerminkan **Volatilitas** (Modul 1, Pelajaran 7) historis saham itu. Detail rumus keduanya akan kita bahas lengkap di Modul 7.

**4. Grafik Harga Penutupan**
Ini adalah **Line Chart** (Modul 3, Pelajaran 2) dari harga Close historis. Garis putus-putus hijau dan merah (jika ada) adalah level **Support** dan **Resistance** (Modul 3, Pelajaran 5).

**5. Tabel Riwayat Harga**
Data mentah **OHLCV** (Modul 3, Pelajaran 1 dan 3) per hari bursa — inilah bahan baku dari SEMUA analisis di atas.

## Kenapa Ini Penting untuk Sistem Kita?

Kemampuan menghubungkan setiap elemen visual di halaman ini kembali ke konsep dasarnya adalah keterampilan inti yang membedakan pengguna yang sekadar "melihat angka" dengan pengguna yang benar-benar "membaca data".

## Latihan (Praktik Langsung)

Buka halaman [Saham](/stocks), pilih SATU saham yang belum pernah kamu lihat detailnya. Buka halaman detailnya, lalu tuliskan (di catatanmu sendiri) satu paragraf pendek yang menjelaskan kondisi saham itu saat ini — mencakup harga, arah perubahan, tren umum menurut skor momentum, dan minimal satu level support atau resistance yang terdeteksi.

## Penutup Modul 3

Kamu sudah menyelesaikan tiga modul: dasar-dasar kepemilikan dan saham (Modul 1), mekanisme transaksi di bursa (Modul 2), dan cara membaca data harga (Modul 3). Modul 4 (akan datang) akan membawamu ke sisi lain dari analisis saham: membaca laporan keuangan perusahaan itu sendiri — dari mana nilai sebuah bisnis sesungguhnya berasal.
MD,
        ];
    }
}
