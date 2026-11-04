<?php

namespace App\Filament\Resources\Questions\RelationManagers;

use App\Enums\QuestionAnswerStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('Answers');
    }

    protected static function getModelLabel(): ?string
    {
        return __('answer');
    }

    protected static function getPluralModelLabel(): ?string
    {
        return __('answers');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('body')
                    ->label(__('Answer'))
                    ->required()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'link'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('Status'))
                    ->options(QuestionAnswerStatus::class)
                    ->default(QuestionAnswerStatus::Draft)
                    ->helperText(__('The asker will receive an email when the answer is published.'))
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('body')
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('body')
                    ->label(__('Answer'))
                    ->formatStateUsing(fn (string $state): string => Str::of($state)->stripTags()->squish()->limit(100)->toString())
                    ->wrap(),
                TextColumn::make('status')
                    ->label(__('Status'))
                    ->badge(),
                TextColumn::make('user.name')
                    ->label(__('Officer'))
                    ->placeholder('-'),
                TextColumn::make('answered_at')
                    ->label(__('Published On'))
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('Write an answer'))
                    ->mutateDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
