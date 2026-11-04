<?php

namespace Database\Factories;

use App\Enums\MenuItemType;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MenuItem>
 */
class MenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'parent_id' => null,
            'label' => fake()->words(2, true),
            'url' => fake()->url(),
            'type' => MenuItemType::Custom,
            'linkable_id' => null,
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function forPage(Page $page): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => $page->title,
            'type' => MenuItemType::Page,
            'linkable_id' => $page->id,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
