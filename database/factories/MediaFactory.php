<?php

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = fake()->unique()->slug(2).'.jpg';

        return [
            'user_id' => User::factory(),
            'file_name' => $fileName,
            'file_path' => 'media/'.$fileName,
            'mime_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(10_000, 2_000_000),
            'alt_text' => fake()->sentence(4),
            'disk' => 'public',
        ];
    }
}
