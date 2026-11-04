<?php

namespace Tests\Feature\Filament;

use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Faqs\FaqResource;
use App\Filament\Resources\Media\MediaResource;
use App\Filament\Resources\Menus\MenuResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Questions\QuestionResource;
use App\Filament\Resources\QuestionTopics\QuestionTopicResource;
use App\Filament\Resources\Settings\SettingResource;
use App\Filament\Resources\Slides\SlideResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\LatestQuestionsWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Models\MenuItem;
use App\Models\Page;
use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_dashboard_renders_for_admin(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin')
            ->assertOk();
    }

    public function test_latest_questions_widget_lists_only_pending_questions(): void
    {
        $pending = Question::factory()->create();
        $answered = Question::factory()->answered()->create();

        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(LatestQuestionsWidget::class)
            ->assertCanSeeTableRecords([$pending])
            ->assertCanNotSeeTableRecords([$answered]);
    }

    public function test_stats_widget_counts_pending_questions(): void
    {
        Question::factory()->count(2)->create();

        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(StatsOverviewWidget::class)
            ->assertSeeInOrder(['Pertanyaan Menunggu', '2']);
    }

    /**
     * @return array<string, array{class-string<\Filament\Resources\Resource>}>
     */
    public static function resources(): array
    {
        return [
            'posts' => [PostResource::class],
            'categories' => [CategoryResource::class],
            'pages' => [PageResource::class],
            'media' => [MediaResource::class],
            'menus' => [MenuResource::class],
            'slides' => [SlideResource::class],
            'questions' => [QuestionResource::class],
            'question topics' => [QuestionTopicResource::class],
            'faqs' => [FaqResource::class],
            'settings' => [SettingResource::class],
            'users' => [UserResource::class],
        ];
    }

    /**
     * @param  class-string<\Filament\Resources\Resource>  $resource
     */
    #[DataProvider('resources')]
    public function test_resource_index_page_renders_for_admin(string $resource): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get($resource::getUrl('index'))
            ->assertOk();
    }

    public function test_question_view_page_renders_with_answers(): void
    {
        $question = Question::factory()->answered()->create(['subject' => 'Cara mengurus izin usaha']);
        QuestionAnswer::factory()->published()->for($question)->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(QuestionResource::getUrl('view', ['record' => $question]))
            ->assertOk()
            ->assertSee('Cara mengurus izin usaha');
    }

    public function test_menu_edit_page_renders_nested_items(): void
    {
        $parent = MenuItem::factory()->create(['label' => 'Profil']);
        MenuItem::factory()->forPage(Page::factory()->create())->create([
            'menu_id' => $parent->menu_id,
            'parent_id' => $parent->id,
            'label' => 'Visi Misi',
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->get(MenuResource::getUrl('edit', ['record' => $parent->menu_id]))
            ->assertOk()
            ->assertSee('Profil')
            ->assertSee('Visi Misi');
    }

    public function test_editor_is_forbidden_from_user_management(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(UserResource::getUrl('index'))
            ->assertForbidden();
    }

    public function test_viewer_is_forbidden_from_creating_posts(): void
    {
        $this->actingAs(User::factory()->viewer()->create())
            ->get(PostResource::getUrl('create'))
            ->assertForbidden();
    }
}
