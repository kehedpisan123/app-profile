<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@example.com')->firstOrFail();

        $pages = [
            ['title' => 'Tentang Kami', 'slug' => 'tentang-kami', 'content' => '<p>Gambaran umum, sejarah, dan tugas pokok instansi.</p>'],
            ['title' => 'Visi dan Misi', 'slug' => 'visi-misi', 'content' => '<h2>Visi</h2><p>Terwujudnya pelayanan publik yang prima.</p><h2>Misi</h2><ol><li>Meningkatkan kualitas layanan.</li><li>Mewujudkan tata kelola yang transparan.</li></ol>'],
            ['title' => 'Struktur Organisasi', 'slug' => 'struktur-organisasi', 'content' => '<p>Susunan organisasi dan tata kerja instansi.</p>'],
            ['title' => 'Kontak', 'slug' => 'kontak', 'content' => '<p>Hubungi kami melalui kanal resmi yang tersedia.</p>'],
        ];

        foreach ($pages as $sortOrder => $page) {
            Page::query()->firstOrCreate(
                ['slug' => $page['slug']],
                [...$page, 'user_id' => $author->id, 'status' => PageStatus::Published, 'sort_order' => $sortOrder, 'show_in_menu' => true],
            );
        }
    }
}
