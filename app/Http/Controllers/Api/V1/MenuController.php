<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\MenuLocation;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MenuResource;
use App\Models\Menu;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuController extends Controller
{
    public function show(MenuLocation $location): MenuResource
    {
        $menu = Menu::query()
            ->active()
            ->where('location', $location)
            ->with([
                'rootItems' => fn (HasMany $query) => $query->active()->with([
                    'linkable',
                    'children' => fn (HasMany $query) => $query->active()->with('linkable'),
                ]),
            ])
            ->oldest('id')
            ->firstOrFail();

        return MenuResource::make($menu);
    }
}
