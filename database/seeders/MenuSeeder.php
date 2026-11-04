<?php

namespace Database\Seeders;

use App\Enums\MenuItemType;
use App\Enums\MenuLocation;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Page;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $header = Menu::query()->firstOrCreate(
            ['location' => MenuLocation::Header, 'name' => 'Menu Utama'],
            ['is_active' => true],
        );

        if ($header->items()->doesntExist()) {
            $header->items()->create(['label' => 'Beranda', 'type' => MenuItemType::Custom, 'url' => '/', 'sort_order' => 1]);

            $profile = $header->items()->create(['label' => 'Profil', 'type' => MenuItemType::Custom, 'url' => '#', 'sort_order' => 2]);

            Page::query()->where('show_in_menu', true)->orderBy('sort_order')->get()
                ->each(fn (Page $page, int $index) => $profile->children()->create([
                    'menu_id' => $header->id,
                    'label' => $page->title,
                    'type' => MenuItemType::Page,
                    'linkable_id' => $page->id,
                    'sort_order' => $index + 1,
                ]));

            $news = Category::query()->where('slug', 'berita')->first();

            if ($news) {
                $header->items()->create(['label' => 'Berita', 'type' => MenuItemType::Category, 'linkable_id' => $news->id, 'sort_order' => 3]);
            }

            $header->items()->create(['label' => 'FAQ', 'type' => MenuItemType::Custom, 'url' => '/faq', 'sort_order' => 4]);
            $header->items()->create(['label' => 'Tanya Kami', 'type' => MenuItemType::Custom, 'url' => '/pertanyaan', 'sort_order' => 5]);
        }

        $footer = Menu::query()->firstOrCreate(
            ['location' => MenuLocation::Footer, 'name' => 'Menu Footer'],
            ['is_active' => true],
        );

        if ($footer->items()->doesntExist()) {
            $footer->items()->create(['label' => 'Kebijakan Privasi', 'type' => MenuItemType::Custom, 'url' => '/kebijakan-privasi', 'sort_order' => 1]);
            $footer->items()->create(['label' => 'Kontak', 'type' => MenuItemType::Custom, 'url' => '/kontak', 'sort_order' => 2]);
        }
    }
}
