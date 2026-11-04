<?php

namespace App\Filament\Widgets;

use App\Enums\QuestionStatus;
use App\Filament\Resources\Questions\QuestionResource;
use App\Models\Question;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestQuestionsWidget extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string
    {
        return __('Questions Awaiting Answers');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Question::query()
                ->with('topic')
                ->where('status', QuestionStatus::Pending)
                ->latest())
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading(__('No questions are waiting'))
            ->columns([
                TextColumn::make('subject')
                    ->label(__('Title'))
                    ->limit(60),
                TextColumn::make('name')
                    ->label(__('Asker')),
                TextColumn::make('topic.name')
                    ->label(__('Topic'))
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label(__('Submitted'))
                    ->since(),
            ])
            ->recordActions([
                Action::make('answer')
                    ->label(__('Reply'))
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->url(fn (Question $record): string => QuestionResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
