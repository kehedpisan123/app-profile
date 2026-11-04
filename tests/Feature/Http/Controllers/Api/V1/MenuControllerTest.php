<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Enums\MenuLocation;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_active_items_with_children_and_linked_slugs(): void
    {
        $page = Page::factory()->create(['slug' => 'tentang-kami']);
        $menu = Menu::factory()->create(['location' => MenuLocation::Header]);
        $profile = MenuItem::factory()->for($menu)->create(['label' => 'Profil', 'sort_order' => 1]);
        MenuItem::factory()->for($menu)->create(['label' => 'Kontak', 'sort_order' => 2]);
        MenuItem::factory()->for($menu)->inactive()->create(['label' => 'Tersembunyi']);
        MenuItem::factory()->forPage($page)->create(['menu_id' => null, 'parent_id' => $profile->id]);

        $response = $this->getJson(route('api.v1.menus.show', ['location' => 'header']));

        $response->assertOk()
            ->assertJsonPath('data.location', 'header')
            ->assertJsonCount(2, 'data.items')
            ->assertJsonPath('data.items.0.label', 'Profil')
            ->assertJsonPath('data.items.0.children.0.type', 'page')
            ->assertJsonPath('data.items.0.children.0.slug', 'tentang-kami')
            ->assertJsonPath('data.items.1.label', 'Kontak');
    }

    public function test_show_returns_404_for_unknown_location(): void
    {
        $this->getJson('/api/v1/menus/unknown')->assertNotFound();
    }

    public function test_show_returns_404_when_location_has_only_inactive_menu(): void
    {
        Menu::factory()->inactive()->create(['location' => MenuLocation::Footer]);

        $this->getJson(route('api.v1.menus.show', ['location' => 'footer']))->assertNotFound();
    }
}
