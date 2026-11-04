<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\QuestionTopic;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            'pelayanan-publik' => [
                ['question' => 'Kapan jam layanan kantor?', 'answer' => '<p>Layanan dibuka Senin–Kamis pukul 08.00–16.00 dan Jumat pukul 08.00–16.30.</p>'],
                ['question' => 'Apakah layanan dikenakan biaya?', 'answer' => '<p>Seluruh layanan tidak dipungut biaya kecuali yang diatur oleh peraturan.</p>'],
            ],
            'administrasi' => [
                ['question' => 'Dokumen apa saja yang perlu disiapkan?', 'answer' => '<p>Siapkan KTP, KK, dan dokumen pendukung sesuai jenis layanan.</p>'],
            ],
            'perizinan' => [
                ['question' => 'Berapa lama proses perizinan?', 'answer' => '<p>Proses perizinan selesai paling lama 14 hari kerja sejak berkas lengkap.</p>'],
            ],
        ];

        foreach ($faqs as $topicSlug => $topicFaqs) {
            $topic = QuestionTopic::query()->where('slug', $topicSlug)->first();

            foreach ($topicFaqs as $sortOrder => $faq) {
                Faq::query()->firstOrCreate(
                    ['question' => $faq['question']],
                    [...$faq, 'topic_id' => $topic?->id, 'sort_order' => $sortOrder, 'is_active' => true],
                );
            }
        }
    }
}
