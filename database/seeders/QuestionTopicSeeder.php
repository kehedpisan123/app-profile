<?php

namespace Database\Seeders;

use App\Models\QuestionTopic;
use Illuminate\Database\Seeder;

class QuestionTopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            ['name' => 'Pelayanan Publik', 'slug' => 'pelayanan-publik', 'icon' => 'building-office'],
            ['name' => 'Administrasi', 'slug' => 'administrasi', 'icon' => 'document-text'],
            ['name' => 'Perizinan', 'slug' => 'perizinan', 'icon' => 'clipboard-document-check'],
            ['name' => 'Pengaduan', 'slug' => 'pengaduan', 'icon' => 'exclamation-triangle'],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'icon' => 'chat-bubble-left-right'],
        ];

        foreach ($topics as $sortOrder => $topic) {
            QuestionTopic::query()->firstOrCreate(
                ['slug' => $topic['slug']],
                [...$topic, 'sort_order' => $sortOrder, 'is_active' => true],
            );
        }
    }
}
