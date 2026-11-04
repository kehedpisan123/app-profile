<?php

namespace Database\Factories;

use App\Enums\SettingType;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $key = Str::snake(fake()->unique()->words(2, true));

        return [
            'group' => 'general',
            'key' => $key,
            'label' => Str::headline($key),
            'value' => fake()->sentence(3),
            'type' => SettingType::Text,
        ];
    }
}
