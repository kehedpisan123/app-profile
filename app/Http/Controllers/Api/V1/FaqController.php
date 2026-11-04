<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FaqResource;
use App\Models\Faq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FaqController extends Controller
{
    /**
     * List active FAQs, optionally filtered by topic slug.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return FaqResource::collection(
            Faq::query()
                ->active()
                ->with('topic')
                ->when($request->filled('topic'), fn (Builder $query) => $query->whereRelation('topic', 'slug', $request->string('topic')->toString()))
                ->orderBy('sort_order')
                ->get(),
        );
    }
}
