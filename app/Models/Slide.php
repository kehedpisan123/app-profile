<?php

namespace App\Models;

use Database\Factories\SlideFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[Fillable(['title', 'description', 'image', 'link_url', 'sort_order', 'is_active', 'start_date', 'end_date'])]
class Slide extends Model
{
    /** @use HasFactory<SlideFactory> */
    use HasFactory;

    use HasTranslations;

    /** @var array<int, string> */
    public array $translatable = ['title', 'description'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Scope a query to slides that are active and within their display schedule.
     *
     * @param  Builder<Slide>  $query
     */
    #[Scope]
    protected function showing(Builder $query): void
    {
        $today = today();

        $query->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('start_date')->orWhereDate('start_date', '<=', $today))
            ->where(fn (Builder $query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', $today));
    }
}
