<?php

namespace App\Http\Resources\Api\V1;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MenuItem
 */
class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * The `slug` identifies the linked page, post, or category so the client can route to it,
     * while `url` is only filled for custom links.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'type' => $this->type->value,
            'url' => $this->url,
            'slug' => $this->whenLoaded('linkable', fn (): ?string => $this->linkable?->getAttribute('slug')),
            'sort_order' => $this->sort_order,
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
