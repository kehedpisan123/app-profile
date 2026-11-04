<?php

namespace Database\Factories;

use App\Enums\QuestionStatus;
use App\Models\Question;
use App\Models\QuestionTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'topic_id' => QuestionTopic::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->numerify('08##########'),
            'subject' => rtrim(fake()->sentence(6), '.').'?',
            'body' => fake()->paragraphs(2, true),
            'status' => QuestionStatus::Pending,
            'ip_address' => fake()->ipv4(),
        ];
    }

    public function answered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuestionStatus::Answered,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuestionStatus::Rejected,
        ]);
    }
}
