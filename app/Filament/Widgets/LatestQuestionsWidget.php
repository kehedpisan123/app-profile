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

    protected static ?string $heading = 'Pertanyaan Menunggu Jawaban';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Question::query()
                ->with('topic')
                ->where('status', QuestionStatus::Pending)
                ->latest())
            ->defaultPaginationPageOption(5)
            ->emptyStateHeading('Tidak ada pertanyaan yang menunggu')
            ->columns([
                TextColumn::make('subject')
                    ->label('Judul')
                    ->limit(60),
                TextColumn::make('name')
                    ->label('Penanya'),
                TextColumn::make('topic.name')
                    ->label('Topik')
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->since(),
            ])
            ->recordActions([
                Action::make('answer')
                    ->label('Jawab')
                    ->icon(Heroicon::OutlinedChatBubbleLeftEllipsis)
                    ->url(fn (Question $record): string => QuestionResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
