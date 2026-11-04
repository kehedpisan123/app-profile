<?php

namespace Tests\Feature\Http\Controllers\Api\V1;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_only_published_posts(): void
    {
        $published = Post::factory()->published()->create();
        Post::factory()->create();
        Post::factory()->scheduled()->create();
        Post::factory()->archived()->create();

        $response = $this->getJson(route('api.v1.posts.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.slug', $published->slug)
            ->assertJsonMissingPath('data.0.content');
    }

    public function test_index_filters_posts_by_category_slug(): void
    {
        $news = Category::factory()->create(['slug' => 'berita']);
        $newsPost = Post::factory()->published()->for($news)->create();
        Post::factory()->published()->create();

        $response = $this->getJson(route('api.v1.posts.index', ['category' => 'berita']));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $newsPost->id)
            ->assertJsonPath('data.0.category.slug', 'berita');
    }

    public function test_index_filters_featured_posts(): void
    {
        $featured = Post::factory()->published()->featured()->create();
        Post::factory()->published()->create();

        $response = $this->getJson(route('api.v1.posts.index', ['featured' => 1]));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $featured->id);
    }

    public function test_index_searches_posts_by_title(): void
    {
        $match = Post::factory()->published()->create(['title' => 'Jadwal Vaksinasi Massal']);
        Post::factory()->published()->create(['title' => 'Rapat Koordinasi']);

        $response = $this->getJson(route('api.v1.posts.index', ['search' => 'vaksinasi']));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_index_caps_page_size_at_50(): void
    {
        $response = $this->getJson(route('api.v1.posts.index', ['per_page' => 500]));

        $response->assertOk()->assertJsonPath('meta.per_page', 50);
    }

    public function test_show_returns_content_and_increments_view_count(): void
    {
        $post = Post::factory()->published()->create(['content' => '<p>Isi lengkap</p>']);

        $response = $this->getJson(route('api.v1.posts.show', $post));

        $response->assertOk()
            ->assertJsonPath('data.content', '<p>Isi lengkap</p>')
            ->assertJsonPath('data.author.name', $post->user->name);

        $this->assertSame(1, $post->fresh()->view_count);
    }

    public function test_show_returns_404_for_draft_post(): void
    {
        $post = Post::factory()->create();

        $this->getJson(route('api.v1.posts.show', $post))->assertNotFound();
    }

    public function test_show_returns_404_for_scheduled_post(): void
    {
        $post = Post::factory()->scheduled()->create();

        $this->getJson(route('api.v1.posts.show', $post))->assertNotFound();
    }
}
