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
use Illuminate\Support\Str;

class AnswersRelationManager extends RelationManager
{
    protected static string $relationship = 'answers';

    protected static ?string $title = 'Jawaban';

    protected static ?string $modelLabel = 'jawaban';

    protected static ?string $pluralModelLabel = 'jawaban';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                RichEditor::make('body')
                    ->label('Jawaban')
                    ->required()
                    ->toolbarButtons([
                        ['bold', 'italic', 'underline', 'link'],
                        ['bulletList', 'orderedList'],
                        ['undo', 'redo'],
                    ])
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(QuestionAnswerStatus::class)
                    ->default(QuestionAnswerStatus::Draft)
                    ->helperText('Penanya akan menerima email saat jawaban diterbitkan.')
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
                    ->label('Jawaban')
                    ->formatStateUsing(fn (string $state): string => Str::of($state)->stripTags()->squish()->limit(100)->toString())
                    ->wrap(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('user.name')
                    ->label('Petugas')
                    ->placeholder('-'),
                TextColumn::make('answered_at')
                    ->label('Diterbitkan')
                    ->dateTime()
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tulis jawaban')
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
