<?php

namespace Database\Seeders;

use App\Models\LearningLesson;
use App\Models\LearningQuestion;
use App\Models\LearningQuestionOption;
use App\Models\LearningQuiz;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningQuizSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('learning_question_options')->delete();
        DB::table('learning_questions')->delete();
        DB::table('learning_quizzes')->delete();

        foreach ($this->quizzes() as $lessonSlug => $questions) {
            $lesson = LearningLesson::where('slug', $lessonSlug)->first();

            if (! $lesson) {
                $this->command?->warn("Skipping quiz for unknown lesson [{$lessonSlug}].");

                continue;
            }

            $quiz = LearningQuiz::create([
                'lesson_id' => $lesson->id,
                'title' => "Kuis: {$lesson->title}",
            ]);

            foreach ($questions as $index => $questionData) {
                $question = LearningQuestion::create([
                    'quiz_id' => $quiz->id,
                    'order' => $index + 1,
                    'type' => 'multiple_choice',
                    'question' => $questionData['question'],
                    'explanation' => $questionData['explanation'],
                    'difficulty' => $questionData['difficulty'] ?? 'medium',
                ]);

                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    LearningQuestionOption::create([
                        'question_id' => $question->id,
                        'order' => $optionIndex + 1,
                        'text' => $optionData['text'],
                        'is_correct' => $optionData['correct'] ?? false,
                    ]);
                }
            }
        }
    }

    /**
     * @return array<string, array<int, array{question: string, explanation: string, difficulty?: string, options: array<int, array{text: string, correct?: bool}>}>>
     */
    private function quizzes(): array
    {
        return [
            'apa-itu-perusahaan' => [
                [
                    'question' => 'Dalam contoh warung kopi, apa yang disebut "Modal"?',
                    'explanation' => 'Modal adalah uang/nilai yang ditanam pemilik untuk memulai dan menjalankan bisnis — seperti Rp10 juta yang kamu masukkan di awal untuk membeli mesin, meja-kursi, dan stok.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Mesin kopi dan meja-kursi'],
                        ['text' => 'Uang yang ditanam pemilik di awal untuk memulai bisnis', 'correct' => true],
                        ['text' => 'Uang hasil jualan kopi'],
                        ['text' => 'Utang ke bank'],
                    ],
                ],
                [
                    'question' => 'Warung kopi mencatat pemasukan Rp8.000.000 dan pengeluaran Rp5.500.000 dalam sebulan. Berapa untung/rugi bulan itu?',
                    'explanation' => 'Untung = Pemasukan − Pengeluaran = Rp8.000.000 − Rp5.500.000 = Rp2.500.000 untung.',
                    'options' => [
                        ['text' => 'Untung Rp2.500.000', 'correct' => true],
                        ['text' => 'Rugi Rp2.500.000'],
                        ['text' => 'Untung Rp13.500.000'],
                        ['text' => 'Impas (tidak untung tidak rugi)'],
                    ],
                ],
                [
                    'question' => 'Manakah yang termasuk "Aset" sebuah bisnis?',
                    'explanation' => 'Aset adalah segala sesuatu yang dimiliki dan bernilai bagi bisnis, seperti mesin, meja-kursi, stok bahan baku, dan kas — bukan utang atau persentase kepemilikan.',
                    'options' => [
                        ['text' => 'Utang ke bank'],
                        ['text' => 'Mesin kopi, meja-kursi, dan kas', 'correct' => true],
                        ['text' => 'Persentase kepemilikan pemilik'],
                        ['text' => 'Harga saham'],
                    ],
                ],
            ],
            'apa-itu-kepemilikan' => [
                [
                    'question' => 'Kamu kontribusi modal Rp10 juta, Sari kontribusi Rp5 juta (total Rp15 juta). Berapa persen kepemilikan Sari?',
                    'explanation' => 'Kepemilikan Sari = Rp5 juta / Rp15 juta = 33,3%.',
                    'options' => [
                        ['text' => '50%'],
                        ['text' => '33,3%', 'correct' => true],
                        ['text' => '66,7%'],
                        ['text' => '100%'],
                    ],
                ],
                [
                    'question' => 'Sebuah bisnis punya aset senilai Rp20.000.000 dan utang Rp5.000.000. Berapa total ekuitas bisnis ini?',
                    'explanation' => 'Ekuitas = Aset − Utang = Rp20.000.000 − Rp5.000.000 = Rp15.000.000.',
                    'options' => [
                        ['text' => 'Rp25.000.000'],
                        ['text' => 'Rp5.000.000'],
                        ['text' => 'Rp15.000.000', 'correct' => true],
                        ['text' => 'Rp20.000.000'],
                    ],
                ],
                [
                    'question' => 'Benar atau salah: semakin besar persentase kepemilikanmu di sebuah bisnis, semakin besar juga suara/hakmu dalam keputusan besar bisnis itu.',
                    'explanation' => 'Benar — persentase kepemilikan umumnya menentukan proporsi suara dalam keputusan besar, selain proporsi bagi hasil untung/rugi.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Benar', 'correct' => true],
                        ['text' => 'Salah'],
                    ],
                ],
            ],
            'apa-itu-saham' => [
                [
                    'question' => 'Sebuah perusahaan punya 1.000.000 saham beredar. Kamu memegang 250.000 lembar. Berapa persen kepemilikanmu?',
                    'explanation' => '250.000 / 1.000.000 = 25%.',
                    'options' => [
                        ['text' => '20%'],
                        ['text' => '25%', 'correct' => true],
                        ['text' => '40%'],
                        ['text' => '2,5%'],
                    ],
                ],
                [
                    'question' => 'PT Kopi Nusantara Tbk punya 400.000.000 saham beredar dengan harga Rp2.500/lembar. Berapa kapitalisasi pasarnya?',
                    'explanation' => 'Kapitalisasi Pasar = Rp2.500 × 400.000.000 = Rp1.000.000.000.000 (Rp1 triliun).',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Rp100 juta'],
                        ['text' => 'Rp1 triliun', 'correct' => true],
                        ['text' => 'Rp400 juta'],
                        ['text' => 'Rp10 miliar'],
                    ],
                ],
                [
                    'question' => 'Apa itu "saham beredar" (shares outstanding)?',
                    'explanation' => 'Saham beredar adalah total jumlah lembar saham yang diterbitkan oleh perusahaan dan dipegang oleh seluruh pemegang saham.',
                    'options' => [
                        ['text' => 'Saham yang sedang naik harganya'],
                        ['text' => 'Total lembar saham yang diterbitkan perusahaan', 'correct' => true],
                        ['text' => 'Saham yang belum terjual sama sekali'],
                        ['text' => 'Saham milik direksi perusahaan saja'],
                    ],
                ],
            ],
            'mengapa-perusahaan-menjual-saham' => [
                [
                    'question' => 'Benar atau salah: ketika kamu membeli saham BBCA hari ini lewat aplikasi sekuritas, uang yang kamu bayarkan langsung masuk ke kas PT Bank Central Asia Tbk.',
                    'explanation' => 'Salah — transaksi harian di bursa terjadi di pasar sekunder, uangmu berpindah ke investor lain yang menjual sahamnya, bukan ke kas perusahaan (kecuali saat IPO di pasar primer).',
                    'options' => [
                        ['text' => 'Benar'],
                        ['text' => 'Salah', 'correct' => true],
                    ],
                ],
                [
                    'question' => 'Apa alasan utama sebuah perusahaan melakukan IPO?',
                    'explanation' => 'Alasan utama IPO adalah mendapatkan modal besar untuk ekspansi tanpa menanggung beban bunga dan kewajiban pembayaran rutin seperti utang bank.',
                    'options' => [
                        ['text' => 'Supaya nama perusahaan terkenal'],
                        ['text' => 'Mendapatkan modal besar tanpa harus berutang ke bank', 'correct' => true],
                        ['text' => 'Supaya karyawan bisa membeli produk perusahaan'],
                        ['text' => 'Karena diwajibkan pemerintah'],
                    ],
                ],
                [
                    'question' => 'Apa yang membedakan perusahaan publik dari perusahaan privat?',
                    'explanation' => 'Perusahaan publik telah melewati IPO sehingga sahamnya tercatat di bursa dan bisa dibeli siapa saja yang memiliki rekening efek.',
                    'options' => [
                        ['text' => 'Perusahaan publik tidak punya pemilik'],
                        ['text' => 'Saham perusahaan publik bisa dibeli siapa saja lewat bursa', 'correct' => true],
                        ['text' => 'Perusahaan privat tidak boleh mencari untung'],
                        ['text' => 'Perusahaan publik selalu lebih kecil'],
                    ],
                ],
            ],
            'mengapa-investor-membeli-saham' => [
                [
                    'question' => 'Kamu punya 500 lembar saham dan menerima dividen Rp80/saham. Berapa total dividen yang kamu terima?',
                    'explanation' => 'Total dividen = Rp80 × 500 lembar = Rp40.000.',
                    'options' => [
                        ['text' => 'Rp8.000'],
                        ['text' => 'Rp40.000', 'correct' => true],
                        ['text' => 'Rp80.000'],
                        ['text' => 'Rp400.000'],
                    ],
                ],
                [
                    'question' => 'Kamu beli saham di harga Rp3.200, sekarang harganya Rp3.600, dan kamu punya 500 lembar yang belum dijual. Berapa capital gain "di atas kertas"-mu?',
                    'explanation' => 'Capital gain = (Rp3.600 − Rp3.200) × 500 lembar = Rp400 × 500 = Rp200.000 (masih di atas kertas karena belum dijual).',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Rp400'],
                        ['text' => 'Rp3.600'],
                        ['text' => 'Rp200.000', 'correct' => true],
                        ['text' => 'Rp1.800.000'],
                    ],
                ],
                [
                    'question' => 'Apa itu dividen?',
                    'explanation' => 'Dividen adalah bagian dari laba perusahaan yang dibagikan secara tunai kepada pemegang saham sesuai proporsi kepemilikan.',
                    'options' => [
                        ['text' => 'Selisih untung dari kenaikan harga saham'],
                        ['text' => 'Bagian laba perusahaan yang dibagikan tunai ke pemegang saham', 'correct' => true],
                        ['text' => 'Biaya untuk membeli saham'],
                        ['text' => 'Pajak atas transaksi saham'],
                    ],
                ],
            ],
            'bagaimana-investor-menghasilkan-uang' => [
                [
                    'question' => 'Di mana sebagian besar transaksi jual-beli saham sehari-hari terjadi?',
                    'explanation' => 'Setelah IPO, hampir semua transaksi terjadi di pasar sekunder — antar-investor, bukan dengan perusahaan.',
                    'options' => [
                        ['text' => 'Pasar Primer'],
                        ['text' => 'Pasar Sekunder', 'correct' => true],
                        ['text' => 'Langsung ke kas perusahaan'],
                        ['text' => 'Bank sentral'],
                    ],
                ],
                [
                    'question' => 'Kamu beli saham senilai Rp8.400.000 (1.000 lembar). Menerima dividen total Rp100.000, dan harga naik jadi Rp9.200/lembar. Kira-kira berapa total return-mu?',
                    'explanation' => 'Return dividen (~1,19%) + return capital gain (~9,52%) ≈ 10,71% total return.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Sekitar 1,2%'],
                        ['text' => 'Sekitar 9,5%'],
                        ['text' => 'Sekitar 10,7%', 'correct' => true],
                        ['text' => 'Sekitar 100%'],
                    ],
                ],
                [
                    'question' => 'Benar atau salah: keuntungan dari kenaikan harga saham yang belum kamu jual sudah pasti menjadi milikmu dan tidak akan berubah lagi.',
                    'explanation' => 'Salah — keuntungan itu masih "di atas kertas" (unrealized) dan bisa berubah, naik maupun turun, sampai kamu benar-benar menjual sahamnya.',
                    'options' => [
                        ['text' => 'Benar'],
                        ['text' => 'Salah', 'correct' => true],
                    ],
                ],
            ],
            'bagaimana-investor-bisa-rugi' => [
                [
                    'question' => 'Kamu beli saham di Rp5.000, lalu jual di Rp3.500 (1.000 lembar). Berapa capital loss-mu?',
                    'explanation' => 'Capital Loss = (Rp5.000 − Rp3.500) × 1.000 lembar = Rp1.500.000.',
                    'options' => [
                        ['text' => 'Rp1.500'],
                        ['text' => 'Rp1.500.000', 'correct' => true],
                        ['text' => 'Rp3.500.000'],
                        ['text' => 'Rp5.000.000'],
                    ],
                ],
                [
                    'question' => 'Saham A bergerak ±1% per hari, Saham B bergerak ±8% per hari. Mana yang volatilitasnya lebih tinggi?',
                    'explanation' => 'Saham B memiliki volatilitas lebih tinggi karena pergerakan harganya jauh lebih besar dan tidak menentu dibanding Saham A.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Saham A'],
                        ['text' => 'Saham B', 'correct' => true],
                        ['text' => 'Sama saja'],
                        ['text' => 'Tidak bisa ditentukan'],
                    ],
                ],
                [
                    'question' => 'Apa skenario risiko paling ekstrem bagi pemegang saham?',
                    'explanation' => 'Skenario paling ekstrem adalah perusahaan bangkrut dan delisting, di mana pemegang saham bisa kehilangan hampir seluruh modal yang diinvestasikan.',
                    'options' => [
                        ['text' => 'Harga saham stagnan selama setahun'],
                        ['text' => 'Perusahaan tidak membagikan dividen'],
                        ['text' => 'Perusahaan bangkrut dan sahamnya delisting', 'correct' => true],
                        ['text' => 'Harga saham turun 2% dalam sehari'],
                    ],
                ],
            ],
            'saham-vs-aset-lain' => [
                [
                    'question' => 'Apa yang dimaksud dengan "likuiditas" sebuah aset?',
                    'explanation' => 'Likuiditas mengukur seberapa cepat dan mudah sebuah aset bisa diubah menjadi uang tunai tanpa kehilangan banyak nilai.',
                    'options' => [
                        ['text' => 'Seberapa besar keuntungan yang bisa didapat'],
                        ['text' => 'Seberapa cepat dan mudah aset diubah menjadi uang tunai', 'correct' => true],
                        ['text' => 'Seberapa aman aset itu dari risiko'],
                        ['text' => 'Seberapa mahal harga aset itu'],
                    ],
                ],
                [
                    'question' => 'Apa perbedaan mendasar antara memiliki saham dan memiliki obligasi suatu perusahaan?',
                    'explanation' => 'Pemegang saham memiliki sebagian perusahaan, sedangkan pemegang obligasi meminjamkan uang dan berhak atas bunga tetap serta pengembalian pokok, bukan kepemilikan.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Tidak ada bedanya'],
                        ['text' => 'Pemegang saham adalah pemilik perusahaan, pemegang obligasi adalah pemberi pinjaman', 'correct' => true],
                        ['text' => 'Obligasi hanya bisa dibeli institusi besar'],
                        ['text' => 'Saham selalu lebih aman dari obligasi'],
                    ],
                ],
                [
                    'question' => 'Benar atau salah: aset dengan potensi return lebih tinggi pada umumnya juga membawa risiko yang lebih tinggi.',
                    'explanation' => 'Benar — ini adalah pola umum dalam dunia investasi (trade-off return-risiko), meskipun bukan hukum mutlak yang berlaku di semua kondisi.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Benar', 'correct' => true],
                        ['text' => 'Salah'],
                    ],
                ],
            ],

            // Modul 2: Mekanisme Pasar & Transaksi Saham
            'apa-itu-bursa-efek' => [
                [
                    'question' => 'Apa fungsi utama sebuah Bursa Efek?',
                    'explanation' => 'Bursa Efek adalah pasar terpusat dan diawasi tempat semua order beli-jual saham dari seluruh investor dipertemukan dengan aturan yang sama untuk semua orang.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Mempertemukan order beli dan jual saham secara terpusat dan diawasi', 'correct' => true],
                        ['text' => 'Menjadi tempat perusahaan menyimpan seluruh uang tunainya'],
                        ['text' => 'Memberikan pinjaman modal langsung ke perusahaan'],
                        ['text' => 'Menetapkan harga saham secara sepihak'],
                    ],
                ],
                [
                    'question' => 'Apa nama bursa saham resmi di Indonesia?',
                    'explanation' => 'Bursa Efek Indonesia, disingkat IDX (atau BEI), adalah satu-satunya bursa saham resmi di Indonesia.',
                    'options' => [
                        ['text' => 'Bursa Efek Indonesia (IDX)', 'correct' => true],
                        ['text' => 'Bank Indonesia'],
                        ['text' => 'Otoritas Jasa Keuangan'],
                        ['text' => 'Kustodian Sentral Efek Indonesia'],
                    ],
                ],
                [
                    'question' => 'Mengapa transaksi saham perlu diawasi oleh otoritas seperti OJK?',
                    'explanation' => 'Karena uang banyak orang dipertaruhkan, pengawasan memastikan transparansi perusahaan tercatat dan transaksi berjalan adil bagi semua pihak.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Agar harga saham selalu naik'],
                        ['text' => 'Untuk menjaga transparansi dan keadilan transaksi bagi seluruh investor', 'correct' => true],
                        ['text' => 'Supaya hanya investor besar yang boleh bertransaksi'],
                        ['text' => 'Agar perusahaan tidak perlu melapor kinerja keuangannya'],
                    ],
                ],
            ],
            'broker-dan-rekening-efek' => [
                [
                    'question' => 'Mengapa investor perorangan membutuhkan broker untuk membeli saham?',
                    'explanation' => 'Sistem bursa hanya bisa diakses oleh anggota resmi berizin — investor perorangan harus lewat Perusahaan Sekuritas (broker) sebagai perantara.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Karena investor perorangan tidak punya akses langsung ke sistem bursa', 'correct' => true],
                        ['text' => 'Karena hukum mewajibkan setiap investor punya dua rekening'],
                        ['text' => 'Karena broker menentukan harga saham'],
                        ['text' => 'Karena bursa hanya buka untuk broker, bukan investor'],
                    ],
                ],
                [
                    'question' => 'Apa fungsi Rekening Dana Nasabah (RDN)?',
                    'explanation' => 'RDN adalah rekening bank khusus atas nama investor sendiri (terpisah dari rekening broker) untuk menyimpan dana secara aman sebelum dipakai bertransaksi.',
                    'options' => [
                        ['text' => 'Menyimpan dana investor secara terpisah dan aman dari aset broker', 'correct' => true],
                        ['text' => 'Menyimpan seluruh saham investor secara fisik'],
                        ['text' => 'Menjadi rekening operasional milik perusahaan sekuritas'],
                        ['text' => 'Menggantikan fungsi rekening bank biasa sepenuhnya'],
                    ],
                ],
                [
                    'question' => 'Benar atau salah: aplikasi Stock Recommendation ini bisa dipakai untuk benar-benar membeli dan menjual saham.',
                    'explanation' => 'Salah — aplikasi ini murni untuk riset dan belajar. Transaksi sungguhan hanya bisa dilakukan lewat aplikasi perusahaan sekuritas resmi yang terdaftar OJK.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Benar'],
                        ['text' => 'Salah', 'correct' => true],
                    ],
                ],
            ],
            'apa-itu-lot' => [
                [
                    'question' => 'Berapa jumlah lembar saham dalam 1 Lot di Bursa Efek Indonesia?',
                    'explanation' => '1 Lot di IDX setara dengan 100 lembar saham — pembelian harus dalam kelipatan lot.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => '10 lembar'],
                        ['text' => '100 lembar', 'correct' => true],
                        ['text' => '1.000 lembar'],
                        ['text' => 'Tergantung harga sahamnya'],
                    ],
                ],
                [
                    'question' => 'Harga saham TLKM Rp2.620 per lembar. Berapa nilai transaksi untuk membeli 2 lot (belum termasuk biaya broker)?',
                    'explanation' => '2 lot = 200 lembar. Nilai transaksi = Rp2.620 × 200 = Rp524.000.',
                    'options' => [
                        ['text' => 'Rp5.240'],
                        ['text' => 'Rp52.400'],
                        ['text' => 'Rp524.000', 'correct' => true],
                        ['text' => 'Rp2.620.000'],
                    ],
                ],
                [
                    'question' => 'Harga yang ditampilkan di halaman Saham pada aplikasi ini adalah harga per apa?',
                    'explanation' => 'Harga yang ditampilkan selalu per lembar saham, bukan per lot — perlu dikalikan 100 untuk memperkirakan nilai transaksi per lot.',
                    'options' => [
                        ['text' => 'Per lot'],
                        ['text' => 'Per lembar', 'correct' => true],
                        ['text' => 'Per 10 lembar'],
                        ['text' => 'Per transaksi'],
                    ],
                ],
            ],
            'bid-ask-dan-spread' => [
                [
                    'question' => 'Apa yang dimaksud dengan "bid" pada suatu saham?',
                    'explanation' => 'Bid adalah harga tertinggi yang bersedia dibayar oleh calon pembeli saat itu.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Harga tertinggi yang bersedia dibayar calon pembeli', 'correct' => true],
                        ['text' => 'Harga terendah yang diminta calon penjual'],
                        ['text' => 'Harga penutupan hari sebelumnya'],
                        ['text' => 'Selisih antara harga tertinggi dan terendah hari itu'],
                    ],
                ],
                [
                    'question' => 'Bid suatu saham Rp4.760 dan ask-nya Rp4.780. Berapa spread-nya?',
                    'explanation' => 'Spread = Ask − Bid = Rp4.780 − Rp4.760 = Rp20.',
                    'options' => [
                        ['text' => 'Rp4.760'],
                        ['text' => 'Rp4.780'],
                        ['text' => 'Rp20', 'correct' => true],
                        ['text' => 'Rp9.540'],
                    ],
                ],
                [
                    'question' => 'Spread yang sangat lebar pada suatu saham biasanya menandakan apa?',
                    'explanation' => 'Spread lebar menandakan saham itu jarang diperdagangkan (likuiditas rendah) — sedikit pembeli/penjual yang saling mendekati harga wajar.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Saham itu sangat aktif diperdagangkan'],
                        ['text' => 'Saham itu jarang diperdagangkan / likuiditasnya rendah', 'correct' => true],
                        ['text' => 'Harga saham itu pasti akan naik'],
                        ['text' => 'Perusahaan itu baru saja IPO'],
                    ],
                ],
            ],
            'jenis-order-market-limit' => [
                [
                    'question' => 'Apa ciri utama sebuah Market Order?',
                    'explanation' => 'Market Order dieksekusi segera di harga pasar terbaik yang tersedia saat itu, tanpa menentukan harga spesifik — cepat tapi harga tidak pasti.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Dieksekusi segera di harga pasar terbaik yang tersedia', 'correct' => true],
                        ['text' => 'Hanya dieksekusi pada harga tertentu yang ditentukan investor'],
                        ['text' => 'Selalu dieksekusi di harga terendah sepanjang hari'],
                        ['text' => 'Tidak akan pernah dieksekusi'],
                    ],
                ],
                [
                    'question' => 'Investor yang ingin kepastian penuh atas harga transaksinya, meskipun ordernya mungkin tidak langsung tereksekusi, sebaiknya memakai jenis order apa?',
                    'explanation' => 'Limit Order memberi kepastian harga (order hanya tereksekusi di harga yang ditentukan atau lebih baik), dengan trade-off kepastian eksekusi yang lebih rendah.',
                    'options' => [
                        ['text' => 'Market Order'],
                        ['text' => 'Limit Order', 'correct' => true],
                        ['text' => 'Keduanya sama saja'],
                        ['text' => 'Tidak ada jenis order yang memberi kepastian harga'],
                    ],
                ],
                [
                    'question' => 'Benar atau salah: Limit Order dijamin selalu tereksekusi selama pasar masih buka.',
                    'explanation' => 'Salah — Limit Order hanya tereksekusi kalau harga pasar benar-benar mencapai harga yang ditentukan. Kalau tidak pernah tercapai, order itu tidak akan tereksekusi.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Benar'],
                        ['text' => 'Salah', 'correct' => true],
                    ],
                ],
            ],
            'auto-reject-dan-batas-harga' => [
                [
                    'question' => 'Apa tujuan utama mekanisme Auto Reject di bursa?',
                    'explanation' => 'Auto Reject membatasi kenaikan/penurunan harga maksimum dalam sehari untuk mencegah volatilitas ekstrem dan potensi manipulasi harga.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Mencegah volatilitas ekstrem dan potensi manipulasi harga', 'correct' => true],
                        ['text' => 'Memastikan harga saham selalu naik setiap hari'],
                        ['text' => 'Mempercepat proses settlement transaksi'],
                        ['text' => 'Menentukan dividen yang dibagikan perusahaan'],
                    ],
                ],
                [
                    'question' => 'ARA dan ARB masing-masing adalah singkatan dari apa?',
                    'explanation' => 'ARA = Auto Reject Atas (batas kenaikan harga), ARB = Auto Reject Bawah (batas penurunan harga).',
                    'options' => [
                        ['text' => 'Auto Reject Atas dan Auto Reject Bawah', 'correct' => true],
                        ['text' => 'Analisis Risiko Atas dan Analisis Risiko Bawah'],
                        ['text' => 'Aturan Resmi Awal dan Aturan Resmi Berakhir'],
                        ['text' => 'Akun Rekening Aktif dan Akun Rekening Beku'],
                    ],
                ],
                [
                    'question' => 'Kalau kamu melihat lonjakan harga historis yang sangat ekstrem (di luar wajar) pada data sebuah saham, apa kesimpulan yang paling masuk akal?',
                    'explanation' => 'Karena ada mekanisme Auto Reject, lonjakan ekstrem dalam kondisi normal tidak mungkin terjadi — kemungkinan besar itu tanda data belum bersih (misalnya akibat aksi korporasi yang belum disesuaikan).',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Itu pasti pergerakan pasar yang wajar dan sah'],
                        ['text' => 'Kemungkinan besar ada masalah pada data itu sendiri', 'correct' => true],
                        ['text' => 'Bursa pasti sedang libur saat itu'],
                        ['text' => 'Saham itu pasti baru IPO'],
                    ],
                ],
            ],
            'jam-dan-hari-perdagangan' => [
                [
                    'question' => 'Apa yang dimaksud dengan "Hari Bursa"?',
                    'explanation' => 'Hari Bursa adalah hari kerja Senin–Jumat saat IDX buka untuk perdagangan, tidak termasuk hari libur nasional/bursa.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Setiap hari tanpa kecuali, termasuk akhir pekan'],
                        ['text' => 'Hari kerja Senin–Jumat, kecuali libur nasional/bursa', 'correct' => true],
                        ['text' => 'Hanya hari Senin setiap minggu'],
                        ['text' => 'Kapan pun investor memilih untuk bertransaksi'],
                    ],
                ],
                [
                    'question' => 'Mengapa tabel Riwayat Harga di aplikasi ini tidak pernah punya baris untuk hari Sabtu atau Minggu?',
                    'explanation' => 'Karena bursa tidak beroperasi di akhir pekan — tidak ada transaksi sama sekali di hari-hari itu, sehingga tidak ada data untuk dicatat.',
                    'options' => [
                        ['text' => 'Karena data akhir pekan sengaja dihapus dari sistem'],
                        ['text' => 'Karena bursa tidak beroperasi di akhir pekan, sehingga tidak ada transaksi untuk dicatat', 'correct' => true],
                        ['text' => 'Karena aplikasi ini belum selesai dibuat'],
                        ['text' => 'Karena harga saham selalu sama dengan hari Jumat'],
                    ],
                ],
                [
                    'question' => '"Return 20 hari" pada Skor Momentum di aplikasi ini menghitung 20 hari dalam pengertian apa?',
                    'explanation' => 'Perhitungan ini menggunakan 20 hari bursa (hari kerja transaksi), bukan 20 hari kalender biasa yang mencakup akhir pekan.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => '20 hari kalender berturut-turut termasuk akhir pekan'],
                        ['text' => '20 hari bursa (hari kerja perdagangan)', 'correct' => true],
                        ['text' => '20 minggu'],
                        ['text' => '20 jam perdagangan'],
                    ],
                ],
            ],
            'kode-saham-dan-papan-pencatatan' => [
                [
                    'question' => 'Apa itu "Ticker" atau kode saham?',
                    'explanation' => 'Ticker adalah kode unik (biasanya 4 huruf di IDX) yang mewakili sebuah perusahaan tercatat di bursa, seperti "BBCA" untuk Bank Central Asia.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Kode unik yang mewakili sebuah perusahaan tercatat di bursa', 'correct' => true],
                        ['text' => 'Nomor rekening efek investor'],
                        ['text' => 'Nama lengkap resmi perusahaan'],
                        ['text' => 'Kode negara tempat perusahaan berdiri'],
                    ],
                ],
                [
                    'question' => 'Apa fungsi Papan Pencatatan di IDX?',
                    'explanation' => 'Papan Pencatatan mengelompokkan perusahaan tercatat berdasarkan kriteria seperti ukuran, lama beroperasi, dan profitabilitas — misalnya Papan Utama vs Papan Pengembangan.',
                    'options' => [
                        ['text' => 'Mengelompokkan perusahaan tercatat berdasarkan kriteria seperti ukuran dan kinerja', 'correct' => true],
                        ['text' => 'Menentukan harga saham secara otomatis'],
                        ['text' => 'Menampilkan jadwal libur bursa'],
                        ['text' => 'Mencatat riwayat transaksi tiap investor'],
                    ],
                ],
                [
                    'question' => 'Kalau kolom "Papan" pada suatu saham di aplikasi ini kosong ("-"), apa artinya?',
                    'explanation' => 'Ini adalah data pengembangan yang belum lengkap diisi — bukan berarti sahamnya tidak tercatat di papan manapun.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Saham itu tidak tercatat resmi di bursa manapun'],
                        ['text' => 'Data papan pencatatannya belum lengkap diisi di sistem kita', 'correct' => true],
                        ['text' => 'Saham itu baru saja delisting'],
                        ['text' => 'Saham itu berada di semua papan sekaligus'],
                    ],
                ],
            ],

            // Modul 3: Membaca Data Harga Saham
            'apa-itu-data-ohlc' => [
                [
                    'question' => 'OHLC adalah singkatan dari apa?',
                    'explanation' => 'OHLC = Open (harga pembukaan), High (tertinggi), Low (terendah), Close (penutupan) — empat angka harga yang dicatat setiap hari perdagangan.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Open, High, Low, Close', 'correct' => true],
                        ['text' => 'Order, Harga, Lot, Cash'],
                        ['text' => 'Open, Hold, Liquidate, Close'],
                        ['text' => 'Owner, History, Level, Change'],
                    ],
                ],
                [
                    'question' => 'Sebuah saham mencatat Open 5.000, High 5.200, Low 4.950, Close 5.180. Apa yang bisa disimpulkan dari posisi Close yang dekat dengan High?',
                    'explanation' => 'Close yang dekat dengan High menandakan sentimen pembeli cenderung menguat menjelang penutupan hari itu.',
                    'options' => [
                        ['text' => 'Tekanan jual menguat menjelang penutupan'],
                        ['text' => 'Sentimen pembeli cenderung menguat menjelang penutupan', 'correct' => true],
                        ['text' => 'Tidak ada transaksi terjadi hari itu'],
                        ['text' => 'Harga pasti akan turun keesokan harinya'],
                    ],
                ],
                [
                    'question' => 'Kenapa harga saham dicatat dengan 4 angka (OHLC) per hari, bukan cuma 1 angka?',
                    'explanation' => 'Karena harga bergerak sepanjang hari — satu angka saja akan kehilangan banyak cerita penting tentang bagaimana pergerakan itu terjadi.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Karena harga saham berubah sepanjang hari dan satu angka saja tidak cukup menceritakannya', 'correct' => true],
                        ['text' => 'Karena aturan bursa mewajibkan pencatatan berlebih'],
                        ['text' => 'Karena setiap saham punya 4 harga berbeda secara bersamaan'],
                        ['text' => 'Supaya data lebih sulit dibaca oleh pemula'],
                    ],
                ],
            ],
            'membaca-grafik-harga' => [
                [
                    'question' => 'Grafik "Harga Penutupan" di halaman Detail Saham pada aplikasi ini adalah jenis grafik apa?',
                    'explanation' => 'Aplikasi ini memakai Line Chart yang menghubungkan harga Close setiap hari, dipilih karena lebih sederhana dibaca pemula dibanding candlestick.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Line Chart berbasis harga Close', 'correct' => true],
                        ['text' => 'Candlestick Chart'],
                        ['text' => 'Diagram batang volume'],
                        ['text' => 'Diagram lingkaran (pie chart)'],
                    ],
                ],
                [
                    'question' => 'Apa kelebihan utama Candlestick Chart dibanding Line Chart?',
                    'explanation' => 'Candlestick menampilkan keempat angka OHLC sekaligus per hari, memberi informasi lebih kaya (termasuk volatilitas intra-hari) dibanding line chart yang hanya memakai Close.',
                    'options' => [
                        ['text' => 'Lebih sederhana dan lebih mudah dibaca pemula'],
                        ['text' => 'Menampilkan keempat angka OHLC sekaligus per hari, informasi lebih kaya', 'correct' => true],
                        ['text' => 'Tidak membutuhkan data historis sama sekali'],
                        ['text' => 'Hanya menampilkan volume perdagangan'],
                    ],
                ],
                [
                    'question' => 'Garis putus-putus hijau dan merah yang kadang muncul pada grafik harga di aplikasi ini menandakan apa?',
                    'explanation' => 'Garis-garis itu adalah level Support (hijau) dan Resistance (merah) yang dideteksi otomatis dari titik balik harga historis — dibahas lengkap di Pelajaran 5.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Batas Auto Reject harian'],
                        ['text' => 'Level Support dan Resistance', 'correct' => true],
                        ['text' => 'Rata-rata bergerak MA20 dan MA50'],
                        ['text' => 'Prediksi harga besok'],
                    ],
                ],
            ],
            'apa-itu-volume-perdagangan' => [
                [
                    'question' => 'Apa definisi Volume Perdagangan?',
                    'explanation' => 'Volume adalah jumlah total lembar saham yang berpindah tangan (diperjualbelikan) dalam satu hari perdagangan.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Jumlah total lembar saham yang berpindah tangan dalam sehari', 'correct' => true],
                        ['text' => 'Jumlah investor yang memiliki saham tersebut'],
                        ['text' => 'Selisih harga tertinggi dan terendah hari itu'],
                        ['text' => 'Jumlah hari saham itu sudah tercatat di bursa'],
                    ],
                ],
                [
                    'question' => 'Saham naik 2% dengan volume jauh di atas rata-rata harian. Apa yang bisa disimpulkan?',
                    'explanation' => 'Volume tinggi menandakan banyak investor beramai-ramai membeli — sinyal kepercayaan pasar yang lebih kuat terhadap kenaikan itu, dibanding kenaikan dengan volume rendah.',
                    'options' => [
                        ['text' => 'Kenaikan itu kurang meyakinkan'],
                        ['text' => 'Kenaikan itu didukung banyak investor, lebih meyakinkan', 'correct' => true],
                        ['text' => 'Harga pasti akan turun besok'],
                        ['text' => 'Volume tidak ada hubungannya dengan pergerakan harga'],
                    ],
                ],
                [
                    'question' => 'Manakah pernyataan yang paling tepat tentang hubungan harga dan volume?',
                    'explanation' => 'Aturan praktis umum: pergerakan harga yang didukung volume tinggi cenderung lebih dipercaya untuk berlanjut, dibanding pergerakan dengan volume rendah yang lebih rentan berbalik arah.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Pergerakan harga dengan volume tinggi cenderung lebih meyakinkan untuk berlanjut', 'correct' => true],
                        ['text' => 'Volume tidak pernah relevan untuk analisis harga'],
                        ['text' => 'Volume rendah selalu berarti harga akan naik'],
                        ['text' => 'Volume hanya penting untuk saham baru IPO'],
                    ],
                ],
            ],
            'mengenali-tren-harga' => [
                [
                    'question' => 'Apa ciri sebuah Uptrend (tren naik)?',
                    'explanation' => 'Uptrend ditandai titik-titik terendah berturut-turut yang cenderung lebih tinggi dari titik terendah sebelumnya, meski ada naik-turun kecil di sepanjang jalan.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Harga naik terus tanpa pernah turun sedikit pun'],
                        ['text' => 'Harga secara umum bergerak naik, dengan titik terendah yang cenderung meninggi', 'correct' => true],
                        ['text' => 'Harga bergerak dalam rentang sempit tanpa arah jelas'],
                        ['text' => 'Harga turun setiap hari berturut-turut'],
                    ],
                ],
                [
                    'question' => 'Sebuah saham bergerak naik-turun dalam rentang sempit selama sebulan terakhir tanpa arah jelas. Tren apa ini?',
                    'explanation' => 'Ini disebut Sideways atau Konsolidasi — harga "beristirahat" sebelum memilih arah berikutnya.',
                    'options' => [
                        ['text' => 'Uptrend'],
                        ['text' => 'Downtrend'],
                        ['text' => 'Sideways / Konsolidasi', 'correct' => true],
                        ['text' => 'Auto Reject'],
                    ],
                ],
                [
                    'question' => 'Benar atau salah: sebuah saham dalam uptrend tidak akan pernah mengalami penurunan harga harian.',
                    'explanation' => 'Salah — hampir semua tren punya gerakan zig-zag kecil di sepanjang jalan. Yang menentukan arah tren adalah pola keseluruhan, bukan setiap gerakan harian.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Benar'],
                        ['text' => 'Salah', 'correct' => true],
                    ],
                ],
            ],
            'support-dan-resistance' => [
                [
                    'question' => 'Apa yang dimaksud dengan level "Support" pada suatu saham?',
                    'explanation' => 'Support adalah level harga di mana saham secara historis cenderung berhenti turun dan berbalik naik, seperti "lantai" harga.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Level harga di mana saham cenderung berhenti turun dan berbalik naik', 'correct' => true],
                        ['text' => 'Level harga tertinggi yang pernah dicapai saham sepanjang sejarah'],
                        ['text' => 'Level harga di mana saham cenderung berhenti naik'],
                        ['text' => 'Rata-rata harga saham selama setahun terakhir'],
                    ],
                ],
                [
                    'question' => 'Sebuah level Resistance yang berhasil "ditembus" oleh kenaikan harga sering kali berubah peran menjadi apa?',
                    'explanation' => 'Level resistance yang tertembus sering berubah peran menjadi support baru, begitu juga sebaliknya — fenomena umum dalam analisis level harga.',
                    'options' => [
                        ['text' => 'Support baru', 'correct' => true],
                        ['text' => 'Hilang sepenuhnya dan tidak relevan lagi'],
                        ['text' => 'Batas Auto Reject baru'],
                        ['text' => 'Harga pembukaan hari berikutnya'],
                    ],
                ],
                [
                    'question' => 'Bagaimana fitur "Analisis Lanjutan" di aplikasi ini mendeteksi level Support dan Resistance?',
                    'explanation' => 'Sistem mendeteksi titik-titik balik (swing high/low) dari data harga historis dan mengelompokkan level yang berdekatan — murni pola historis, bukan jaminan masa depan.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Secara otomatis dari titik-titik balik harga historis', 'correct' => true],
                        ['text' => 'Berdasarkan prediksi kecerdasan buatan tentang harga masa depan'],
                        ['text' => 'Dari opini analis yang dimasukkan manual'],
                        ['text' => 'Dari harga rata-rata seluruh saham di sektor yang sama'],
                    ],
                ],
            ],
            'memahami-perubahan-harga-harian' => [
                [
                    'question' => 'Dari harga apa "Change" (perubahan harga harian) dihitung?',
                    'explanation' => 'Change dihitung dari selisih Close hari ini dengan Close hari bursa sebelumnya (bukan hari kalender sebelumnya).',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Selisih Close hari ini dengan Close hari bursa sebelumnya', 'correct' => true],
                        ['text' => 'Selisih High dan Low hari ini'],
                        ['text' => 'Selisih Open dan Close hari ini'],
                        ['text' => 'Rata-rata harga selama sebulan terakhir'],
                    ],
                ],
                [
                    'question' => 'Saham ditutup Rp8.400 kemarin dan Rp8.820 hari ini. Berapa change percent-nya?',
                    'explanation' => 'Change = 8.820 − 8.400 = 420. Change Percent = (420 / 8.400) × 100% = 5%.',
                    'options' => [
                        ['text' => '2%'],
                        ['text' => '5%', 'correct' => true],
                        ['text' => '8%'],
                        ['text' => '42%'],
                    ],
                ],
                [
                    'question' => 'Mengapa perubahan harga juga dinyatakan dalam persentase (change percent), tidak hanya Rupiah?',
                    'explanation' => 'Persentase memudahkan perbandingan antar saham dengan harga yang jauh berbeda — kenaikan Rp100 punya arti berbeda pada saham Rp1.000 dibanding saham Rp10.000.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Agar mudah dibandingkan antar saham dengan harga yang berbeda-beda', 'correct' => true],
                        ['text' => 'Karena aturan bursa mewajibkannya'],
                        ['text' => 'Karena angka Rupiah tidak akurat'],
                        ['text' => 'Supaya terlihat lebih besar dari angka sebenarnya'],
                    ],
                ],
            ],
            'data-historis-vs-real-time' => [
                [
                    'question' => 'Apa perbedaan utama data historis dan data real-time?',
                    'explanation' => 'Data real-time diperbarui tiap detik mengikuti transaksi langsung di bursa; data historis adalah data hari-hari sebelumnya yang sudah selesai dan tercatat, dipakai untuk analisis pola.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Data real-time diperbarui tiap detik, data historis adalah data hari-hari sebelumnya', 'correct' => true],
                        ['text' => 'Tidak ada perbedaan, keduanya sama saja'],
                        ['text' => 'Data historis hanya berisi harga saham asing'],
                        ['text' => 'Data real-time hanya tersedia untuk investor institusi'],
                    ],
                ],
                [
                    'question' => 'Dari mana aplikasi ini mendapatkan data harga sahamnya?',
                    'explanation' => 'Aplikasi ini mengambil data historis dari Yahoo Finance lewat endpoint publik yang tidak resmi, disinkronkan manual atau terjadwal — bukan sumber data resmi berbayar.',
                    'options' => [
                        ['text' => 'Endpoint publik tidak resmi dari Yahoo Finance', 'correct' => true],
                        ['text' => 'Langsung dari server resmi Bursa Efek Indonesia'],
                        ['text' => 'Dimasukkan manual oleh admin setiap detik'],
                        ['text' => 'Dari aplikasi trading berbayar resmi'],
                    ],
                ],
                [
                    'question' => 'Benar atau salah: karena sumber datanya tidak resmi, aplikasi ini cocok dipakai untuk mengambil keputusan transaksi mendadak berdasarkan harga detik ini juga.',
                    'explanation' => 'Salah — aplikasi ini dirancang sebagai alat belajar dan riset berbasis data historis yang disinkronkan berkala, bukan terminal trading real-time.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Benar'],
                        ['text' => 'Salah', 'correct' => true],
                    ],
                ],
            ],
            'latihan-membaca-halaman-detail-saham' => [
                [
                    'question' => 'Badge hijau/merah di samping harga saham pada halaman Detail Saham mencerminkan konsep apa yang sudah kamu pelajari?',
                    'explanation' => 'Badge itu menampilkan Change dan Change Percent, dihitung dari selisih Close hari ini dengan Close hari bursa sebelumnya.',
                    'difficulty' => 'easy',
                    'options' => [
                        ['text' => 'Change dan Change Percent', 'correct' => true],
                        ['text' => 'Level Support dan Resistance'],
                        ['text' => 'Bid dan Ask'],
                        ['text' => 'Papan Pencatatan'],
                    ],
                ],
                [
                    'question' => 'Garis hijau/merah putus-putus pada grafik harga, dan angka OHLCV pada tabel di bawahnya, sama-sama berasal dari mana?',
                    'explanation' => 'Keduanya dibangun dari data mentah OHLCV (Open, High, Low, Close, Volume) yang sama — garis adalah hasil analisis Support/Resistance dari data itu.',
                    'options' => [
                        ['text' => 'Data mentah OHLCV yang sama, diolah dengan cara berbeda', 'correct' => true],
                        ['text' => 'Dua sumber data yang sepenuhnya terpisah'],
                        ['text' => 'Prediksi manual dari analis'],
                        ['text' => 'Data real-time yang berbeda dari tabel riwayat harga'],
                    ],
                ],
                [
                    'question' => 'Setelah menyelesaikan Modul 1–3, kemampuan inti apa yang seharusnya sudah kamu kuasai?',
                    'explanation' => 'Kemampuan menghubungkan setiap elemen visual di halaman Detail Saham (harga, tren, level, riwayat) kembali ke konsep dasarnya — dari kepemilikan saham, mekanisme transaksi, sampai membaca data harga.',
                    'difficulty' => 'hard',
                    'options' => [
                        ['text' => 'Menganalisis laporan keuangan perusahaan secara mendalam'],
                        ['text' => 'Membaca dan memahami halaman Detail Saham secara utuh dan bermakna', 'correct' => true],
                        ['text' => 'Memprediksi harga saham dengan akurat'],
                        ['text' => 'Membuat strategi trading algoritmik'],
                    ],
                ],
            ],
        ];
    }
}
