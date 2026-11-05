<?php

namespace Tests\Feature\Filament\Resources\Posts;

use App\Enums\PostStatus;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PostResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_title_fills_slug_on_create(): void
    {
        $this->actingAs(User::factory()->create());

        Livewire::test(CreatePost::class)
            ->fillForm(['title' => 'Layanan Online Dibuka'])
            ->assertSchemaStateSet(['slug' => 'layanan-online-dibuka']);
    }

    public function test_publishing_post_without_date_sets_published_at(): void
    {
        $editor = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($editor);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Pengumuman Libur Nasional',
                'slug' => 'pengumuman-libur-nasional',
                'status' => PostStatus::Published->value,
                'category_id' => $category->id,
                'user_id' => $editor->id,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::query()->where('slug', 'pengumuman-libur-nasional')->firstOrFail();

        $this->assertSame(PostStatus::Published, $post->status);
        $this->assertNotNull($post->published_at);
        $this->assertSame('Pengumuman Libur Nasional', $post->getTranslation('title', 'id', false));
    }

    public function test_duplicate_slug_is_rejected(): void
    {
        $editor = User::factory()->create();
        Post::factory()->create(['slug' => 'sudah-ada']);

        $this->actingAs($editor);

        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => 'Judul Baru',
                'slug' => 'sudah-ada',
                'status' => PostStatus::Draft->value,
                'user_id' => $editor->id,
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }

    public function test_edit_form_returns_to_the_saved_locale_and_leaves_the_empty_locale_unset(): void
    {
        $editor = User::factory()->create();
        $post = Post::factory()->create([
            'title' => ['id' => 'Pengumuman Libur'],
            'summary' => ['id' => 'Ringkasan singkat'],
            'content' => ['id' => '<p>Isi pengumuman</p>'],
        ]);

        $this->actingAs($editor);

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->assertSchemaStateSet([
                'title' => 'Pengumuman Libur',
                'summary' => 'Ringkasan singkat',
            ])
            ->set('activeLocale', 'en')
            ->assertSchemaStateSet([
                'title' => null,
                'summary' => null,
            ])
            ->set('activeLocale', 'id')
            ->assertSchemaStateSet([
                'title' => 'Pengumuman Libur',
                'summary' => 'Ringkasan singkat',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $post->refresh();

        $this->assertSame(['id' => 'Pengumuman Libur'], $post->getTranslations('title'));
        $this->assertSame(['id' => 'Ringkasan singkat'], $post->getTranslations('summary'));
        $this->assertSame(['id' => '<p>Isi pengumuman</p>'], $post->getTranslations('content'));
    }
}
