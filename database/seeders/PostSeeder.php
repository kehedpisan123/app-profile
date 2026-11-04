<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $author = User::query()->where('email', 'admin@example.com')->firstOrFail();

        Category::query()->each(function (Category $category) use ($author): void {
            Post::factory()
                ->count(4)
                ->published()
                ->for($author)
                ->for($category)
                ->create();
        });

        Post::factory()
            ->count(2)
            ->published()
            ->featured()
            ->for($author)
            ->for(Category::query()->where('slug', 'berita')->firstOrFail())
            ->create();

        Post::factory()
            ->count(3)
            ->for($author)
            ->for(Category::query()->inRandomOrder()->firstOrFail())
            ->create();
    }
}
