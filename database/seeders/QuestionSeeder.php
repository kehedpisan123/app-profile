<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionAnswer;
use App\Models\QuestionTopic;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $officer = User::query()->where('email', 'editor@example.com')->firstOrFail();
        $topics = QuestionTopic::query()->get();

        Question::factory()
            ->count(5)
            ->sequence(fn ($sequence) => ['topic_id' => $topics->random()->id])
            ->create();

        Question::factory()
            ->count(6)
            ->answered()
            ->sequence(fn ($sequence) => ['topic_id' => $topics->random()->id])
            ->has(
                QuestionAnswer::factory()
                    ->published()
                    ->for($officer)
                    ->state(['answered_at' => now()->subDays(fake()->numberBetween(1, 30))]),
                'answers',
            )
            ->create();

        Question::factory()
            ->rejected()
            ->for($topics->random(), 'topic')
            ->create();
    }
}
