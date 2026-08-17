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

        $module = LearningModule::where('slug', 'dasar-dasar-saham')->firstOrFail();

        foreach ($this->lessons() as $order => $lesson) {
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

    /**
     * @return array<int, array{slug: string, title: string, estimated_minutes: int, learning_objectives: array<int, string>, key_terms: array<int, string>, content: string, summary: string}>
     */
    private function lessons(): array
    {
        return [
            $this->lessonApaItuPerusahaan(),
            $this->lessonApaItuKepemilikan(),
            $this->lessonApaItuSaham(),
            $this->lessonMengapaPerusahaanMenjualSaham(),
            $this->lessonMengapaInvestorMembeliSaham(),
            $this->lessonBagaimanaInvestorUntung(),
            $this->lessonBagaimanaInvestorRugi(),
            $this->lessonSahamVsAsetLain(),
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
}
