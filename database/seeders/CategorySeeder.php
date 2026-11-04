<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Berita', 'slug' => 'berita', 'icon' => 'newspaper', 'description' => 'Kabar terbaru kegiatan instansi.'],
            ['name' => 'Pengumuman', 'slug' => 'pengumuman', 'icon' => 'megaphone', 'description' => 'Informasi resmi untuk masyarakat.'],
            ['name' => 'Agenda', 'slug' => 'agenda', 'icon' => 'calendar', 'description' => 'Jadwal kegiatan dan acara.'],
            ['name' => 'Artikel', 'slug' => 'artikel', 'icon' => 'book-open', 'description' => 'Tulisan dan wawasan seputar layanan.'],
        ];

        foreach ($categories as $sortOrder => $category) {
            Category::query()->firstOrCreate(
                ['slug' => $category['slug']],
                [...$category, 'sort_order' => $sortOrder, 'is_active' => true],
            );
        }
    }
}
