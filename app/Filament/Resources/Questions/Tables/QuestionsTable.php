<?php

namespace App\Filament\Resources\Questions\Tables;

use App\Filament\Resources\Questions\Actions\RejectQuestionAction;
use App\Models\Question;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuestionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('subject')
                    ->label('Judul')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('name')
                    ->label('Penanya')
                    ->searchable()
                    ->description(fn (Question $record): string => $record->email),
                TextColumn::make('topic.name')
                    ->label('Topik')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('answers_count')
                    ->label('Jawaban')
                    ->counts('answers'),
                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('topic')
                    ->label('Topik')
                    ->relationship('topic', 'name')
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    EditAction::make(),
                    RejectQuestionAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
