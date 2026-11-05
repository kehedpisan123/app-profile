<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * List published posts, optionally filtered by category slug, featured flag, or search keyword.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $posts = Post::query()
            ->published()
            ->with(['category', 'user'])
            ->when($request->filled('category'), fn (Builder $query) => $query->whereRelation('category', 'slug', $request->string('category')->toString()))
            ->when($request->has('featured'), fn (Builder $query) => $query->where('is_featured', $request->boolean('featured')))
            ->when($request->filled('search'), function (Builder $query) use ($request): void {
                $query->whereTranslationLike(
                    ['title', 'summary'],
                    $request->string('search')->trim()->toString(),
                );
            })
            ->latest('published_at')
            ->paginate(min(max($request->integer('per_page', 10), 1), 50))
            ->withQueryString();

        return PostResource::collection($posts);
    }

    public function show(Post $post): PostResource
    {
        abort_unless($post->isPublished(), 404);

        $post->incrementQuietly('view_count');

        return PostResource::make($post->load(['category', 'user']));
    }
}
