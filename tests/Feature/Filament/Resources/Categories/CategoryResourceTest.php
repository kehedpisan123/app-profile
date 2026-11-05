<?php

namespace Tests\Feature\Filament\Resources\Categories;

use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Models\Category;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoryResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_edit_modal_loads_the_translation_for_the_selected_locale(): void
    {
        $category = Category::factory()->create([
            'name' => ['id' => 'Berita', 'en' => 'News'],
            'description' => ['id' => 'Kabar terbaru', 'en' => 'Latest updates'],
        ]);

        $this->actingAs(User::factory()->admin()->create());

        Livewire::test(ManageCategories::class)
            ->mountAction(TestAction::make('edit')->table($category))
            ->assertSchemaStateSet([
                'name' => 'Berita',
                'description' => 'Kabar terbaru',
            ])
            ->set('activeLocale', 'en')
            ->assertSchemaStateSet([
                'name' => 'News',
                'description' => 'Latest updates',
            ])
            ->set('activeLocale', 'id')
            ->assertSchemaStateSet([
                'name' => 'Berita',
                'description' => 'Kabar terbaru',
            ]);
    }
}
