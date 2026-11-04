<?php

namespace App\Filament\Resources\Questions\Pages;

use App\Enums\QuestionStatus;
use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Question;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListQuestions extends ListRecords
{
    protected static string $resource = QuestionResource::class;

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('Semua')];

        foreach (QuestionStatus::cases() as $status) {
            $tabs[$status->value] = Tab::make($status->getLabel())
                ->badge(Question::query()->where('status', $status)->count())
                ->badgeColor($status->getColor())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', $status));
        }

        return $tabs;
    }
}
