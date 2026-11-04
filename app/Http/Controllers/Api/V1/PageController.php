<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PageStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\Page;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return PageResource::collection(
            Page::query()->published()->orderBy('sort_order')->get(),
        );
    }

    public function show(Page $page): PageResource
    {
        abort_unless($page->status === PageStatus::Published, 404);

        return PageResource::make($page);
    }
}
