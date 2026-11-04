<?php

namespace Tests\Feature\Models;

use App\Enums\MenuItemType;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_child_item_inherits_menu_from_parent(): void
    {
        $parent = MenuItem::factory()->create();

        $child = $parent->children()->create(['label' => 'Sub', 'url' => '/sub']);

        $this->assertSame($parent->menu_id, $child->menu_id);
    }

    public function test_linked_item_resolves_linkable_and_clears_url(): void
    {
        $page = Page::factory()->create();

        $item = MenuItem::factory()->forPage($page)->create(['url' => '/ignored']);

        $this->assertNull($item->url);
        $this->assertTrue($item->linkable->is($page));
    }

    public function test_switching_item_to_custom_clears_linkable(): void
    {
        $item = MenuItem::factory()->forPage(Page::factory()->create())->create();

        $item->update(['type' => MenuItemType::Custom, 'url' => '/kontak']);

        $this->assertNull($item->fresh()->linkable_type);
        $this->assertNull($item->fresh()->linkable_id);
        $this->assertSame('/kontak', $item->fresh()->url);
    }
}
