<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_published_pages_in_sort_order(): void
    {
        $second = Page::factory()->create(['sort_order' => 2]);
        $first = Page::factory()->create(['sort_order' => 1]);
        Page::factory()->draft()->create();

        $response = $this->getJson(route('api.v1.pages.index'));

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $first->id)
            ->assertJsonPath('data.1.id', $second->id);
    }

    public function test_show_returns_published_page_by_slug(): void
    {
        $page = Page::factory()->create(['slug' => 'visi-misi', 'content' => '<p>Visi</p>']);

        $response = $this->getJson(route('api.v1.pages.show', $page));

        $response->assertOk()
            ->assertJsonPath('data.slug', 'visi-misi')
            ->assertJsonPath('data.content', '<p>Visi</p>');
    }

    public function test_show_returns_404_for_draft_page(): void
    {
        $page = Page::factory()->draft()->create();

        $this->getJson(route('api.v1.pages.show', $page))->assertNotFound();
    }
}
