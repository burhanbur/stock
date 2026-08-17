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
        ];
    }
}
