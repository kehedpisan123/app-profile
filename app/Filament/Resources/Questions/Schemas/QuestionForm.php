<?php

namespace App\Filament\Resources\Questions\Schemas;

use App\Enums\QuestionStatus;
use App\Models\Question;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class QuestionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Pertanyaan')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('subject')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('body')
                            ->label('Isi Pertanyaan')
                            ->required()
                            ->rows(8),
                    ]),
                Section::make('Penanya')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->options(QuestionStatus::class)
                            ->disableOptionWhen(fn (string $value): bool => $value === QuestionStatus::Answered->value)
                            ->disabled(fn (?Question $record): bool => $record?->status === QuestionStatus::Answered)
                            ->helperText('Status "Dijawab" diatur otomatis saat ada jawaban yang diterbitkan.')
                            ->required(),
                        Select::make('topic_id')
                            ->label('Topik')
                            ->relationship('topic', 'name')
                            ->preload(),
                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->label('Telepon')
                            ->tel()
                            ->maxLength(30),
                    ]),
            ]);
    }
}
